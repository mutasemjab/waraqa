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
    public function __construct()
    {
        $this->middleware('permission:noteVoucher-table')->only(['index']);
        $this->middleware('permission:noteVoucher-add')->only(['create', 'store']);
        $this->middleware('permission:noteVoucher-edit')->only(['edit', 'update']);
        $this->middleware('permission:noteVoucher-delete')->only(['destroy']);
    }

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
        // Validation
        $request->validate([
            'note_voucher_type_id' => 'required|exists:note_voucher_types,id',
            'date_note_voucher' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:0',
            // Ensure product is identified either by ID or name
            'products.*.product_id' => 'nullable|required_without:products.*.name|exists:products,id',
        ]);

        $noteVoucherType = NoteVoucherType::findOrFail($request->note_voucher_type_id);
        
        $rules = [];
        if ($noteVoucherType->in_out_type == 1) { // Receipt
             $rules['toWarehouse'] = 'required|exists:warehouses,id';
             
             $recipientType = $request->recipient_type ?? 'provider';
             if ($recipientType === 'provider') {
                 $rules['provider_id'] = 'required|exists:providers,id';
             } elseif (in_array($recipientType, ['seller', 'user'])) {
                 $rules['user_id'] = 'required|exists:users,id';
             } elseif ($recipientType === 'event') {
                 $rules['event_id'] = 'required|exists:events,id';
             }
        } elseif ($noteVoucherType->in_out_type == 2) { // Sending
             $rules['fromWarehouse'] = 'required|exists:warehouses,id';
             
             $recipientType = $request->recipient_type ?? 'provider';
             if ($recipientType === 'provider') {
                 $rules['provider_id'] = 'required|exists:providers,id';
             } elseif (in_array($recipientType, ['seller', 'user'])) {
                 $rules['user_id'] = 'required|exists:users,id';
             } elseif ($recipientType === 'event') {
                 $rules['event_id'] = 'required|exists:events,id';
             }
        } elseif ($noteVoucherType->in_out_type == 3) { // Transfer
             $rules['fromWarehouse'] = 'required|exists:warehouses,id';
             $rules['toWarehouse'] = 'required|exists:warehouses,id|different:fromWarehouse';
        }
        
        if (!empty($rules)) {
            $request->validate($rules);
        }

        $lastNoteVoucher = NoteVoucher::orderBy('id', 'desc')->first();
        $newNumber = $lastNoteVoucher ? $lastNoteVoucher->id + 1 : 1;

        // Get note voucher type to check if it's receipt (in_out_type = 1)
        // $noteVoucherType = NoteVoucherType::findOrFail($request['note_voucher_type_id']); // Already fetched above

        // Create the note voucher
        $noteVoucherData = [
            'note_voucher_type_id' => $request['note_voucher_type_id'],
            'date_note_voucher' => $request['date_note_voucher'],
            'number' => $newNumber,
            'note' => $request['note'],
        ];

        // For receipt type (in_out_type = 1), recipient can be provider, user (seller), or event
        if ($noteVoucherType->in_out_type == 1) {
            $noteVoucherData['to_warehouse_id'] = $request['toWarehouse'];

            // Handle different recipient types
            $recipientType = $request['recipient_type'] ?? 'provider';
            if ($recipientType === 'provider' && $request->has('provider_id')) {
                $noteVoucherData['provider_id'] = $request['provider_id'];
            } elseif (($recipientType === 'seller' || $recipientType === 'user') && $request->has('user_id')) {
                $noteVoucherData['user_id'] = $request['user_id'];
            } elseif ($recipientType === 'event' && $request->has('event_id')) {
                $noteVoucherData['event_id'] = $request['event_id'];
            }
        } elseif ($noteVoucherType->in_out_type == 2) {
            // For outgoing type (in_out_type = 2), warehouse is from and recipient can be provider, user (seller), or event
            $noteVoucherData['from_warehouse_id'] = $request['fromWarehouse'];

            // Handle different recipient types
            $recipientType = $request['recipient_type'] ?? 'provider';
            if ($recipientType === 'provider' && $request->has('provider_id')) {
                $noteVoucherData['provider_id'] = $request['provider_id'];
            } elseif (($recipientType === 'seller' || $recipientType === 'user') && $request->has('user_id')) {
                $noteVoucherData['user_id'] = $request['user_id'];
            } elseif ($recipientType === 'event' && $request->has('event_id')) {
                $noteVoucherData['event_id'] = $request['event_id'];
            }
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
                    'purchasing_price' => $productData['price'],
                    'tax_percentage' => $productData['tax'],
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
            'user',
            'event',
            'voucherProducts',
            'noteVoucherType' // Include the related noteVoucherType
        ])->findOrFail($id);

        return view('admin.noteVouchers.show', compact('noteVoucher'));
    }

    public function edit($id)
    {
        $noteVoucher = NoteVoucher::with('noteVoucherType', 'voucherProducts.product', 'provider', 'user', 'event', 'fromWarehouse', 'toWarehouse')->findOrFail($id);

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

            // For receipt type (in_out_type = 1), recipient can be provider, user (seller), or event
            if ($noteVoucherType->in_out_type == 1) {
                $updateData['to_warehouse_id'] = $request['toWarehouse'];
                $updateData['from_warehouse_id'] = null;

                // Clear all recipient types first
                $updateData['provider_id'] = null;
                $updateData['user_id'] = null;
                $updateData['event_id'] = null;

                // Handle different recipient types
                $recipientType = $request['recipient_type'] ?? 'provider';
                if ($recipientType === 'provider' && $request->has('provider_id')) {
                    $updateData['provider_id'] = $request['provider_id'];
                } elseif (($recipientType === 'seller' || $recipientType === 'user') && $request->has('user_id')) {
                    $updateData['user_id'] = $request['user_id'];
                } elseif ($recipientType === 'event' && $request->has('event_id')) {
                    $updateData['event_id'] = $request['event_id'];
                }
            } elseif ($noteVoucherType->in_out_type == 2) {
                // For outgoing type (in_out_type = 2), warehouse is from and recipient can be provider, user (seller), or event
                $updateData['from_warehouse_id'] = $request['fromWarehouse'];
                $updateData['to_warehouse_id'] = null;

                // Clear all recipient types first
                $updateData['provider_id'] = null;
                $updateData['user_id'] = null;
                $updateData['event_id'] = null;

                // Handle different recipient types
                $recipientType = $request['recipient_type'] ?? 'provider';
                if ($recipientType === 'provider' && $request->has('provider_id')) {
                    $updateData['provider_id'] = $request['provider_id'];
                } elseif (($recipientType === 'seller' || $recipientType === 'user') && $request->has('user_id')) {
                    $updateData['user_id'] = $request['user_id'];
                } elseif ($recipientType === 'event' && $request->has('event_id')) {
                    $updateData['event_id'] = $request['event_id'];
                }
            } else {
                // For transfer type (in_out_type = 3), warehouse is from and warehouse is to
                $updateData['from_warehouse_id'] = $request['fromWarehouse'];
                $updateData['to_warehouse_id'] = $request['toWarehouse'];
                $updateData['provider_id'] = null;
                $updateData['user_id'] = null;
                $updateData['event_id'] = null;
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
                        'purchasing_price' => $productData['price'],
                        'tax_percentage' => $productData['tax'],
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
