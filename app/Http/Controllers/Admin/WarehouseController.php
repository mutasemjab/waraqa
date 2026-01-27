<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:warehouse-table')->only(['index']);
        $this->middleware('permission:warehouse-add')->only(['create', 'store']);
        $this->middleware('permission:warehouse-edit')->only(['edit', 'update']);
        $this->middleware('permission:warehouse-delete')->only(['destroy']);
    }

    public function index()
    {

        $data = Warehouse::paginate(PAGINATION_COUNT);

        return view('admin.warehouses.index', ['data' => $data]);
    }

    public function create()
    {
        if (auth()->user()->can('warehouse-add')) {
         
            return view('admin.warehouses.create');
        } else {
            return redirect()->back()
                ->with('error', "Access Denied");
        }
    }



    public function store(Request $request)
    {

        try {
            $warehouse = new Warehouse();

            $warehouse->name = $request->get('name');


            if ($warehouse->save()) {

                return redirect()->route('warehouses.index')->with(['success' => 'warehouse created']);
            } else {
                return redirect()->back()->with(['error' => 'Something wrong']);
            }
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        if (auth()->user()->can('warehouse-edit')) {
            $data = Warehouse::findorFail($id);
     
            return view('admin.warehouses.edit', compact('data'));
        } else {
            return redirect()->back()
                ->with('error', "Access Denied");
        }
    }

    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::findorFail($id);
        try {

            $warehouse->name = $request->get('name');


            if ($warehouse->save()) {

                return redirect()->route('warehouses.index')->with(['success' => 'warehouse update']);
            } else {
                return redirect()->back()->with(['error' => 'Something wrong']);
            }
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);



            // Delete the category
            if ($warehouse->delete()) {
                return redirect()->back()->with(['success' => 'warehouse deleted successfully']);
            } else {
                return redirect()->back()->with(['error' => 'Something went wrong']);
            }
        } catch (\Exception $ex) {
            return redirect()->back()->with(['error' => 'Something went wrong: ' . $ex->getMessage()]);
        }
    }
    public function show($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $movements = $warehouse->movements()->paginate(10);

        return view('admin.warehouses.show', compact('warehouse', 'movements'));
    }

    public function quantities($id)
    {
        if (!auth()->user()->can('warehouse-table')) {
            return redirect()->back()->with('error', "Access Denied");
        }

        $warehouse = Warehouse::findOrFail($id);
        $locale = \Illuminate\Support\Facades\App::getLocale();
        $nameColumn = $locale === 'ar' ? 'name_ar' : 'name_en';

        // Get all products with their quantities in this warehouse
        $products = \Illuminate\Support\Facades\DB::table('voucher_products')
            ->join('note_vouchers', 'voucher_products.note_voucher_id', '=', 'note_vouchers.id')
            ->join('note_voucher_types', 'note_vouchers.note_voucher_type_id', '=', 'note_voucher_types.id')
            ->join('products', 'voucher_products.product_id', '=', 'products.id')
            ->select(
                'voucher_products.product_id',
                \Illuminate\Support\Facades\DB::raw('products.' . $nameColumn . ' as product_name'),
                \Illuminate\Support\Facades\DB::raw('SUM(CASE
                    WHEN (note_voucher_types.in_out_type = 1 AND note_vouchers.to_warehouse_id = ' . $warehouse->id . ')
                    OR (note_voucher_types.in_out_type = 3 AND note_vouchers.to_warehouse_id = ' . $warehouse->id . ')
                    THEN voucher_products.quantity
                    ELSE 0
                END) as input_quantity'),
                \Illuminate\Support\Facades\DB::raw('SUM(CASE
                    WHEN note_voucher_types.in_out_type IN (2, 3) AND note_vouchers.from_warehouse_id = ' . $warehouse->id . '
                    THEN voucher_products.quantity
                    ELSE 0
                END) as output_quantity')
            )
            ->where(function($query) use ($warehouse) {
                $query->where(function($q) use ($warehouse) {
                    $q->where('note_voucher_types.in_out_type', 1)
                      ->where('note_vouchers.to_warehouse_id', $warehouse->id);
                })
                ->orWhere(function($q) use ($warehouse) {
                    $q->where('note_voucher_types.in_out_type', 3)
                      ->where('note_vouchers.to_warehouse_id', $warehouse->id);
                })
                ->orWhere(function($q) use ($warehouse) {
                    $q->whereIn('note_voucher_types.in_out_type', [2, 3])
                      ->where('note_vouchers.from_warehouse_id', $warehouse->id);
                });
            })
            ->groupBy('voucher_products.product_id', 'products.' . $nameColumn)
            ->get();

        return view('admin.warehouses.quantities', compact('warehouse', 'products'));
    }

}
