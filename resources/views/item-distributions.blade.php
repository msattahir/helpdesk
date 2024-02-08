@php
    $label_p = "Item Distributions";
    $label_s = "Item Distribution";

    $base_url_name = "helpdesk-requests";
    $add_button_label = (check_route_access($base_url_name . '.delete') ? "New {$label_s}" : '');
@endphp

<x-layout :page_title="$label_p" :add_button_label="$add_button_label">
    <div class="card">
        <x-table-header>
            <x-filter>
            {!! get_filter_form([
                [
                    'name' => 'distributionable_type',
                    'label' => 'Category',
                    'options' => get_distribute_to_options('model')
                ],
                [
                    'div-id' => 'staff-div',
                    'name' => 'staff_id',
                    'label' => 'Staff',
                    'options' => get_staff_options()
                ],
                [
                    'div-id' => 'office-div',
                    'name' => 'office_id',
                    'label' => 'Office No',
                    'options' => get_office_options()
                ],
                [
                    'name' => 'ddd_id',
                    'label' => 'DDD',
                    'options' => get_ddd_options()
                ],
                [
                    'name' => 'ddd_floor',
                    'label' => 'Floor',
                    'options' => get_floor_options()
                ],
                [
                    'name' => 'item_id',
                    'label' => 'Item',
                    'options' => get_item_options()
                ],
                [
                    'name' => 'status',
                    'label' => 'Status',
                    'options' => get_distribution_status_options("Filter")
                ],
                [
                    'name' => 'date_from',
                    'type' => 'date',
                    'label' => 'Date From'
                ],
                [
                    'name' => 'date_to',
                    'type' => 'date',
                    'label' => 'Date To'
                ],
                [
                    'name' => 'allocation_period',
                    'label' => 'Allocation Period',
                    'options' => get_allocation_period_options(),
                    'custom-filter' => true,
                ]
            ]) !!}
            </x-filter>
        </x-table-header>
        <div id="response" class="m-2"></div>
        <x-table/>

        <x-table-footer/>
    </div>
    <x-delete-modal/>
</x-layout>

<table id="template-table" style="display: none;">
    <td id="t-block">
        <span class="d-block h5 text-inherit mb-0">$slot</span>
        $slot
    </td>
    <td id="t-item">
        <span class="d-block h5 text-inherit mb-0">$slot</span>
        <span class="d-block fs-5 text-body">$slot</span>
        <span class="d-block h5 text-inherit mb-0">$slot</span>
    </td>
    <td id="t-action">
        @if(check_route_access($base_url_name . '.delete'))
            @php
                $attrs = 'data-id="row.id"
                    data-ddd_id="row.ddd_id"
                    data-model_id="row.distributionable_id"
                    data-model_type="row.distributionable_type"
                    data-item_id="row.item_id"
                    data-reference_no="row.item_reference_no"
                    data-distribution_item_id="row.distribution_item_id"
                    data-remark="row.remark"
                    data-status="row.status"
                    data-time="row.time"';
            @endphp

            <button class="btn btn-primary btn-xs dropdown dropdown-toggle" type="button" id="dropdownMenuButtonWithIcon" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi-sliders"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWithIcon">
                <a class="dropdown-item link-blue" type="button" data-bs-toggle="modal" data-bs-target="#register-modal" name="edit" {!! $attrs !!}>
                    <i class="bi-pencil-square dropdown-item-icon"></i> Edit Distribution
                </a>
                <a class="dropdown-item link-primary" type="button" name="return" data-id="row.id">
                    <i class="bi bi-arrow-return-left dropdown-item-icon"></i> Return Item
                </a>
                <a class="dropdown-item link-danger" type="button" name="delete" data-id="row.id">
                    <i class="bi bi-trash dropdown-item-icon"></i> Delete Distribution
                </a>
            </div>
        @else
            <button class="btn btn-primary btn-xs" type="button" name="return" data-id="row.id">
                <i class="bi-arrow-return-left"></i> Return Item
            </button>
        @endif
    </td>
</table>

<div class="modal fade" id="register-modal" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <form id="register-form">
            @method('POST')

            <div class="modal-header">
                <h5 class="modal-title h4" id="modal-title">New {{$label_s}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-response"></div>
                <input type="hidden" name="id">
                <input type="hidden" name="operation">

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">DDD</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="ddd_id" required>
                            <option value="" selected disabled>Select DDD</option>
                            {!!get_ddd_options()!!}
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Distribute To</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="distribute_to" required>
                            <option value="" selected disabled>Staff/Office</option>
                            {!!get_distribute_to_options()!!}
                        </select>
                    </div>
                </div>
                <div id="distribute-to"></div>
                <hr>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Item Condition</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="item_condition" required>
                            <option value="" selected disabled>Select Item Condition</option>
                            {!!get_distribution_condition_options()!!}
                        </select>
                    </div>
                </div>
                <div id="item-condition"></div>
                <hr>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Remark</label>
                    </div>
                    <div class="col-sm-8">
                        <textarea class="form-control" name="remark" placeholder="Remark(Optional)" rows="5" maxlength="255"></textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Status</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="status" required>
                            <option value="" selected disabled>Select Status ...</option>
                            {!!get_distribution_status_options("Allocated")!!}
                        </select>
                    </div>
                </div>
                <x-datetime-field/>

            </div>
            <div class="modal-footer">
                <button class="col-sm-3 btn btn-primary" type="submit" id="submit-btn">Submit</button>
            </div>

        </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    initialize_datatable('#records-table',
    columns = [
        {title: '#', data: 'DT_RowIndex', orderable: false, searchable: false},
        {
            title: 'Cat.',
            name: 'category',
            data: 'category',
            className: 'text-nowrap',
        },
        {name: 'distributionable_type', data: 'distributionable_type', visible: false},

        {
            title: 'Ref.',
            name: 'receiver_no',
            className: 'text-nowrap',
            render: function (data, type, row){
                return replace_slots($('#t-block').html(), [
                    row.receiver_no, row.receiver_name
                ]);
            }
        },
        {data: 'staff_id', name: 'staff_id', visible: false},
        {data: 'office_id', name: 'office_id', visible: false},
        {name: 'receiver_name', data: 'receiver_name', visible: false},

        {
            title: 'DDD',
            name: 'ddd_name',
            className: 'text-nowrap',
            render: function (data, type, row){
                return replace_slots($('#t-block').html(), [
                    row.ddd_name, row.ddd_location
                ]);
            }
        },
        {data: 'ddd_id', name: 'ddd_id', visible: false},
        {data: 'ddd_floor', name: 'ddd_floor', visible: false},
        {data: 'ddd_location', name: 'ddd_location', visible: false},

        {
            title: 'Item',
            name: 'item_name',
            className: 'text-nowrap',
            render: function (data, type, row){
                return replace_slots($('#t-item').html(), [
                    row.item_name,
                    row.item_model,
                    row.item_reference_no
                ]);
            }
        },
        {data: 'item_id', name: 'item_id', visible: false},
        {data: 'item_model', name: 'item_model', visible: false},
        {data: 'item_reference_no', name: 'item_reference_no', visible: false},

        {
            title: 'Status',
            data: 'status',
            name: 'status',
            render: function (data, type, row){
                return format_label(row.status);
            }
        },
        {title: 'Remark', className: 'text-nowrap', data: 'remark', width: '20px'},
        {
            title: 'Date/Time',
            data: 'time',
            name: 'time',
            className: 'text-nowrap',
            render: function (data, type, row){
                return format_date_time(row.time);
            },
            searchable: false
        },
        {
            title: 'Action',
            render: function (data, type, row){
                return replace_template_values($('#t-action').html(), row);
            },
            orderable: false,
            searchable: false,
            className: 'no-export'
        },
    ]);

    manage_records({url : '/item-distributions'});

    $('#register-modal').on('show.bs.modal', function (e) {
        var opener=e.relatedTarget;
        $('#register-form').trigger("reset");
        $('#modal-response').html("");

        var $this = $('#register-form');

        if(opener.name == "edit"){
            $("#register-modal #modal-title").html("Update <?=@$label_s?>");
            $this.find('[name="_method"]').val('PUT');
            $this.find('[name="operation"]').val(opener.name);

            $this.find('[name="id"]').val($(opener).data('id'));
            $this.find('[name="ddd_id"]').val($(opener).data('ddd_id'));
            $this.find('[name="item_id"]').val($(opener).data('item_id'));
            $this.find('[name="reference_no"]').val($(opener).data('reference_no'));
            $this.find('[name="remark"]').val($(opener).data('remark'));
            $this.find('[name="status"]').val($(opener).data('status'));
            $this.find('[name="time"]').val(format_date_time_local($(opener).data('time')));

            if($(opener).data('model_type') == 'App\\Models\\Staff'){
                var distribute_to = "Staff";
            }else{
                var distribute_to = "Office";
            }
            $this.find('[name="distribute_to"]').val(distribute_to);
            $this.find('[name="distribute_to"]').trigger("change", function(){
                $this.find('[name="ddd_id"]').trigger("change", function(){
                    $this.find('[name="model_id"]').val($(opener).data('model_id'));
                    original_form = $this.serialize();
                });
            });

            $this.find('[name="item_condition"]').val('New');
            $this.find('[name="item_condition"]').trigger("change", function(){
                $this.find('[name="item_id"]').val($(opener).data('item_id'));
                $this.find('[name="reference_no"]').val($(opener).data('reference_no'));
            });

            $("#submit-btn").html("Update");
        }else{
            $("#register-modal #modal-title").html("New <?=@$label_s?>");
            $this.find('[name="_method"]').val('POST');
            $this.find('[name="id"]').val('');

            $("#submit-btn").html("Submit");
        }
        original_form = $this.serialize();
    });

    $(document).on('click', '[name="return"]', function(){
        var _id = $(this).data('id');
        var $form = $('#register-form');

        $form.find('[name="_method"]').val('PUT');
        $form.find('[name="operation"]').val('return');

        $form.find('[name="id"]').val(_id);
        $form.find('[name="ddd_id"]').val("");
        $form.find('[name="distribute_to"]').val("");
        $form.find('[name="item_condition"]').val("");
        $form.find('[name="remark"]').val("");
        $form.find('[name="status"]').val("");
        $form.find('[name="time"]').val("");

        $form.trigger('submit');
    });

    var ts_model_id;

    $(document).on('change', '#register-form [name="distribute_to"]', function(e, callback){
        if($(this).val() == "Staff"){
            $('#distribute-to').html(`<div class="row mb-3">
                <div class="col-sm-4">
                    <label class="form-label">Staff</label>
                </div>
                <div class="col-sm-8">
                    <select class="form-select" name="model_id" required>
                        <option value="" selected disabled>Select Staff</option>
                    </select>
                </div>
            </div>`);
        }else{
            $('#distribute-to').html(`<div class="row mb-3">
                <div class="col-sm-4">
                    <label class="form-label">Office</label>
                </div>
                <div class="col-sm-8">
                    <select class="form-select" name="model_id" required>
                        <option value="" selected disabled>Select Office</option>
                    </select>
                </div>
            </div>`);
        }
        ts_model_id = new TomSelect('#register-form [name="model_id"]');

        if (typeof callback === "function") callback();
        else $('#register-form').find('[name="ddd_id"]').trigger("change");
    });

    $(document).on('change', '#register-form [name="item_condition"]', function(e, callback){
        if($(this).val() == "New"){
            $('#item-condition').html(`<div class="row mb-3">
                <div class="col-sm-4">
                    <label class="form-label">Item</label>
                </div>
                <div class="col-sm-8">
                    <select class="form-select" name="item_id" required>
                        <option value="" selected disabled>Select Item</option>
                        {!!get_item_options()!!}
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-4">
                    <label class="form-label">Reference No</label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="form-control validate-uppercase" name="reference_no" placeholder="Reference No" maxlength="255" required/>
                </div>
            </div>`);
        }else{
            $('#item-condition').html(`<div class="row mb-3">
                <div class="col-sm-4">
                    <label class="form-label">Returned Item</label>
                </div>
                <div class="col-sm-8">
                    <select class="form-select" name="distribution_item_id" required>
                        <option value="" selected disabled>Select Returned Item</option>
                        {!!get_returned_item_options()!!}
                    </select>
                </div>
            </div>`);
        }
        if (typeof callback === "function") callback();
    });

    $(document).on('change', '#register-form [name="ddd_id"]', function (e, callback) {
        var distribute_to = $('#register-form [name="distribute_to"]').val();
        if(distribute_to == "Staff"){
            var url = '/get-options/staff/';
        }else if(distribute_to == "Office"){
            var url = '/get-options/office/';
        }else{
            return;
        }

        var $div = $('#register-form [name="model_id"]');
        $div.find('option:not(:first)').remove();
        var _id = $(this).val();
        Pace.restart();
        Pace.track(function(){
            $.ajax({
                url: url + _id,
                method: 'GET',
                success: function(data) {
                    $div.append(data.record);
                    ts_model_id.clear();
                    ts_model_id.clearOptions();
                    ts_model_id.sync();
                    if (typeof callback === "function")
                        callback();
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
    });

    $('#office-div').hide();
    $(document).on('change', '#filter-form [name="distributionable_type"]', function(e){
        if($(this).val() == "App\\Models\\Office"){
            $('#staff-div').hide();
            $('#office-div').show();

            $('#filter-form [name="staff_id"]').val('');
            datatable.column('staff_id:name').search('').draw();
        }else{
            $('#staff-div').show();
            $('#office-div').hide();

            $('#filter-form [name="office_id"]').val('');
            datatable.column('office_id:name').search('').draw();
        }
    });

    $(document).on('change', '#filter-form [name="date_from"]', function(e){
        if($(this).val() == ""){
            date_from = "";
            $('#filter-form [name="date_to"]').attr('min', '2010-04-22');
        }else{
            date_from = $(this).val() + ' 00:00:00';
            $('#filter-form [name="date_to"]').attr('min', $(this).val());
        }
        datatable.ajax.reload();
        update_filter_count();
    });

    $(document).on('change', '#filter-form [name="date_to"]', function(e){
        var today = new Date();
        if($(this).val() == ""){
            date_to = "";
            $('#filter-form [name="date_from"]').attr('max', today);
        }else{
            date_to = $(this).val() + ' 23:59:59';
            $('#filter-form [name="date_from"]').attr('max', $(this).val());
        }
        datatable.ajax.reload();
        update_filter_count();
    });

    $(document).on('change', '#filter-form [name="allocation_period"]', function(e){
        if($(this).val() == ""){
            allocation_period = "";
        }else{
            allocation_period = $(this).val();
        }

        datatable.ajax.reload();
        update_filter_count();
    });
});
</script>
