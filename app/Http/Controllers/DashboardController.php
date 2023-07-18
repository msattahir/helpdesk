<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ActivityLog;
use App\Models\ItemDistribution;
use Illuminate\Http\Request;
use App\Models\HelpdeskRequest;
use App\Models\HelpdeskSupport;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){
        $total_count = 'COUNT(*) as total';
        $pending_count = 'SUM(CASE WHEN status = "Pending" THEN 1 ELSE 0 END) as pending';
        $treated_count = 'SUM(CASE WHEN status != "Pending" THEN 1 ELSE 0 END) as treated';

        $helpdesk_requests = HelpdeskRequest::selectRaw($total_count)
            ->selectRaw($pending_count)
            ->selectRaw($treated_count);

        $pending_count = 'SUM(CASE WHEN status = "Allocated" THEN 1 ELSE 0 END) as pending';
        $treated_count = 'SUM(CASE WHEN status != "Allocated" THEN 1 ELSE 0 END) as treated';
        $item_requests = ItemDistribution::selectRaw($total_count)
            ->selectRaw($pending_count)
            ->selectRaw($treated_count);

        $role = auth()->user()->role;
        if($role == "Staff"){
            $staff_id = auth()->id();

            $helpdesk_requests = $helpdesk_requests->where('staff_id', $staff_id);
            $item_requests = $item_requests->where('staff_id', $staff_id);
        }elseif($role == "DDD Admin"){
            $ddd_id = auth()->user()->ddd_id;
            $ddd_condition = function ($query) use ($ddd_id) {
                $query->where('ddd_id', $ddd_id);
            };

            $helpdesk_requests = $helpdesk_requests
            ->with(['staff' => $ddd_condition]);

            $item_requests = $item_requests
            ->with(['distributionable' => $ddd_condition]);
        }elseif($role == "Floor Admin"){
            $floor = auth()->user()->ddd->floor;
            $floor_condition = function ($query) use ($floor) {
                $query->where('floor', $floor);
            };

            $helpdesk_requests = $helpdesk_requests
            ->with(['staff.ddd' => $floor_condition]);

            $item_requests = $item_requests
            ->with(['distributionable.ddd' => $floor_condition]);
        }

        $helpdesk_requests = $helpdesk_requests->first();
        $item_requests = $item_requests->first();

        $inventory = [];
        if(auth()->user()->role == "Admin"){
            $inventory = Item::withCount('distribution_items')
                ->with('inventory')
                ->selectRaw('CONCAT(name, " - ", model) AS name')
                ->get();
        }

        return view('index', [
            'helpdesk_requests' => $helpdesk_requests,
            'item_requests' => $item_requests,
            'inventory' => $inventory
        ]);
    }

    public function recent_helpdesk_requests(){
        $data = HelpdeskRequest::with([
            'staff' => function ($query) {
                $query->select('id', 'staff_no', 'name', 'ddd_id');
            }, 'ddd' => function ($query) {
                $query->select('id', 'short', 'floor');
            }
        ])->latest('time')
        ->take(5)
        ->get();

        return DataTables::of($data)
        ->addIndexColumn()
        ->make(true);
    }

    public function recent_item_requests(){
        $data = ItemDistribution::with('distributionable.ddd')
        ->latest('time')
        ->take(5)
        ->get();

        return DataTables::of($data)
        ->addIndexColumn()
        ->make(true);
    }

    public function activities(){
        $data = ActivityLog::with(['staff' => function ($query) {
            $query->select('id', 'staff_no', 'name');
        }])
        ->where('staff_id', auth()->id())
        ->latest()
        ->get();

        return DataTables::of($data)
        ->addIndexColumn()
        ->make(true);
    }
}
