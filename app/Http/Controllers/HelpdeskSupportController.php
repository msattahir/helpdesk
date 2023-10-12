<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\HelpdeskRequest;
use App\Models\HelpdeskSupport;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class HelpdeskSupportController extends Controller
{
    public $label = 'Helpdesk Support';

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
            } elseif(!in_array($role, ["Admin", "Helpdesk Admin", "Adhoc Staff"])){
                $staff_id = auth()->id();

                $staff_condition = function ($query) use ($staff_id) {
                    $query->where('id', $staff_id);
                };
            }

            $query = HelpdeskSupport::with([
                'staff',
                'request.ddd',
                'request.request_category.parent'
            ])
            ->whereHas('staff', $staff_condition)
            ->whereHas('request.ddd', $ddd_condition);

            $request_id = request('request');
            if($request_id){
                $query = $query
                ->where('helpdesk_request_id', $request_id);
            }

            $data = $query
            ->latest();

            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
        }

        return view('helpdesk-supports');
    }

    public static function rules(Request $request){
        return [
            'status'  => Rule::in(get_request_status_options("validate")),
            'escalate_staff_id'  => 'nullable|exists:staff,id',
            'remark'  => 'nullable|max:200',
            'time'  => 'required|date_format:Y-m-d\TH:i'
        ];
    }

    public function store(Request $request){
        $form_data = $request->validate(
            self::rules($request)
        );

        $timestamp = date("Y-m-d H:i:s");
        $form_data['valid_from'] = $timestamp;

        HelpdeskSupport::create($form_data);

        return response()->json([
            'status' => 'success',
            'message' => $this->label.' submitted successfully'
        ]);
    }

    public function update(Request $request, $id){
        if($request->operation == "start"){
            $form_data = [
                'status' => 'In-Progress'
            ];
        } else {
            $form_data = $request->validate(
                self::rules($request)
            );
        }

        $timestamp = date("Y-m-d H:i:s");
        $form_data['alter_staff_id'] = auth()->id();

        $update = soft_update(
            HelpdeskSupport::class,
            $id,
            $form_data,
            auth()->id(),
            $timestamp
        );
        $current_support = HelpdeskSupport::find($id);
        $next_support = $current_support->next_support();

        if($form_data['status'] == 'Escalated'){
            $new_request_status = "In-Progress";
        }else{
            $new_request_status = $form_data['status'];
        }
        $helpdesk_request = HelpdeskRequest::
        find($current_support->helpdesk_request_id);

        if(!$next_support && $helpdesk_request->status != $new_request_status){
            $helpdesk_request->status = $new_request_status;
            $helpdesk_request->save();
        }

        if(@$form_data['escalate_staff_id'] != ""){
            if($next_support){
                if($next_support->staff_id != $form_data['escalate_staff_id']){
                    $update = soft_update(
                        HelpdeskSupport::class,
                        $next_support->id,
                        [
                            'staff_id' => $form_data['escalate_staff_id']
                        ],
                        auth()->id(),
                        $timestamp
                    );
                }
            }else{
                HelpdeskSupport::create([
                    'helpdesk_request_id' => $current_support->helpdesk_request_id,
                    'status' => 'Pending',
                    'staff_id' => $form_data['escalate_staff_id'],
                    'valid_from' => $timestamp,
                    'time' => $timestamp
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => $this->label.' updated successfully'
        ]);
    }

    public function show(Request $request, $id){
        if($request->ajax()){
            return HelpdeskSupport::find($id)->next_support();
        }
    }

    public function destroy($delete_id){
        try {
            $timestamp = date("Y-m-d H:i:s");

            $del = soft_delete(HelpdeskSupport::class, $delete_id, auth()->id(), $timestamp);

            return response()->json(['status' => 'success', 'message' => $this->label.' deleted successfully']);
        } catch (QueryException $e) {
            return delete_catch_error($e, $this->label, ['item_request_id'], $delete_id);
        }
    }
}
