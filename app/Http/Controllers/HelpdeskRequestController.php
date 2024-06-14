<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HelpdeskRequest;
use App\Models\HelpdeskSupport;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use App\Events\HelpdeskRequestSubmitted;


class HelpdeskRequestController extends Controller
{

    public $label = 'Helpdesk Request';

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
                    $query->where('id', $staff_id);
                };
            }

            $data = HelpdeskRequest::with([
                'staff.location',
                'ddd',
                'first_support.staff',
                'request_category.parent'
            ])
            ->whereHas('staff', $staff_condition)
            ->whereHas('ddd', $ddd_condition)
            ->select('helpdesk_requests.*');

            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
        }

        return view('helpdesk-requests');
    }

    public function show(Request $request, $id){
        $data = HelpdeskRequest::find($id);

        return view('helpdesk-request-details',[
            'data' => $data
        ]);
    }

    public static function rules(Request $request){
        return [
            'ddd_id'  => 'required|exists:ddds,id',
            'staff_id'  => 'required|exists:staff,id',
            'request_category_id'  => 'required|exists:request_categories,id',
            'description'  => 'nullable|max:200',
            'support_staff_id'  => 'required|exists:staff,id'
        ];
    }

    public function store(Request $request){
        $form_data = $request->validate(self::rules($request));

        $timestamp = date("Y-m-d H:i:s");
        $form_data['authorize_staff_id'] = auth()->id();
        $form_data['valid_from'] = $timestamp;
        $form_data['time'] = $timestamp;

        $helpdesk_request = HelpdeskRequest::create($form_data);

        $first_support = HelpdeskSupport::create([
            'helpdesk_request_id' => $helpdesk_request->id,
            'status' => 'Pending',
            'staff_id' => $form_data['support_staff_id'],
            'valid_from' => $timestamp,
            'time' => $timestamp
        ]);

        // HelpdeskRequestSubmitted::dispatch($helpdesk_request, $first_support);

        return response()->json([
            'status' => 'success',
            'message' => $this->label.' added successfully'
        ]);
    }

    public function update(Request $request, $id){
        $form_data = $request->validate(self::rules($request));

        $valid_until = date("Y-m-d H:i:s");
        $form_data['alter_staff_id'] = auth()->id();

        $update = soft_update(
            HelpdeskRequest::class,
            $id,
            $form_data,
            auth()->id(),
            $valid_until
        );

        $first_support = HelpdeskRequest::with('first_support')
        ->find($id)
        ->first_support;

        if($form_data['support_staff_id'] != $first_support->staff_id){
            $update = soft_update(
                HelpdeskSupport::class,
                $first_support->id,
                [
                    'staff_id' => $form_data['support_staff_id']
                ],
                auth()->id(),
                $valid_until
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => $this->label.' updated successfully'
        ]);
    }

    public function destroy($delete_id){
        $del = soft_delete(
            HelpdeskRequest::class,
            $delete_id,
            auth()->id(),
            date("Y-m-d H:i:s"),
            ['helpdesk_request_id'],
            $delete_id
        );

        return response()->json(['status' => 'success', 'message' => $this->label.' deleted successfully']);
    }
}
