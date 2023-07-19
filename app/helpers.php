<?php

use App\Models\Ddd;
use App\Models\Location;
use App\Models\Item;
use App\Models\DistributionItem;
use App\Models\Staff;
use App\Models\Office;
use App\Models\RequestCategory;
use Illuminate\Support\Facades\Hash;

function number_with_commas($num, $sign=true, $include_zero=false){
    if($num == 0) return ($include_zero ? (($sign ? '₦' : '').'0.00') : '');
    else return ($sign ? '₦' : '').number_format((float)$num, 2, '.', ',');
}
function integer_format($num, $include_zero=false){
    $num = (int)$num + 0;
    if($num == 0){
        return $include_zero ? '0' : '';
    }else{
        $tmp = explode('.', $num);
        if(strlen(@$tmp[1]) > 2){
            $dp = 2;
        }else{
            $dp = strlen(@$tmp[1]);
        }
        return number_format($num, $dp, '.', ',');
    }
}
function reverse_number_with_commas($num) {
    $num = str_replace('&#8358;', '', $num);
    return (float)preg_replace('/[^0-9.\-]/', '', $num);
}
function format_count($count){
    if($count > 0){
        return '<span class="badge bg-primary rounded-pill ms-1">'.$count.'</span>';
    }
    return '';
}
function format_label($label){
    if(in_array($label, ["Pending", "Blocked", "Allocated"])){
        return '<span class="badge bg-soft-warning text-warning">
            <span class="legend-indicator bg-warning"></span>' . $label .
        '</span>';
    }elseif(in_array($label, ["Escalated", "In-Progress", "Configured"])){
        return '<span class="badge bg-soft-primary text-primary">
            <span class="legend-indicator bg-primary"></span>' . $label .
        '</span>';
    }elseif(in_array($label, ["Resolved", "Active", "Now", "Installed", "Distributed"])){
        return '<span class="badge bg-soft-success text-success">
            <span class="legend-indicator bg-success"></span>' . $label .
        '</span>';
    }elseif(in_array($label, ["Unresolved", "Retired", "Returned"])){
        return '<span class="badge bg-soft-danger text-danger">
            <span class="legend-indicator bg-danger"></span>' . $label .
        '</span>';
    }
    return $label;
}
function format_date_time($timestamp){
    $timestamp = strtotime($timestamp);

    return '<h5>' . date('Y-m-d', $timestamp) . '</h5>' . date('h:i A', $timestamp);
}
function transform_time($timestamp){
    $timestamp = strtotime($timestamp);

    return '<h5>'. date('Y-m-d', $timestamp) . '</h5>' . date('h:i:s A', $timestamp);
}

function get_item_category_options($default = ""){
    $return = "";
    $options = ["Worktool", "Consumable"];
    if($default == "validate"){
        return $options;
    }
    foreach ($options as $value) {
        $return .= "<option ".($default == $value ? "selected":"").">$value</option>";
    }
    return $return;
}

function get_distribute_to_options($default = ""){
    $return = "";
    $options = ["Staff", "Office"];
    if($default == "validate"){
        return $options;
    }
    foreach ($options as $value) {
        $label = $value;
        if($default == "model"){
            $value = "App\\Models\\" . $value;
        }
        $return .= '<option value="'.$value.'" '.($default == $value ? 'selected':'').'>'.$label.'</option>';
    }
    return $return;
}

function get_returned_item_options($default = ""){
    $return = "";
    $options = [];

    $sel = DistributionItem::whereHas('last_distribution', function ($query) {
        $query->where('status', 'Returned');
    })
    ->get();
    foreach ($sel as $fet) {
        $return .= '<option value='.@$fet->id.' '.(@$fet->id == $default ? "selected" : "").'>'.@$fet->item->name.' - '.@$fet->item->model.' - '.@$fet->reference_no.'</option>';
        $options[] = @$fet->id;
    }
    if($default == "validate"){
        return $options;
    }
    return $return;
}

function get_item_options($default = ""){
    $return = "";
    $options = [];

    $sel = Item::select('id','name','model')
    ->orderBy('name')
    ->orderBy('model')
    ->get();
    foreach ($sel as $fet) {
        $return .= '<option value='.@$fet->id.' '.(@$fet->id == $default ? "selected" : "").'>'.@$fet->name.' - '.@$fet->model.'</option>';
        $options[] = @$fet->id;
    }
    if($default == "validate"){
        return $options;
    }
    return $return;
}

function get_item_name_options($default = ""){
    $return = "";
    $options = [
        "LAPTOP",
        "DESKTOP",
        "PRINTER",
        "IP TELEPHONE",
        "TONER (Black)",
        "TONER (Cyan)",
        "TONER (Margenta)",
        "TONER (Yellow)",
        "IPAD",
        "Others"
    ];
    sort($options);

    if($default == "validate"){
        return $options;
    }
    foreach ($options as $value) {
        $return .= "<option ".($default == $value ? "selected":"").">$value</option>";
    }
    return $return;
}

function get_request_category_options($args = []){
    $default = @$args['default'];
    $parent_id = @$args['parent_id'];

    $return = "";
    $options = [];

    $query = RequestCategory::orderByRaw("name = 'Others' ASC, name ASC");
    if($parent_id == ""){
        $query = $query->whereNull('parent_id');
    }else{
        $query = $query->where('parent_id', $parent_id);
    }

    $sel = $query->get();
    foreach ($sel as $fet) {
        $return .= '<option value='.@$fet->id.' '.(@$fet->id == $default ? "selected" : "").'>'.@$fet->name.'</option>';
        $options[] = @$fet->id;
    }
    if($default == "validate"){
        return $options;
    }
    return $return;
}

function get_request_status_options($default = ""){
    $return = "";
    $options = ["Escalated", "Resolved", "Unresolved"];
    if($default == "Filter"){
        array_unshift($options, "Pending");
    }

    if($default == "validate"){
        return $options;
    }
    foreach ($options as $value) {
        $return .= "<option ".($default == $value ? "selected":"").">$value</option>";
    }
    return $return;
}

function get_distribution_status_options($default = ""){
    $return = "";
    $options = ["Allocated", "Configured", "Installed", "Distributed"];
    if($default == "Filter"){
        $options[] = "Returned";
    }

    if($default == "validate"){
        return $options;
    }
    foreach ($options as $value) {
        $return .= "<option ".($default == $value ? "selected":"").">$value</option>";
    }
    return $return;
}

function get_distribution_condition_options($default = ""){
    $return = "";
    $options = ["New", "Returned"];

    if($default == "validate"){
        return $options;
    }
    foreach ($options as $value) {
        $return .= "<option ".($default == $value ? "selected":"").">$value</option>";
    }
    return $return;
}

function get_ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

function get_floor_options($default = ""){
    $return = "";
    $options = [];
    for ($i = 0; $i <= 15; $i++) {
        $value = "Floor";
        if($i == 0){
            $value = "Ground {$value}";
        }else {
            $value = get_ordinal($i) . " {$value}";
        }
        $return .= "<option ".($default == $value ? "selected":"").">$value</option>";
        $options[] = $value;
    }
    if($default == "validate"){
        return $options;
    }

    return $return;
}

function get_ddd_category_options($default = ""){
    $return = "";
    $options = ["Directorate", "Division", "Department"];
    if($default == "validate"){
        return $options;
    }
    foreach ($options as $value) {
        $return .= "<option ".($default == $value ? "selected":"").">$value</option>";
    }
    return $return;
}

function get_ddd_options($default = ""){
    $return = "";
    $options = [];

    $sel = Ddd::select('id','short','name')->orderBy('short')->get();
    foreach ($sel as $fet) {
        $return .= '<option value='.@$fet->id.' '.(@$fet->id == $default ? "selected" : "").'>'.@$fet->short.' - '.@$fet->name.'</option>';
        $options[] = @$fet->id;
    }
    if($default == "validate"){
        return $options;
    }
    return $return;
}

function get_location_options($default = ""){
    $return = "";
    $options = [];

    $sel = Location::select('id','name')->get();
    foreach ($sel as $fet) {
        $return .= '<option value='.@$fet->id.' '.(@$fet->id == $default ? "selected" : "").'>'.@$fet->name.'</option>';
        $options[] = @$fet->id;
    }
    if($default == "validate"){
        return $options;
    }
    return $return;
}

function get_account_status_options($default = ""){
    $return = "";
    $options = ["Active", "Blocked", "Retired"];
    if($default == "validate"){
        return $options;
    }
    foreach ($options as $value) {
        $return .= "<option ".($default == $value ? "selected":"").">$value</option>";
    }
    return $return;
}

function get_account_role_options($default = ""){
    $return = "";
    $options = [
        "Admin",
        "Inventory Admin",
        "Helpdesk Admin",
        "Helpdesk Staff",
        "Contract Staff",
        "Adhoc Staff",
        "Staff"
    ];

    if($default == "validate"){
        return $options;
    }
    foreach ($options as $value) {
        $return .= "<option ".($default == $value ? "selected":"").">$value</option>";
    }
    return $return;
}

function get_filter_form($selects = [], $type = ""){
    $div = '<form id="filter-form"><div class="row">';
    $i = 1;
    $role = auth()->user()->role;
    foreach($selects as $select){

        if(
            (@$select['label'] == "Staff" && $role == 'Staff') ||
            (@$select['label'] == "DDD" && in_array($role, ['Staff', 'DDD Admin'])) ||
            (@$select['label'] == "Floor" && $role != 'Admin')
        ){
            continue;
        }

        if(@$select['type'] != ""){
            if(in_array($select['type'], ["date", "time"])){
                $attrs = 'type="text" onfocus="(this.type=\''.$select['type'].'\')" onfocusout="(this.type=\'text\')"';
            }else{
                $attrs = 'type="'.$select['type'].'"';
            }
            $div .= '<div class="col-sm-6 mb-4">
                <small class="text-cap text-body">'.@$select['label'].'</small>
            <div class="tom-select-custom">
            <input '.$attrs.' data-target-column-name="'.@$select['name'].'" placeholder="'.@$select['label'].'" class="form-input" value="'.@$select['value'].'">';
        }else{
            $div .= '<div class="col-sm-6 mb-4">
                <small class="text-cap text-body">'.@$select['label'].'</small>
            <div class="tom-select-custom">
            <select
            data-target-column-name="'.@$select['name'].'" class="js-datatable-filter form-select form-select-sm">
                <option value="">-ALL-</option>
                '.$select['options'].'
            </select>';
        }
        $div .= '</div></div>';
        $i ++;
    }
    $div .= '</div>
        <div class="d-grid">
            <button class="btn btn-primary" type="reset">Reset</button>
        </div>
    </form>';
    return $div;
}

function get_office_options($args = []){
    $default = @$args['default'];
    $ddd_id = @$args['ddd_id'];

    $return = "";
    $options = [];

    $query = Office::query();

    if($ddd_id != ""){
        $query = $query->where('ddd_id', $ddd_id);
    }

    $sel = $query->orderBy('office_no')->get();
    foreach ($sel as $fet) {
        $return .= '<option value='.@$fet->id.' '.(@$fet->id == $default ? "selected" : "").'>'.@$fet->office_no.'</option>';
        $options[] = @$fet->id;
    }
    if($default == "validate"){
        return $options;
    }
    return $return;
}

function get_staff_options($args = []){
    $default = @$args['default'];
    $ddd_id = @$args['ddd_id'];

    $return = "";
    $options = [];

    $query = Staff::select('id','name','staff_no');

    if($ddd_id != ""){
        $query = $query->where('ddd_id', $ddd_id);
    }

    $sel = $query->orderBy('staff_no')->get();
    foreach ($sel as $fet) {
        $return .= '<option value='.@$fet->id.' '.(@$fet->id == $default ? "selected" : "").'>'.@$fet->staff_no.' - '.@$fet->name.'</option>';
        $options[] = @$fet->id;
    }
    if($default == "validate"){
        return $options;
    }
    return $return;
}

function get_report_type_options($default = ""){
    $role = auth()->user()->role;

    if(in_array($role, ["Admin", "Inventory Admin"])){
        $options = ["Helpdesk Supports", "Worktools Distribution", "Consumables Distribution"];
    } elseif(in_array($role, ["Helpdesk Admin", "Adhoc Staff"])){
        $options = ["Helpdesk Supports"];
    } else{
        $options = [];
    }
    $return = "";

    if($default == "validate"){
        return $options;
    }
    foreach ($options as $value) {
        $return .= "<option ".($default == $value ? "selected":"").">$value</option>";
    }
    return $return;
}

function get_report_by_options($default = ""){
    $return = "";
    $options = ["Categories", "Floors", "DDDs", "Staff"];
    if($default == "validate"){
        return $options;
    }
    foreach ($options as $value) {
        $return .= "<option ".($default == $value ? "selected":"").">$value</option>";
    }
    return $return;
}

/** QUERY FUNCTIONS */
function delete_catch_error($e, $label, $fks, $fk_value){
    if($e->errorInfo[0] == '23000'){
        $tables_list = get_foreign_tables_list($fks, $fk_value);
        if($tables_list == ""){
            $msg = 'Error occurred while deleting record';
        }else{
            $msg = "{$label} cannot be deleted because it is used in {$tables_list}";
        }

        return response()->json(['status' => 'danger', 'message' => $msg, 'error' => $e]);
    }
    error_log($e);

    return response()->json(['status' => 'danger', 'message' => 'Error occurred while deleting record', 'error' => $e]);
}

function get_foreign_tables($fks, $fk_value){
    return DB::table('information_schema.KEY_COLUMN_USAGE')
        ->select(array('table_name', 'column_name'))
        ->distinct()
        ->whereIn('column_name', $fks)
        ->where('table_schema', env('DB_DATABASE'))
        ->get();
}

function get_foreign_tables_list($fks, $fk_value){
    $tables = get_foreign_tables($fks, $fk_value);

    $tables_list = "";
    if(count($tables) == 0){
        return $tables_list;
    }

    $visited_tables = [];

    foreach ($tables as $table) {
        $count = DB::table($table->TABLE_NAME)
            ->where($table->COLUMN_NAME, $fk_value)->count();
        if($count > 0){
            if(!in_array($table->TABLE_NAME, $visited_tables)){
                if($tables_list != ""){
                    $tables_list .= ", ";
                }

                $tables_list .= $table->TABLE_NAME;
                $visited_tables[] = $table->TABLE_NAME;
            }
        }
    }
    if($tables_list != ""){
        $tables_list = preg_replace("/,([^,]+)$/", " and $1", strtoupper($tables_list));
    }
    return $tables_list;
}

function record_changed($model, $id, $form_data){
    $model = $model::find($id);
    $intersecting_columns = array_intersect_key($form_data, array_flip($model->getFillable()));

    $original_record = array_intersect_key($model->getOriginal(), $intersecting_columns);
    $new_record = array_intersect_key($form_data, $intersecting_columns);
    if(isset($new_record['time'])){
        $new_record['time'] = date("Y-m-d H:i:s", strtotime($new_record['time']));
    }

    $original_record = array_map(function ($value) {
        return is_numeric($value) ? intval($value) : $value;
    }, $original_record);

    $new_record = array_map(function ($value) {
        return is_numeric($value) ? intval($value) : $value;
    }, $new_record);

    ksort($original_record);
    ksort($new_record);

    if ($original_record !== $new_record){
        return true;
    }
    return false;
}

function soft_update($model, $id, $form_data, $user_id, $valid_until){
    $record = $model::find($id);
    if(@$record->status != "Pending"){
        $new_record = $record->replicate();
        $new_record->valid_until = $valid_until;
        $new_record->alter_staff_id = $user_id;
        $new_record->save();

        $form_data['valid_from'] = $valid_until;
        $form_data['alter_staff_id'] = NULL;
    }

    return $model::find($id)->update($form_data);
}

function soft_delete($model, $id, $user_id, $valid_until, $fks = [], $fk_value = ""){
    if(count($fks) > 0){
        $tables = get_foreign_tables($fks, $fk_value);
        foreach ($tables as $table) {
            $count = DB::table($table->TABLE_NAME)
                ->where($table->COLUMN_NAME, $fk_value)
                ->whereNull('valid_until')
                ->count();
            if($count > 0){
                DB::table($table->TABLE_NAME)
                ->where($table->COLUMN_NAME, $fk_value)
                ->update([
                    'alter_staff_id' => $user_id,
                    'valid_until' => $valid_until
                ]);
            }
        }
    }

    return $model::where('id', $id)
    ->update([
        'alter_staff_id' => $user_id,
        'valid_until' => $valid_until
    ]);
}

function analyse_query($query){
    $sqlQuery = $query->toSql();

    $before = memory_get_usage();
    $results = $query->get();
    $after = memory_get_usage();
    $memoryUsage = $after - $before;

    $start = microtime(true);
    $results = $query->get();
    $end = microtime(true);
    $executionTime = ($end - $start) * 1000;

    $response = response()->json($results);
    $response->header('X-Memory-Usage', $memoryUsage);
    $response->header('X-Execution-Time', $executionTime);

    return response()->json([
        'status' => 'success',
        'message' => "Memory-Usage: {$memoryUsage}<br>
                    X-Execution-Time: {$executionTime}"
    ]);
}

function check_route_access($route){
    $user = auth()->user();

    if ($user->isSuperAdmin() || in_array($route, get_authorized_routes($user))) {
        return true;
    }

    return false;
}

function get_authorized_routes($user){
    $role = $user->role;

    $common_routes = [
        '', 'recent-supports', 'recent-requests', 'profile', 'change-password',
        'item-requests.index', 'item-requests.show', 'item-requests.store',
        'helpdesk-requests.index', 'helpdesk-requests.show', 'helpdesk-requests.store',
        'logout', 'get-record'
    ];

    $specific_routes = [];
    if($role == 'Floor Admin'){
        $specific_routes = [
            'ddds.index',
            'staff.index', 'staff.show'
        ];
    }elseif($role == 'DDD Admin'){
        $specific_routes = [
            'staff.index', 'staff.show'
        ];
    }elseif(in_array($role, ["Helpdesk Admin", "Adhoc Staff"])){
        $specific_routes = [
            'helpdesk-requests.update', 'helpdesk-requests.delete',

            'helpdesk-supports.index', 'helpdesk-supports.show',
            'helpdesk-supports.update', 'helpdesk-supports.delete'
        ];
    }elseif($role == 'Inventory Admin'){
        $specific_routes = [
            'items.index', 'items.update', 'items.delete',
            'helpdesk-requests.update', 'helpdesk-requests.delete',
            'helpdesk-supports.index', 'helpdesk-supports.show',
            'helpdesk-supports.update', 'helpdesk-supports.delete',
            'inventory.index', 'inventory.show', 'inventory.update', 'inventory.delete',
            'item-distributions.index', 'item-distributions.show', 'item-distributions.update', 'item-distributions.delete'
        ];
    }elseif($role == 'Helpdesk Staff'){
        $specific_routes = [
            'helpdesk-supports.index', 'helpdesk-supports.show', 'helpdesk-supports.update'
        ];
    }

    return array_merge($common_routes, $specific_routes);
}

// Table cell template
function td_block_template($args = []){
    return '<h5>'. @$args[0] . '</h5>' . @$args[1];
}

function td_action_template($args = []){
    $return = '<button class="btn btn-primary btn-xs dropdown dropdown-toggle" type="button" id="dropdownMenuButtonWithIcon" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi-sliders"></i>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWithIcon">';

    if (check_route_access($base_url_name.'.update')){
        if(in_array($base_url_name, ['helpdesk-requests'])){
            $return .= '<a class="dropdown-item link-primary" href="$slot">
                <i class="bi-person dropdown-item-icon"></i> View Details
            </a>

            <a class="dropdown-item link-info" href="$slot">
                <i class="bi-clipboard-check dropdown-item-icon"></i> View supports
            </a>

            <a class="dropdown-item link-blue" type="button" data-bs-toggle="modal" data-bs-target="#register-modal" name="edit" $slot>
                <i class="bi-pencil-square dropdown-item-icon"></i> Edit Request
            </a>';
        } else {
            $return .= '<a class="dropdown-item link-primary" href="$slot">
                <i class="bi-person dropdown-item-icon"></i> View Request
            </a>
            <a class="dropdown-item link-info" type="button" data-bs-toggle="modal" data-bs-target="#register-modal" name="authorize" $slot>
                <i class="bi-clipboard-check dropdown-item-icon"></i> Authorize Request
            </a>';
        }

        $return .= '<a class="dropdown-item link-danger" type="button" name="delete" data-id="$slot">
            <i class="bi bi-trash dropdown-item-icon"></i> Delete Request
        </a>';
    }

    $return = '</div>';

    return $return;
}

function convertArrayToBcrypt(array $data){
    $hashedData = '';

    foreach ($data as $value) {
        $hashedValue = Hash::make($value);
        $hashedData .= $hashedValue . '<br>';
    }

    return $hashedData;
}
