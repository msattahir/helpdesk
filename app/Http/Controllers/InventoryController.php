<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class InventoryController extends Controller
{
    public $label = 'Inventory Item';

    public function index(Request $request){
        if($request->ajax()){
            $data = Item::totalByStatus('Allocated')
            ->totalByStatus('Configured')
            ->totalByStatus('Installed')
            ->totalByStatus('Distributed')
            ->totalByStatus('Returned')
            ->orderBy('name')
            ->orderBy('model');

            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
        }

        return view('inventory', [
            'label_p' => 'Inventory',
            'label_s' => 'Inventory',
            'base_url_name' => 'inventory'
        ]);
    }

    public static function rules(Request $request){

        return [
            'item_id'  => 'required|exists:items,id',
            'quantity'  => 'numeric'
        ];
    }

    public static function rules_messages(){
        return [
            'item_id.exists' => 'Item not recognized'
        ];
    }

    public function store(Request $request){
        $form_data = $request->validate(
            self::rules($request),
            self::rules_messages()
        );

        $form_data['staff_id'] = auth()->id();

        Inventory::create($form_data);
        return response()->json([
            'status' => 'success',
            'message' => $this->label.' added successfully'
        ]);
    }

    public function update(Request $request, $id){
        $form_data = $request->validate(
            self::rules($request),
            self::rules_messages()
        );
        $valid_until = date("Y-m-d H:i:s");
        $form_data['staff_id'] = auth()->id();
        $form_data['alter_staff_id'] = auth()->id();

        $update = soft_update(Inventory::class, $request->id, $form_data, auth()->id(), $valid_until);
        return response()->json([
            'status' => 'success',
            'message' => $this->label.' updated successfully'
        ]);
    }

    public function show(Request $request, $id){
        if($request->ajax())
        {
            $data = Inventory::whereNull('valid_until')
            ->where('item_id', $id)
            ->orderBy('created_at', 'DESC')
            ->with([
                'item' => function ($query) {
                    $query->select('id', 'name', 'model', 'category');
                },
                'staff' => function ($query) {
                    $query->select('id', 'staff_no', 'name');
                }
            ])
            ->select('id', 'item_id', 'staff_id', 'quantity', 'created_at');


            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
        }

        return view('inventory', [
            'label_p' => "{$this->label} Details",
            'label_s' => "Inventory",
            'base_url_name' => 'inventory'
        ]);
    }

    public function destroy($id){
        try {
            $valid_until = date("Y-m-d H:i:s");

            $del = soft_delete(Inventory::class, $id, auth()->id(), $valid_until);

            return response()->json(['status' => 'success', 'message' => $this->label.' deleted successfully']);
        } catch (QueryException $e) {
            return delete_catch_error($e, $this->label, ['inventory_id'], $id);
        }
    }
}
