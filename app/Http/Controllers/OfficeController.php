<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Database\QueryException;


class OfficeController extends Controller
{
    public $label = 'Office';

    public function index(Request $request){
        if($request->ajax())
        {
            $role = auth()->user()->role;
            $data = Office::with('ddd', 'location');

            if($role == "DDD Admin"){
                $ddd_id = auth()->user()->ddd_id;
                $data = $data
                ->where('ddd_id', $ddd_id);
            }elseif($role == "Floor Admin"){
                $floor = auth()->user()->ddd->floor;
                $data = $data
                ->whereHas('ddd', function ($query) use ($floor) {
                    $query->where('floor', $floor);
                });
            }

            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
        }

        return view('offices');
    }

    public static function rules(Request $request){
        $unique_rule = Rule::unique('offices')->where(function ($query) use ($request){
            return $query->where('office_no', $request->office_no)
            ->where('location_id', $request->location_id);
        });

        if($request->isMethod('put')){
            $unique_rule = $unique_rule->ignore($request->route('office'));
        }

        return [
            'office_no'  => [
                $unique_rule,
                'required',
                'max:4'
            ],
            'description'  => 'max:50',
            'ddd_id'  => 'required|exists:ddds,id',
            'location_id'  => 'required|exists:locations,id'
        ];
    }

    public function store(Request $request){
        $form_data = $request->validate(self::rules($request));

        Office::create($form_data);
        return response()->json([
            'status' => 'success',
            'message' => $this->label.' added successfully'
        ]);
    }

    public function update(Request $request, $id){
        $form_data = $request->validate(self::rules($request));

        Office::whereId($id)->update($form_data);
        return response()->json([
            'status' => 'success',
            'message' => $this->label.' updated successfully'
        ]);
    }

    public function destroy($id){
        try {
            Office::findOrFail($id)->delete();

            return response()->json(['status' => 'success', 'message' => $this->label.' deleted successfully']);
        } catch (QueryException $e) {
            return delete_catch_error($e, $this->label, ['ddd_id'], $id);
        }
    }
}
