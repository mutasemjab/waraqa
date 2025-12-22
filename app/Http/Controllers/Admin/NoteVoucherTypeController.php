<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NoteVoucherType;
use Illuminate\Http\Request;

class NoteVoucherTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:noteVoucherType-table')->only(['index']);
        $this->middleware('permission:noteVoucherType-add')->only(['create', 'store']);
        $this->middleware('permission:noteVoucherType-edit')->only(['edit', 'update']);
        $this->middleware('permission:noteVoucherType-delete')->only(['destroy']);
    }

    public function index()
    {

        $data = NoteVoucherType::paginate(PAGINATION_COUNT);

        return view('admin.noteVoucherTypes.index', ['data' => $data]);
    }

    public function create()
    {
        if (auth()->user()->can('noteVoucherType-add')) {

            return view('admin.noteVoucherTypes.create');
        } else {
            return redirect()->back()
                ->with('error', "Access Denied");
        }
    }



    public function store(Request $request)
    {

        try {
            $noteVoucherType = new NoteVoucherType();

            $noteVoucherType->number = $request->get('number');
            $noteVoucherType->name = $request->get('name');
            $noteVoucherType->name_en = $request->get('name_en');
            $noteVoucherType->in_out_type = $request->get('in_out_type');
            $noteVoucherType->have_price = $request->get('have_price');
            $noteVoucherType->header = $request->get('header');
            $noteVoucherType->footer = $request->get('footer');


            if ($noteVoucherType->save()) {

                return redirect()->route('noteVoucherTypes.index')->with(['success' => 'noteVoucherType created']);
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
        if (auth()->user()->can('noteVoucherType-edit')) {
            $data = NoteVoucherType::findorFail($id);
            return view('admin.noteVoucherTypes.edit', compact('data'));
        } else {
            return redirect()->back()
                ->with('error', "Access Denied");
        }
    }

    public function update(Request $request, $id)
    {
        $noteVoucherType = NoteVoucherType::findorFail($id);
        try {

            $noteVoucherType->name = $request->get('name');
            $noteVoucherType->name_en = $request->get('name_en');
            $noteVoucherType->in_out_type = $request->get('in_out_type');
            $noteVoucherType->have_price = $request->get('have_price');
            $noteVoucherType->header = $request->get('header');
            $noteVoucherType->footer = $request->get('footer');


            if ($noteVoucherType->save()) {

                return redirect()->route('noteVoucherTypes.index')->with(['success' => 'noteVoucherType update']);
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
            $noteVoucherType = NoteVoucherType::findOrFail($id);



            // Delete the category
            if ($noteVoucherType->delete()) {
                return redirect()->back()->with(['success' => 'noteVoucherType deleted successfully']);
            } else {
                return redirect()->back()->with(['error' => 'Something went wrong']);
            }
        } catch (\Exception $ex) {
            return redirect()->back()->with(['error' => 'Something went wrong: ' . $ex->getMessage()]);
        }
    }
}
