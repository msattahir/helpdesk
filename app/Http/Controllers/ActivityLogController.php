<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ActivityLogController extends Controller
{
    public function index(Request $request){
        if($request->ajax()){
            $data = ActivityLog::with(['staff.ddd'])
            ->latest()
            ->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
        }

        return view('activities');
    }
}
