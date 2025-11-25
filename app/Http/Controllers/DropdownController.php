<?php

namespace App\Http\Controllers;

use App\Models\Dropdowns;
use App\Models\DropdownList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DropdownController extends Controller
{
    public function dropdown()
    {
        $items = Dropdowns::all();
        $values = DropdownList::join('dropdowns', 'dropdown_lists.dropdown_id', '=', 'dropdowns.id')
            ->select('dropdown_lists.*', 'dropdowns.dropdowns as category_name')
            ->get();

        return view('admin.dropdown_list', compact('items', 'values'));
    }


    public function viewList()
    {
        $items = Dropdowns::all(); // dropdown categories
        return view('admin.dropdown_list', compact('items'));
    }


    public function showList(Request $request)
    {
        $request->validate([
            'dropdown_id' => 'required|exists:dropdowns,id',
        ]);

        $selectedId = $request->dropdown_id;
        $items = Dropdowns::all(); // For dropdown list
        if ($selectedId == 0) {
            $values = DB::table('dropdown_lists')
                ->join('dropdowns', 'dropdown_lists.dropdown_id', '=', 'dropdowns.id')
                ->select(
                    'dropdown_lists.id',
                    'dropdown_lists.value',
                    'dropdowns.dropdowns as category_name'
                )
                ->get();
        } else {
            $values = DB::table('dropdown_lists')
                ->join('dropdowns', 'dropdown_lists.dropdown_id', '=', 'dropdowns.id')
                ->where('dropdown_lists.dropdown_id', $selectedId)
                ->select(
                    'dropdown_lists.id',
                    'dropdown_lists.value',
                    'dropdowns.dropdowns as category_name'
                )
                ->get();
        }

        return view('admin.dropdown_list', compact('items', 'values', 'selectedId'));
    }



    public function storelist(Request $request)
    {
        $request->validate([
            'dropdown_id' => 'required|exists:dropdowns,id',
            'value' => 'required|string|max:255',
        ]);

        DropdownList::create([
            'dropdown_id' => $request->dropdown_id,
            'value' => $request->value,
        ]);

        return redirect()->route('dropdown_list')->with('success', 'Dropdown Added Successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'value' => 'required|string|max:255',
        ]);

        $dropdown = DropdownList::findOrFail($id);
        $dropdown->value = $request->value;
        $dropdown->save();

        return redirect()->route('dropdown_list')->with('success', 'Dropdown Updated Successfully!');
    }

    // public function create()
    // {
    //     return view('dropdown.create');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'dropdowns' => 'required|string|max:255'
    //     ]);

    //     dropdowns::create([
    //         'dropdowns' => $request->dropdowns
    //     ]);

    //     return redirect()->back()->with('success', 'Dropdown item added!');
    // }
    // public function createlist()
    // {
    //     $dropdowns = dropdowns::all();
    //     return view('dropdown_list.create', compact('dropdowns'));
    // }

}
