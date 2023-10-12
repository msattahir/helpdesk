<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ItemController extends Controller
{
    public $label = 'Item';

    public function index(Request $request){
        if($request->ajax())
        {
            $data = Item::latest();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('name-view', function($data){
                return '<span class="d-block h5 text-inherit mb-0">'.@$data->name.'</span>';
            })
            ->addColumn('category-view', function($data){
                return '<span class="d-block h5 text-inherit mb-0">'.@$data->category.'</span>';
            })
            ->addColumn('action', function($data){
                $attrs = 'data-id="'.$data->id.'"'.
                    'data-name="'.$data->name.'"'.
                    'data-model="'.$data->model.'"'.
                    'data-category="'.$data->category.'"';
                return '<div class="btn-group">
                    <button type="button" class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#register-modal" name="edit" '.$attrs.'">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <div class="or or-xs"></div>
                    <button type="button" class="btn btn-danger btn-xs" name="delete" data-id="'.$data->id.'">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>';
            })
            ->with('no_records', $data->count())
            ->rawColumns(['name-view', 'category-view', 'action'])
            ->make(true);
        }

        return view('items');
    }

    public static function rules(Request $request){
        $unique_rule = Rule::unique('items')->where(function ($query) use ($request){
            return $query->where('name', $request->name)
            ->where('model', $request->model);
        });

        if($request->isMethod('put')){
            $unique_rule = $unique_rule->ignore($request->route('item'));
        }

        return [
            'name'  => [
                $unique_rule,
                Rule::in(get_item_name_options("validate"))
            ],
            'model' => [
                'required',
                'max:100'
            ],
            'category' => [
                Rule::in(get_item_category_options("validate"))
            ]
        ];
    }

    public static function rules_messages(){
        return [
            'name.unique' => 'The Worktool Model already registered!'
        ];
    }

    public function store(Request $request){
        $form_data = $request->validate(
            self::rules($request),
            self::rules_messages()
        );

        Item::create($form_data);
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

        Item::whereId($id)->update($form_data);
        return response()->json([
            'status' => 'success',
            'message' => $this->label.' updated successfully'
        ]);
    }

    public function destroy($id){
        try {
            Item::findOrFail($id)->delete();

            return response()->json(['status' => 'success', 'message' => $this->label.' deleted successfully']);
        } catch (QueryException $e) {
            return delete_catch_error($e, $this->label, ['item_id'], $id);
        }
    }
}
