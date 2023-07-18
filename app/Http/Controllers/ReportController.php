<?php

namespace App\Http\Controllers;

use App\Models\Ddd;
use App\Models\Item;
use App\Models\Floor;
use App\Models\Staff;
use App\Models\Office;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RequestCategory;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private $label = 'Report';

    public function index(Request $request){
        // $date_from_val = date("Y-m-d H:i:s", 0);
        // $date_to_val = date("Y-m-d H:i:s");

        // $condition = function ($query) use ($date_from_val, $date_to_val) {
        //     $query->whereBetween('time', [$date_from_val, $date_to_val]);
        // };

        return view('reports');
    }

    public function generate(Request $request){
        if(@$request->date_from == ""){
            $date_from_val = date("Y-m-d H:i:s", 0);
            $date_from = "Inception";
        }else{
            $timestamp = strtotime($request->date_from);
            $date_from_val = date("Y-m-d H:i:s", $timestamp);
            $date_from = date('jS F, Y', $timestamp);
        }

        if(@$request->date_to == ""){
            $date_to_val = date("Y-m-d H:i:s");
            $date_to = "Date";
        }else{
            $timestamp = strtotime($request->date_to) + 86399;
            $date_to_val = date("Y-m-d H:i:s", $timestamp);
            $date_to = date('jS F, Y', $timestamp);
        }

        $columns = [];

        if($request->report_type == "Helpdesk Supports"){
            $columns = $this->supports_columns($request);

            if($request->report_by == "Staff"){
                $table = [];
                $parent_request_cats = RequestCategory::whereNull('parent_id')->get();
                foreach ($parent_request_cats as $parent) {
                    $condition = function ($query) use ($date_from_val, $date_to_val, $parent) {
                        $query->whereHas('request.request_category', function ($query) use($parent){
                            $query->where('parent_id', $parent->id);
                        })
                        ->whereBetween('time', [$date_from_val, $date_to_val]);
                    };
                    $table[] = [
                        'title' => Str::plural($parent->name),
                        'records' => $this->staff_supports($condition)
                    ];
                }
            } else {
                $condition = function ($query) use ($date_from_val, $date_to_val) {
                    $query->whereBetween('time', [$date_from_val, $date_to_val]);
                };

                if($request->report_by == "Categories"){
                    $table_records = $this->categories_supports($condition);
                }elseif($request->report_by == "Floors"){
                    $table_records = $this->floors_supports($condition);
                }elseif($request->report_by == "DDDs"){
                    $table_records = $this->ddds_supports($condition);
                }

                $table = array([
                    'title' => '',
                    'records' => $table_records
                ]);
            }
        }else{
            if($request->report_type == "Worktools Distribution"){
                $category = 'Worktool';
            }else{
                $category = 'Consumable';
            }
            $columns = $this->distributions_columns($request);

            $table = [];
            $items = Item::whereHas('distribution_items.last_distribution')
            ->distinct()
            ->pluck('name');

            foreach ($items as $item) {
                if($request->report_by == "Floors"){
                    $table_records = $this->floors_distributions(
                        $category,
                        $item,
                        $date_from_val,
                        $date_to_val
                    );
                }elseif($request->report_by == "DDDs"){
                    $condition = function ($query) use ($date_from_val, $date_to_val, $category, $item) {
                        $query->whereBetween('time', [$date_from_val, $date_to_val])
                        ->whereHas('distribution_item.item', function ($query) use ($category, $item) {
                            $query->where('category', $category)
                                ->where('name', $item);
                        });
                    };
                    $table_records = $this->ddds_distributions($condition);
                }
                $table[] = [
                    'title' => Str::plural($item),
                    'records' => $table_records
                ];
            }
        }

        $data = [
            'report_type' => $request->report_type,
            'report_by' => $request->report_by,
            'date_from' => $date_from,
            'date_to' => $date_to,

            'staff_no' => auth()->user()->staff_no,
            'staff_name' => auth()->user()->name,

            'date_generated' => date('jS F, Y'),
            'time_generated' => date('h:i:s a'),

            'columns' => $columns,
            'table' => $table
        ];
        $report_result = (string)view('report-result', compact(
            'data'
        ));

        return response()->json([
            'status' => 'success',
            'result' => $report_result
        ]);
    }

    private function supports_columns($request){
        $columns = [
            [
                'name' => 'pending',
                'label' => 'Pending',
                'h-attr' => 'class="text-center"',
                'd-attr' => 'class="text-center" width="1"',
                'format-value' => function($val){
                    return integer_format($val);
                },
                'total' => true
            ],
            [
                'name' => 'inprogress',
                'label' => 'In-Progress',
                'h-attr' => 'class="text-center"',
                'd-attr' => 'class="text-center" width="1"',
                'format-value' => function($val){
                    return integer_format($val);
                },
                'total' => true
            ],
            [
                'name' => 'unresolved',
                'label' => 'Unresolved',
                'h-attr' => 'class="text-center"',
                'd-attr' => 'class="text-center" width="1"',
                'format-value' => function($val){
                    return integer_format($val);
                },
                'total' => true
            ],
            [
                'name' => 'resolved',
                'label' => 'Resolved',
                'h-attr' => 'class="text-center"',
                'd-attr' => 'class="text-center" width="1"',
                'format-value' => function($val){
                    return integer_format($val);
                },
                'total' => true
            ],
            [
                'name' => 'total',
                'label' => 'Total',
                'h-attr' => 'class="text-center"',
                'd-attr' => 'class="text-center h5" width="1"',
                'format-value' => function($val){
                    return integer_format($val);
                },
                'total' => true
            ]
        ];

        if($request->report_by == "Categories"){
            array_unshift($columns, [
                'name' => 'label',
                'label' => 'Category',
                'formatter' => function($row){
                    return '<span class="h5">'.@$row->label.'</span><br>'.
                    '<span>'.@$row->parent->name.'</span>';
                }
            ]);
        }elseif($request->report_by == "Floors"){
            array_unshift($columns, [
                'name' => 'label',
                'label' => 'Floor',
                'format-value' => function($val){
                    return '<span class="h5">'.$val.'</h5>';
                }
            ]);
        }elseif($request->report_by == "DDDs"){
            array_unshift($columns, [
                'name' => 'label',
                'label' => 'DDD',
                'formatter' => function($row){
                    return '<span class="h5">'.@$row->label.'</span><br>'.
                    '<span>'.@$row->name.'</span>';
                }
            ]);
        }elseif($request->report_by == "Staff"){
            array_unshift($columns, [
                'name' => 'label',
                'label' => 'Staff',
                'formatter' => function($row){
                    return '<span class="h5">'.@$row->staff_no.'</span><br>'.
                    '<span>'.@$row->name.'</span>';
                }
            ]);

            array_splice($columns, 4, 0, array([
                'name' => 'escalated',
                'label' => 'Escalated',
                'h-attr' => 'class="text-center"',
                'd-attr' => 'class="text-center" width="1"',
                'format-value' => function($val){
                    return integer_format($val);
                },
                'total' => true
            ]));
        }
        return $columns;
    }

    private function distributions_columns($request){
        $columns = [
            [
                'name' => 'allocated',
                'label' => 'Allocated',
                'h-attr' => 'class="text-center"',
                'd-attr' => 'class="text-center" width="1"',
                'format-value' => function($val){
                    return integer_format($val);
                },
                'total' => true
            ],
            [
                'name' => 'configured',
                'label' => 'Configured',
                'h-attr' => 'class="text-center"',
                'd-attr' => 'class="text-center" width="1"',
                'format-value' => function($val){
                    return integer_format($val);
                },
                'total' => true
            ],
            [
                'name' => 'installed',
                'label' => 'Installed',
                'h-attr' => 'class="text-center"',
                'd-attr' => 'class="text-center" width="1"',
                'format-value' => function($val){
                    return integer_format($val);
                },
                'total' => true
            ],
            [
                'name' => 'distributed',
                'label' => 'Distributed',
                'h-attr' => 'class="text-center"',
                'd-attr' => 'class="text-center" width="1"',
                'format-value' => function($val){
                    return integer_format($val);
                },
                'total' => true
            ],
            [
                'name' => 'returned',
                'label' => 'Returned',
                'h-attr' => 'class="text-center"',
                'd-attr' => 'class="text-center" width="1"',
                'format-value' => function($val){
                    return integer_format($val);
                },
                'total' => true
            ]
        ];

        if($request->report_by == "Categories"){
            array_unshift($columns, [
                'name' => 'label',
                'label' => 'Category',
                'formatter' => function($row){
                    return '<span class="h5">'.@$row->label.'</span><br>'.
                    '<span>'.@$row->parent->name.'</span>';
                }
            ]);
        }elseif($request->report_by == "Floors"){
            array_unshift($columns, [
                'name' => 'label',
                'label' => 'Floor',
                'format-value' => function($val){
                    return '<span class="h5">'.$val.'</h5>';
                }
            ]);
        }elseif($request->report_by == "DDDs"){
            array_unshift($columns, [
                'name' => 'label',
                'label' => 'DDD',
                'formatter' => function($row){
                    return '<span class="h5">'.@$row->label.'</span><br>'.
                    '<span>'.@$row->name.'</span>';
                }
            ]);
        }elseif($request->report_by == "Staff"){
            array_unshift($columns, [
                'name' => 'label',
                'label' => 'Staff',
                'formatter' => function($row){
                    return '<span class="h5">'.@$row->staff_no.'</span><br>'.
                    '<span>'.@$row->name.'</span>';
                }
            ]);
        }
        return $columns;
    }

    /** SUPPORTS */
    private function categories_supports($condition){
        return RequestCategory::whereHas('helpdesks', $condition)
        ->withCount([
            'pending_helpdesks AS pending' => $condition,
            'inprogress_helpdesks AS inprogress' => $condition,
            'resolved_helpdesks AS resolved' => $condition,
            'unresolved_helpdesks AS unresolved' => $condition,
            'helpdesks AS total' => $condition
        ])
        ->addSelect('name AS label')
        ->orderBy('name')
        ->get();
    }

    private function floors_supports($condition){
        return Floor::whereHas('helpdesks', $condition)
        ->withCount([
            'pending_helpdesks AS pending' => $condition,
            'inprogress_helpdesks AS inprogress' => $condition,
            'resolved_helpdesks AS resolved' => $condition,
            'unresolved_helpdesks AS unresolved' => $condition,
            'helpdesks AS total' => $condition
        ])
        ->addSelect('name AS label')
        ->orderBy('id')
        ->get();
    }

    private function ddds_supports($condition){
        return Ddd::whereHas('helpdesks', $condition)
        ->withCount([
            'pending_helpdesks AS pending' => $condition,
            'inprogress_helpdesks AS inprogress' => $condition,
            'resolved_helpdesks AS resolved' => $condition,
            'unresolved_helpdesks AS unresolved' => $condition,
            'helpdesks AS total' => $condition
        ])
        ->addSelect('short AS label')
        ->orderBy('short')
        ->get();
    }

    private function staff_supports($condition){
        return Staff::withoutGlobalScope('select')
        ->whereHas('helpdesks', $condition)
        ->withCount([
            'pending_helpdesks AS pending' => $condition,
            'inprogress_helpdesks AS inprogress' => $condition,
            'escalated_helpdesks AS escalated' => $condition,
            'resolved_helpdesks AS resolved' => $condition,
            'unresolved_helpdesks AS unresolved' => $condition,
            'helpdesks AS total' => $condition
        ])
        ->addSelect('staff_no AS label')
        ->orderBy('staff_no')
        ->get();
    }

    /** DISTRIBUTIONS */
    private function floors_distributions_subquery($table_name, $model_class, $category, $item, $date_from_val, $date_to_val){
        return Floor::join('ddds', 'floors.name', '=', 'ddds.floor')
        ->join($table_name, 'ddds.id', '=', $table_name . '.ddd_id')
        ->join('item_distributions', function ($join) use ($table_name, $model_class, $date_from_val, $date_to_val) {
            $join->on($table_name . '.id', '=', 'item_distributions.distributionable_id')
                ->where('item_distributions.distributionable_type', '=', $model_class)
                ->whereNull('valid_until')
                ->whereBetween('time', [$date_from_val, $date_to_val]);
        })
        ->whereHas('ddds.' . $table_name . '.distributions.distribution_item.item', function ($query) use ($category, $item) {
            $query->where('category', $category)
            ->where('name', $item);
        })
        ->select('floors.name as label')
        ->selectRaw('COUNT(item_distributions.id) as total')
        ->selectRaw('SUM(CASE WHEN item_distributions.status = "Allocated" THEN 1 ELSE 0 END) as allocated')
        ->selectRaw('SUM(CASE WHEN item_distributions.status = "Configured" THEN 1 ELSE 0 END) as configured')
        ->selectRaw('SUM(CASE WHEN item_distributions.status = "Installed" THEN 1 ELSE 0 END) as installed')
        ->selectRaw('SUM(CASE WHEN item_distributions.status = "Distributed" THEN 1 ELSE 0 END) as distributed')
        ->selectRaw('SUM(CASE WHEN item_distributions.status = "Returned" THEN 1 ELSE 0 END) as returned')
        ->groupBy('floors.id');
    }

    private function floors_distributions($category, $item, $date_from_val, $date_to_val){
        $staff_query = $this->floors_distributions_subquery(
            'staff',
            Staff::class,
            $category,
            $item,
            $date_from_val,
            $date_to_val
        );
        $office_query = $this->floors_distributions_subquery(
            'offices',
            Office::class,
            $category,
            $item,
            $date_from_val,
            $date_to_val
        );

        return DB::query()
        ->from($staff_query->unionAll($office_query))
        ->select('label')
        ->selectRaw('
            CAST(SUM(allocated) AS SIGNED) as allocated,
            CAST(SUM(configured) AS SIGNED) as configured,
            CAST(SUM(installed) AS SIGNED) as installed,
            CAST(SUM(distributed) AS SIGNED) as distributed,
            CAST(SUM(returned) AS SIGNED) as returned
        ')
        ->groupBy('label')
        ->get();
    }

    private function ddds_distributions($condition){
        $staff_query = Ddd::whereHas('staff_distributions', $condition)
        ->withCount([
            'staff_allocated AS allocated' => $condition,
            'staff_configured AS configured' => $condition,
            'staff_installed AS installed' => $condition,
            'staff_distributed AS distributed' => $condition,
            'staff_returned AS returned' => $condition
        ])
        ->addSelect('short AS label');

        $office_query = Ddd::whereHas('office_distributions', $condition)
        ->withCount([
            'office_allocated AS allocated' => $condition,
            'office_configured AS configured' => $condition,
            'office_installed AS installed' => $condition,
            'office_distributed AS distributed' => $condition,
            'office_returned AS returned' => $condition
        ])
        ->addSelect('short AS label');

        return DB::query()
        ->from($staff_query->unionAll($office_query))
        ->select('label')
        ->selectRaw('
            CAST(SUM(allocated) AS SIGNED) as allocated,
            CAST(SUM(configured) AS SIGNED) as configured,
            CAST(SUM(installed) AS SIGNED) as installed,
            CAST(SUM(distributed) AS SIGNED) as distributed,
            CAST(SUM(returned) AS SIGNED) as returned
        ')
        ->groupBy('label')
        ->get();
    }
}
