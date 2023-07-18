<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\ItemDistribution;
use Illuminate\Http\Request;
use App\Models\HelpdeskSupport;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class StaffController extends Controller
{
    public $label = 'Staff';

    public function index(Request $request){
        // DB::connection()->enableQueryLog();

        // $query = Staff::with('location')->first();
        // $queries = DB::getQueryLog();
        // dd($queries);

        if($request->ajax())
        {
            $role = auth()->user()->role;
            $query = Staff::with('ddd', 'location');
            if($role == "DDD Admin"){
                $ddd_id = auth()->user()->ddd_id;
                $query = $query
                ->where('ddd_id', $ddd_id);
            }elseif($role == "Floor Admin"){
                $floor = auth()->user()->ddd->floor;
                $query = $query
                ->whereHas('ddd', function ($query) use ($floor) {
                    $query->where('floor', $floor);
                });
            }
            $data = $query
                ->latest()
                ->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
        }

        return view('staff');
    }

    public static function password_rules(){
        return[
            'password' => 'required|confirmed|min:6'
        ];
    }

    public static function rules(Request $request){
        $unique_staff_no = 'unique:staff,staff_no';
        $unique_email = 'unique:staff,email';
        if($request->isMethod('put')){
            $unique_staff_no = $unique_staff_no.','.$request->route('staff');
            $unique_email = $unique_email.','.$request->route('staff');
        }

        $rules = [
            'staff_no'  => ['required', 'max:5', $unique_staff_no],
            'name'  => ['required', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:100',
                $unique_email
            ],
            'ddd_id'  => 'exists:ddds,id',
            'location_id'  => 'exists:locations,id',
            'role'  => [
                Rule::in(get_account_role_options("validate"))
            ],
            'status'  => [
                Rule::in(get_account_status_options("validate"))
            ]
        ];

        if($request->isMethod('post')){
            $rules = array_merge($rules, self::password_rules());
        }
        return $rules;
    }

    public function store(Request $request){
        $form_data = $request->validate(self::rules($request));

        $form_data['password'] = bcrypt($form_data['password']);
        Staff::create($form_data);
        return response()->json([
            'status' => 'success',
            'message' => $this->label.' added successfully'
        ]);
    }

    public function update(Request $request, $id){
        $status = 'success';
        if($request->operation == 'edit'){
            $form_data = $request->validate(self::rules($request));
            Staff::whereId($id)->update($form_data);
            $msg = $this->label.' updated successfully';
        }else{
            $form_data = $request->validate(self::password_rules());
            $form_data['password'] = bcrypt($form_data['password']);
            $form_data['reset_password'] = true;

            Staff::whereId($id)->update($form_data);

            $this->label = 'Staff password';
            $msg = 'Password reset successfully';
        }

        return response()->json([
            'status' => $status,
            'message' => $msg
        ]);
    }

    public function destroy($id){
        try {
            Staff::findOrFail($id)->delete();

            return response()->json(['status' => 'success', 'message' => $this->label.' deleted successfully']);
        } catch (QueryException $e) {
            return delete_catch_error($e, $this->label, ['staff_id', 'request_staff_id', 'support_staff_id', 'supply_staff_id', 'alter_staff_id'], $id);
        }
    }

    public function show($id, $page_title = 'Staff Details'){
        $staff = Staff::find($id);

        $item_query = ItemDistribution::with('item')
        ->where('distributionable_id', $id)
        ->where('distributionable_type', Staff::class);

        $devices = $item_query->whereHas('item', function ($query) {
            $query->where('category', 'Worktool');
        })
        ->get();

        $consumables = $item_query->whereHas('item', function ($query) {
            $query->where('category', 'Consumable');
        })
        ->get();

        $helpdesks = HelpdeskSupport::select('remark', 'status', 'time as date_time')
            ->where('staff_id', $id)
            ->get();

        return view('staff-details',[
            'page_title' => $page_title,
            'staff' => $staff,
            'devices' => $devices,
            'consumables' => $consumables,
            'helpdesks' => $helpdesks
        ]);
    }

    public function profile(){
        return $this->show(auth()->id(), 'Profile');
    }
}
