<?php

namespace App\Http\Controllers\Admin;

use App\Exports\NoteVouchersSampleExport;
use App\Http\Controllers\Controller;
use App\Models\NoteVoucher;
use App\Models\NoteVoucherType;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NoteVouchersImport;
use App\Exports\ProductsSampleExport;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel as ExcelWriter;

class NoteVoucherController extends Controller
{



    public function index()
    {

        $data = NoteVoucher::get();

        return view('admin.noteVouchers.index', ['data' => $data]);
    }

    public function create(Request $request)
    {
        if (auth()->user()->can('noteVoucher-add')) {

            $note_voucher_type_id = $request->query('id');
            $warehouses = Warehouse::get();
            $providers =    Provider::get();
            $note_voucher_type = NoteVoucherType::findOrFail($note_voucher_type_id);

            return view('admin.noteVouchers.create', compact('providers', 'note_voucher_type_id', 'warehouses', 'note_voucher_type'));
        } else {
            return redirect()->back()
                ->with('error', "Access Denied");
        }
    }



   public function store(Request $request)
    {
        $lastNoteVoucher = NoteVoucher::orderBy('id', 'desc')->first();
        $newNumber = $lastNoteVoucher ? $lastNoteVoucher->id + 1 : 1;

        // Get note voucher type to check if it's receipt (in_out_type = 1)
        $noteVoucherType = NoteVoucherType::findOrFail($request['note_voucher_type_id']);

        // Create the note voucher
        $noteVoucherData = [
            'note_voucher_type_id' => $request['note_voucher_type_id'],
            'date_note_voucher' => $request['date_note_voucher'],
            'number' => $newNumber,
            'note' => $request['note'],
        ];

        // For receipt type (in_out_type = 1), provider is from and warehouse is to
        if ($noteVoucherType->in_out_type == 1) {
            $noteVoucherData['provider_id'] = $request['provider_id'];
            $noteVoucherData['to_warehouse_id'] = $request['toWarehouse'];
        } elseif ($noteVoucherType->in_out_type == 2) {
            // For outgoing type (in_out_type = 2), warehouse is from and provider is to
            $noteVoucherData['from_warehouse_id'] = $request['fromWarehouse'];
            $noteVoucherData['provider_id'] = $request['provider_id'];
        } else {
            // For transfer type (in_out_type = 3), warehouse is from and warehouse is to
            $noteVoucherData['from_warehouse_id'] = $request['fromWarehouse'];
            $noteVoucherData['to_warehouse_id'] = $request['toWarehouse'];
        }

        $noteVoucher = NoteVoucher::create($noteVoucherData);

        // Save the products and update quantities
        if ($request->has('products')) {
            foreach ($request['products'] as $productData) {
                // Skip empty rows
                if (empty($productData['quantity'])) {
                    continue;
                }

                // Get product either by ID (if provided) or by name
                $product = null;
                if (!empty($productData['product_id'])) {
                    $product = Product::find($productData['product_id']);
                } else if (!empty($productData['name'])) {
                    $product = Product::where('name_ar', $productData['name'])->first();
                }

                // Skip if product not found
                if (!$product) {
                    continue;
                }

                // Create voucher product record using hasMany relationship
                $noteVoucher->voucherProducts()->create([
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'purchasing_price' => $productData['purchasing_price'] ?? null,
                    'note' => $productData['note'] ?? null,
                ]);
            }
        }

        if ($request->input('redirect_to') == 'show') {
            return redirect()->route('noteVouchers.show', $noteVoucher->id)->with('success', 'Note Voucher created successfully!');
        } else {
            return redirect()->route('noteVouchers.index')->with('success', 'Note Voucher created successfully!');
        }
    }




    public function show($id)
    {
        $noteVoucher = NoteVoucher::with([
            'fromWarehouse',
            'toWarehouse',
            'provider',
            'voucherProducts',
            'noteVoucherType' // Include the related noteVoucherType
        ])->findOrFail($id);

        return view('admin.noteVouchers.show', compact('noteVoucher'));
    }

    public function edit($id)
    {
        $noteVoucher = NoteVoucher::with('noteVoucherType', 'voucherProducts.product', 'provider', 'fromWarehouse', 'toWarehouse')->findOrFail($id);

        // Pass only the note voucher to the view (search-select will handle data loading)
        return view('admin.noteVouchers.edit', compact('noteVoucher'));
    }


    public function update(Request $request, $id)
    {
        $noteVoucher = NoteVoucher::findOrFail($id);
        $noteVoucherType = NoteVoucherType::findOrFail($request['note_voucher_type_id']);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Build update data based on note voucher type
            $updateData = [
                'note_voucher_type_id' => $request['note_voucher_type_id'],
                'date_note_voucher' => $request['date_note_voucher'],
                'note' => $request['note'],
            ];

            // For receipt type (in_out_type = 1), provider is from and warehouse is to
            if ($noteVoucherType->in_out_type == 1) {
                $updateData['provider_id'] = $request['provider_id'];
                $updateData['to_warehouse_id'] = $request['toWarehouse'];
                $updateData['from_warehouse_id'] = null; // Clear from_warehouse for receipt type
            } elseif ($noteVoucherType->in_out_type == 2) {
                // For outgoing type (in_out_type = 2), warehouse is from and provider is to
                $updateData['from_warehouse_id'] = $request['fromWarehouse'];
                $updateData['provider_id'] = $request['provider_id'];
                $updateData['to_warehouse_id'] = null; // Clear to_warehouse for outgoing type
            } else {
                // For transfer type (in_out_type = 3), warehouse is from and warehouse is to
                $updateData['from_warehouse_id'] = $request['fromWarehouse'];
                $updateData['to_warehouse_id'] = $request['toWarehouse'];
                $updateData['provider_id'] = null; // Clear provider_id for transfer type
            }

            $noteVoucher->update($updateData);

            // Delete all existing voucher products for this note voucher
            $noteVoucher->voucherProducts()->delete();

            // Create new voucher products based on the request
            if ($request->has('products')) {
                foreach ($request['products'] as $productData) {
                    // Skip empty rows
                    if (empty($productData['quantity'])) {
                        continue;
                    }

                    // Get product either by ID (if provided) or by name
                    if (!empty($productData['product_id'])) {
                        $product = Product::findOrFail($productData['product_id']);
                    } else if (!empty($productData['name'])) {
                        $product = Product::where('name_ar', $productData['name'])->firstOrFail();
                    } else {
                        continue;
                    }

                    // Create new voucher product record
                    $noteVoucher->voucherProducts()->create([
                        'product_id' => $product->id,
                        'quantity' => $productData['quantity'],
                        'purchasing_price' => $productData['purchasing_price'] ?? null,
                        'note' => $productData['note'] ?? null,
                    ]);
                }
            }

            DB::commit();

            if ($request->input('redirect_to') == 'show') {
                return redirect()->route('noteVouchers.show', $noteVoucher->id)->with('success', 'Note Voucher updated successfully!');
            } else {
                return redirect()->route('noteVouchers.index')->with('success', 'Note Voucher updated successfully!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function destroy($id)
    {
        try {
            $noteVoucher = NoteVoucher::findOrFail($id);



            // Delete the category
            if ($noteVoucher->delete()) {
                return redirect()->back()->with(['success' => 'noteVoucher deleted successfully']);
            } else {
                return redirect()->back()->with(['error' => 'Something went wrong']);
            }
        } catch (\Exception $ex) {
            return redirect()->back()->with(['error' => 'Something went wrong: ' . $ex->getMessage()]);
        }
    }
}
