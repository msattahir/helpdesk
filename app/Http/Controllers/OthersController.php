<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OthersController extends Controller
{
    public function get_record($table, $column, $id){
        $record = DB::table($table)
        ->where($column, $id)
        ->whereNull('valid_until')
        ->first();

        return response()->json([
            'record' => $record
        ]);
    }

    public function get_options($type, $id){
        if($type == 'request-sub-cat'){
            return response()->json([
                'record' => get_request_category_options([
                    'parent_id' => $id
                ])
            ]);
        }elseif($type == 'staff'){
            return response()->json([
                'record' => get_staff_options([
                    'ddd_id' => $id
                ])
            ]);
        }elseif($type == 'office'){
            return response()->json([
                'record' => get_office_options([
                    'ddd_id' => $id
                ])
            ]);
        }
    }
}
