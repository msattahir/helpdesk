<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Staff;
use App\Models\Office;
use App\Models\ItemRequest;
use App\Models\ItemDistribution;
use App\Models\DistributionItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class ItemDistributionController extends Controller
{
    public $label = 'Item Distribution';

    public function index(Request $request){
        if($request->ajax()){

            $staff_condition = function ($query) {};
            $ddd_condition = function ($query) {};

            $role = auth()->user()->role;
            if($role == "DDD Admin"){
                $ddd_id = auth()->user()->ddd_id;

                $ddd_condition = function ($query) use ($ddd_id) {
                    $query->where('id', $ddd_id);
                };
            } elseif($role == "Floor Admin"){
                $floor = auth()->user()->ddd->floor;

                $ddd_condition = function ($query) use ($floor) {
                    $query->where('floor', $floor);
                };
            } elseif($role == "Staff") {
                $staff_id = auth()->id();

                $staff_condition = function ($query) use ($staff_id) {
                    $query->whereHas('staff', function ($query) use ($staff_id) {
                        $query->where('id', $staff_id);
                    });
                };
            }

            $data = ItemDistribution::with([
                'distributionable.ddd',
                'distributionable.location',
                'distribution_item.item'
            ])
            // ->where($staff_condition)
            // ->whereHas('ddd', $ddd_condition)
            ->latest();

            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
        }

        return view('item-distributions');
    }

    public static function rules(Request $request){
        return [
            'item_id'  => 'nullable|exists:items,id',
            'reference_no'  => 'nullable|max:255',
            'distribution_item_id'  => 'nullable|exists:distribution_items,id',
            'remark'  => 'nullable|max:200',
            'status'  => Rule::in(get_distribution_status_options("validate")),
            'time'  => 'required|date_format:Y-m-d\TH:i'
        ];
    }

    public static function rules_messages(){
        return [
            'item_id.exists' => 'Item not recognized'
        ];
    }

    public static function insufficient_balance(Request $request, $id = 0){
        if($request->status == "Dismissed"){
            return false;
        }

        if($id > 0){
            $old_quantity = 1;
        }else{
            $old_quantity = 0;
        }

        if($request->item_condition == "New"){
            $inventory_balance = Item::findOrFail($request->item_id)->inventory_balance;
        }else{
            $distribution_item = DistributionItem::findOrFail($request->distribution_item_id);
            $inventory_balance = Item::totalByStatus('Returned')
                ->findOrFail($distribution_item->item_id)
                ->returned;
        }

        if($inventory_balance + $old_quantity < 1){
            return true;
        }

        return false;
    }

    public function store(Request $request){
        $form_data = $request->validate(
            self::rules($request),
            self::rules_messages()
        );

        if(self::insufficient_balance($request)){
            return response()->json([
                'status' => 'danger',
                'message' => 'Insufficient Quantity'
            ]);
        }

        $timestamp = date("Y-m-d H:i:s");
        $form_data['authorize_staff_id'] = auth()->id();
        $form_data['valid_from'] = $timestamp;

        if(!$request->has('distribution_item_id')){
            $distribution_item = DistributionItem::create($form_data);
            $form_data['distribution_item_id'] = $distribution_item->id;
        }

        $model_type = $request->distribute_to;
        $model_id = $request->model_id;
        if($model_type === "Staff"){
            $model = Staff::find($model_id);
        }else{
            $model = Office::find($model_id);
        }

        if($model){
            $distribution = $model->distributions()->create($form_data);
        }

        if($distribution){
            return response()->json([
                'status' => 'success',
                'message' => $this->label.' submitted successfully'
            ]);
        }else{
            return response()->json([
                'status' => 'danger',
                'message' => 'Failed to submit '. $this->label
            ]);
        }
    }

    public function update(Request $request, $id){
        if($request->operation == "return"){
            $item_distribution = ItemDistribution::find($id);
            $request->distribution_item_id = $item_distribution->distribution_item_id;
            $form_data = [
                'status' => 'Returned'
            ];
        } else {
            $form_data = $request->validate(
                self::rules($request),
                self::rules_messages()
            );
            $form_data['distributionable_id'] = $request->model_id;
            $form_data['distributionable_type'] = 'App\\Models\\' . $request->distribute_to;
        }

        if(self::insufficient_balance($request, $id)){
            return response()->json([
                'status' => 'danger',
                'message' => 'Insufficient Quantity'
            ]);
        }

        if (record_changed(ItemDistribution::class, $id, $form_data)) {
            $timestamp = date("Y-m-d H:i:s");
            $form_data['alter_staff_id'] = auth()->id();
            $update = soft_update(
                ItemDistribution::class,
                $id,
                $form_data,
                auth()->id(),
                $timestamp
            );
        }


        $distribution_item_id = ItemDistribution::find($id)->distribution_item_id;
        DistributionItem::find($distribution_item_id)->update($form_data);

        return response()->json([
            'status' => 'success',
            'message' => $this->label.' updated successfully'
        ]);
    }

    public function show($id){
        $data = DB::table('item_distributions AS d')
        ->leftJoin('items AS d_item', 'd_item.id', '=', 'd.item_id')
        ->leftJoin('staff AS d_staff', 'd_staff.id', '=', 'd.staff_id')
        ->leftJoin('ddds AS d_ddd', 'd_ddd.id', '=', 'd.ddd_id')

        ->leftJoin('staff AS a_staff', function ($join) {
            $join->on('a_staff.id', '=', 'd.authorize_staff_id')
                ->leftJoin('ddds AS a_ddd', 'a_ddd.id', '=', 'a_staff.ddd_id');
        })
        ->select(
            'd_item.name AS distribute_item_name',
            'd_item.model AS distribute_item_model',
            'd.quantity AS distribute_quantity',
            'd.reference_no AS reference_no',
            'd.time AS distribute_time',
            'd.remark AS remark',
            'd.status AS status',

            'd_staff.staff_no AS distribute_staff_no',
            'd_staff.name AS distribute_staff_name',
            'd_staff.email AS distribute_staff_email',
            'd_ddd.short AS distribute_ddd',
            'd_ddd.floor AS distribute_floor',

            'a_staff.staff_no AS authorize_staff_no',
            'a_staff.name AS authorize_staff_name',
            'a_staff.email AS authorize_staff_email',
            'a_ddd.short AS authorize_ddd',
            'a_ddd.floor AS authorize_floor'
        )
        ->where('d.id', $id)
        ->first();

        return view('item-distribution-details',[
            'data' => $data
        ]);
    }

    public function destroy($delete_id){
        try {
            $timestamp = date("Y-m-d H:i:s");

            $del = soft_delete(ItemDistribution::class, $delete_id, auth()->id(), $timestamp);

            return response()->json(['status' => 'success', 'message' => $this->label.' deleted successfully']);
        } catch (QueryException $e) {
            return delete_catch_error($e, $this->label, ['item_request_id'], $delete_id);
        }
    }
}
