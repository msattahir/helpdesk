<?php

namespace App\Http\Controllers;

use App\Models\Ddd;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class DDDController extends Controller
{
    public $label = 'DDD';

    public function index(Request $request){
        if($request->ajax())
        {
            $data = Ddd::query();
            if(auth()->user()->role == "Floor Admin"){
                $floor = auth()->user()->ddd->floor;
                $data = $data->where('floor', $floor);
            }

            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
        }

        return view('ddds');
    }

    public static function rules(Request $request){
        $unique_rule = 'unique:ddds,name';
        $unique_rule_2 = 'unique:ddds,short';

        if($request->isMethod('put')){
            $id = $request->route('ddd');
            $unique_rule = $unique_rule.','.$id;
            $unique_rule_2 = $unique_rule_2.','.$id;
        }

        return [
            'name'  => [
                'required',
                'max:100',
                $unique_rule
            ],
            'short'  => [
                'required',
                'max:10',
                $unique_rule_2
            ],
            'category' => [
                Rule::in(get_ddd_category_options("validate"))
            ],
            'floor' => [
                Rule::in(get_floor_options("validate"))
            ]
        ];
    }

    public function store(Request $request){
        $form_data = $request->validate(self::rules($request));

        Ddd::create($form_data);
        return response()->json([
            'status' => 'success',
            'message' => $this->label.' added successfully'
        ]);
    }

    public function update(Request $request, $id){
        $form_data = $request->validate(self::rules($request));

        Ddd::whereId($id)->update($form_data);
        return response()->json([
            'status' => 'success',
            'message' => $this->label.' updated successfully'
        ]);
    }

    public function destroy($id){
        try {
            Ddd::findOrFail($id)->delete();

            return response()->json(['status' => 'success', 'message' => $this->label.' deleted successfully']);
        } catch (QueryException $e) {
            return delete_catch_error($e, $this->label, ['ddd_id'], $id);
        }
    }
}
