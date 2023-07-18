<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ItemDistribution;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use App\Http\Controllers\ItemDistributionController;

class ItemRequestController extends Controller
{
    public $label = 'Item Request';

    public function index(Request $request){
        if($request->ajax())
        {
            $query = DB::table('item_requests')
            ->leftJoin('items', 'items.id', '=', 'item_requests.item_id')
            ->leftJoin('staff', function ($join) {
                $join->on('staff.id', '=', 'item_requests.staff_id')
                    ->leftJoin('ddds', 'ddds.id', '=', 'staff.ddd_id');
            })
            ->select(
                'item_requests.*',
                'items.name as item_name',
                'items.model as item_model',
                'staff.id as staff_id',
                'staff.staff_no as staff_no',
                'staff.name as staff_name',
                'ddds.id as ddd_id',
                'ddds.short as ddd',
                'ddds.floor as floor'
            )
            ->whereNull('valid_until');

            $role = auth()->user()->role;
            if($role == "Staff"){
                $staff_id = auth()->id();
                $query = $query
                ->where('staff.id', $staff_id);
            }elseif($role == "DDD Admin"){
                $ddd_id = auth()->user()->ddd_id;
                $query = $query
                ->where('ddds.id', $ddd_id);
            }elseif($role == "Floor Admin"){
                $floor = auth()->user()->ddd->floor;
                $query = $query
                ->where('ddds.floor', $floor);
            }

            $data = $query
            ->orderBy('time', 'DESC')
            ->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('staff-view', function($data){
                return '<div>
                    <span class="d-block h5 text-inherit mb-0">'.@$data->staff_no.'</span>
                    <span class="d-block fs-5 text-body">'.@$data->staff_name.'</span>
                </div>';
            })
            ->addColumn('item-view', function($data){
                return '<div>
                    <span class="d-block h5 text-inherit mb-0">'.@$data->item_name.'</span>
                    <span class="d-block fs-5 text-body">'.@$data->item_model.'</span>
                </div>';
            })
            ->editColumn('quantity', function($data){
                return '<h5>'.integer_format(@$data->quantity).'</h5>';
            })
            ->addColumn('status-view', function($data){
                return format_label(@$data->status);
            })
            ->addColumn('date-view', function($data){
                $timestamp = strtotime(@$data->time);

                return '<h5>'.date('Y-m-d', $timestamp).'</h5>'.
                date('h:i:s', $timestamp);
            })
            ->addColumn('action', function($data){
                $attrs = 'data-id="'.$data->id.'"'.
                    'data-item_id="'.$data->item_id.'"'.
                    'data-quantity="'.$data->quantity.'"'.
                    'data-description="'.$data->description.'"';
                $authorize_attrs = 'data-item_request_id="'.$data->id.'"'.
                'data-staff_id="'.$data->staff_id.'"'.
                'data-ddd_id="'.$data->ddd_id.'"'.
                'data-status="'.$data->status.'"';
                return '<button class="btn btn-primary btn-xs dropdown dropdown-toggle" type="button" id="dropdownMenuButtonWithIcon"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi-sliders"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWithIcon">
                    <a class="dropdown-item link-primary" href="/item-requests/'.$data->id.'">
                        <i class="bi-person dropdown-item-icon"></i> View Details
                    </a>
                    '.
                    (check_route_access('item-requests.update') ?

                    '<a class="dropdown-item link-blue" type="button" data-bs-toggle="modal" data-bs-target="#register-modal" name="edit" '.$attrs.'">
                        <i class="bi-pencil-square dropdown-item-icon"></i> Edit Request
                    </a>
                    <a class="dropdown-item link-info" type="button" data-bs-toggle="modal" data-bs-target="#authorize-modal" name="authorize" '.$authorize_attrs.'>
                        <i class="bi-clipboard-check dropdown-item-icon"></i> Authorize Request
                    </a>
                    <a class="dropdown-item link-danger" type="button" name="delete" data-id="'.$data->id.'">
                        <i class="bi bi-trash dropdown-item-icon"></i> Delete Request
                    </a>' : '').

                '</div>';
            })
            ->with('no_records', $data->count())
            ->rawColumns(['staff-view', 'item-view', 'quantity', 'status-view', 'date-view', 'action'])
            ->make(true);
        }

        return view('item-requests');
    }

    public static function rules(Request $request){
        return [
            'item_id'  => 'required|exists:items,id',
            'quantity'  => 'numeric',
            'description'  => 'nullable|max:200'
        ];
    }

    public static function rules_messages(){
        return [
            'item_id.exists' => 'Request item not recognized'
        ];
    }

    public function store(Request $request){
        $form_data = $request->validate(
            self::rules($request),
            self::rules_messages()
        );

        $timestamp = date("Y-m-d H:i:s");
        $form_data['staff_id'] = auth()->id();
        $form_data['valid_from'] = $timestamp;
        $form_data['time'] = $timestamp;

        ItemRequest::create($form_data);
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

        $timestamp = date("Y-m-d H:i:s");
        $form_data['alter_staff_id'] = auth()->id();

        $update = soft_update(ItemRequest::class, $id, $form_data, auth()->id(), $timestamp);
        return response()->json([
            'status' => 'success',
            'message' => $this->label.' updated successfully'
        ]);
    }

    public function authorize_request(Request $request){

        $timestamp = date("Y-m-d H:i:s");
        $form_data = $request->validate([
            'item_request_id'  => 'required|exists:item_requests,id',
            'staff_id'  => 'required|exists:staff,id',
            'ddd_id'  => 'required|exists:ddds,id',
            'status'  => Rule::in(get_request_status_options("validate")),
            'item_id'  => 'nullable|exists:items,id',
            'quantity'  => 'nullable|numeric',
            'reference_no'  => 'nullable|max:255',
            'remark'  => 'nullable|max:200',
            'time'  => 'required|date_format:Y-m-d\TH:i'
        ],
        [
            'item_request_id.exists' => 'Request not recognized',
            'staff_id.exists' => 'Staff not recognized',
            'ddd_id.exists' => 'DDD not recognized'
        ]);

        if($request->status != "Dismissed"){
            if(ItemDistributionController::insufficient_balance($request, $request->id)){
                return response()->json([
                    'status' => 'danger',
                    'message' => 'Insufficient Quantity'
                ]);
            }

            if($request->id > 0){
                $form_data['alter_staff_id'] = auth()->id();

                $update = soft_update(ItemDistribution::class, $request->id, $form_data, auth()->id(), $timestamp);
            }else {
                $form_data['authorize_staff_id'] = auth()->id();
                $form_data['valid_from'] = $timestamp;

                ItemDistribution::create($form_data);
            }
        }

        DB::table('item_requests')
        ->where('id', $request->item_request_id)
        ->update([
            'alter_staff_id' => auth()->id(),
            'status' => $request->status
        ]);
        return response()->json([
            'status' => 'success',
            'message' => $this->label.' authorized successfully'
        ]);
    }

    public function show($id){
        $request = DB::table('item_requests AS r')
        ->leftJoin('items AS r_item', 'r_item.id', '=', 'r.item_id')
        ->leftJoin('staff AS r_staff', function ($join) {
            $join->on('r_staff.id', '=', 'r.staff_id')
                ->leftJoin('ddds AS r_ddd', 'r_ddd.id', '=', 'r_staff.ddd_id');
        })

        ->leftJoin('item_distributions AS d', function ($join) {
            $join->on('d.item_request_id', '=', 'r.id')
                ->whereNull('d.valid_until');
        })
        ->leftJoin('items AS d_item', 'd_item.id', '=', 'd.item_id')
        ->leftJoin('staff AS d_staff', function ($join) {
            $join->on('d_staff.id', '=', 'd.authorize_staff_id')
                ->leftJoin('ddds AS d_ddd', 'd_ddd.id', '=', 'd_staff.ddd_id');
        })
        ->select(
            'r.quantity AS request_quantity',
            'r.description AS description',
            'r.time AS request_time',
            'r.status AS status',

            'r_item.name AS request_item_name',
            'r_item.model AS request_item_model',
            'r_staff.staff_no AS request_staff_no',
            'r_staff.name AS request_staff_name',
            'r_staff.email AS request_staff_email',
            'r_ddd.short AS request_ddd',
            'r_ddd.floor AS request_floor',

            'd.quantity AS supply_quantity',
            'd.time AS supply_time',
            'd.reference_no AS reference_no',
            'd.remark AS remark',

            'd_item.name AS supply_item_name',
            'd_item.model AS supply_item_model',
            'd_staff.staff_no AS supply_staff_no',
            'd_staff.name AS supply_staff_name',
            'd_staff.email AS supply_staff_email',
            'd_ddd.short AS supply_ddd',
            'd_ddd.floor AS supply_floor'
        )
        ->where('r.id', $id)
        ->first();

        return view('item-request-details',[
            'request' => $request
        ]);
    }

    public function destroy($delete_id){
        try {
            $timestamp = date("Y-m-d H:i:s");

            $del = soft_delete(ItemRequest::class, $delete_id, auth()->id(), $timestamp);

            return response()->json(['status' => 'success', 'message' => $this->label.' deleted successfully']);
        } catch (QueryException $e) {
            return delete_catch_error($e, $this->label, ['item_request_id'], $delete_id);
        }
    }
}
