@php
    $label_p = "Helpdesk Requests";
    $label_s = "Helpdesk Request";

    $base_url_name = "helpdesk-requests";
    $add_button_label = (check_route_access($base_url_name . '.store') ? "New {$label_s}" : '');
@endphp

<x-layout :page_title="$label_p" :add_button_label="$add_button_label">

    @if(check_route_access($base_url_name . '.delete'))
    <div class="row align-items-center mb-2">
        <div class="col"></div>
        <div class="col-sm-auto pull-right">
            <a class="btn btn-xs btn-white" href="/helpdesk-supports">
                <i class="bi-list me-1"></i> View All Supports
            </a>
        </div>
    </div>
    @endif

    <div class="card">
        <x-table-header>

            <x-filter>
            {!! get_filter_form([
                [
                    'name' => 'staff.id',
                    'label' => 'Staff',
                    'options' => get_staff_options()
                ],
                [
                    'name' => 'ddd.id',
                    'label' => 'DDD',
                    'options' => get_ddd_options()
                ],
                [
                    'name' => 'ddd.floor',
                    'label' => 'Floor',
                    'options' => get_floor_options()
                ],
                [
                    'name' => 'request_category.parent.id',
                    'label' => 'Request Category',
                    'options' => get_request_category_options()
                ],
                [
                    'name' => 'status',
                    'label' => 'Status',
                    'options' => get_request_status_options("Filter")
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
    <td id="t-action" {{ (check_route_access($base_url_name . '.delete') ? '' : 'data-select=status') }}>
        @php
            $attrs = 'data-id="row.id"
                data-ddd_id="row.ddd_id"
                data-staff_id="row.staff_id"
                data-parent_category_id="row.request_category.parent.id"
                data-request_category_id="row.request_category.id"
                data-description="row.description"
                data-support_staff_id="row.first_support.staff_id"';
        @endphp
        @if(check_route_access($base_url_name . '.delete'))
            <button class="btn btn-primary btn-xs dropdown dropdown-toggle" type="button" id="dropdownMenuButtonWithIcon" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi-sliders"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWithIcon">
                <a class="dropdown-item link-primary" href="/{{ $base_url_name }}/row.id">
                    <i class="bi-headset dropdown-item-icon"></i> View Details
                </a>
                <a class="dropdown-item link-info" href="/helpdesk-supports?request=row.id">
                    <i class="bi-clipboard-check dropdown-item-icon"></i> View supports
                </a>
                <a class="dropdown-item link-blue" type="button" data-bs-toggle="modal" data-bs-target="#register-modal" name="edit" {!! $attrs !!}>
                    <i class="bi-pencil-square dropdown-item-icon"></i> Edit Request
                </a>
                <a class="dropdown-item link-danger" type="button" name="delete" data-id="row.id">
                    <i class="bi bi-trash dropdown-item-icon"></i> Delete Request
                </a>
            </div>
        @else
            <a class="btn btn-primary btn-xs" type="button" href="/{{ $base_url_name }}/row.id" >
                <i class="bi-headset"></i> Details
            </a>
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
                        <label class="form-label">Requested by</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="staff_id" required>
                            <option value="" selected disabled>Select Staff</option>
                        </select>
                    </div>
                </div>

                <hr>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Category</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="parent_category_id" required>
                            <option value="" selected disabled>Select Category</option>
                            {!!get_request_category_options()!!}
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Sub-Category</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="request_category_id" required>
                            <option value="" selected disabled>Select Sub-Category</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Description</label>
                    </div>
                    <div class="col-sm-8">
                        <textarea class="form-control" name="description" placeholder="Description(Optional)" rows="5" maxlength="200"></textarea>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Assign To</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="support_staff_id" required>
                            <option value="" selected disabled>Select Staff</option>
                            {!!get_staff_options(['ddd_id' => auth()->user()->ddd_id])!!}
                        </select>
                    </div>
                </div>
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
    var base_url = '/{{ $base_url_name }}';

    initialize_datatable('#records-table', columns = [
        {title: '#', data: 'DT_RowIndex', orderable: false, searchable: false},
        {
            title: 'Staff',
            data: 'staff.staff_no',
            name: 'staff.staff_no',
            render: function (data, type, row){
                var template = '<a href="/{{ $base_url_name }}/' + row.id + '">' +
                    $('#t-block').html() +
                '</a>';

                return replace_slots(template, [
                    row.staff.staff_no, row.staff.name
                ]);
            }
        },
        {data: 'staff.id', name: 'staff.id', visible: false},
        {data: 'staff.name', name: 'staff.name', visible: false},

        {
            title: 'DDD',
            data: 'ddd.short',
            name: 'ddd.short',
            className: 'text-nowrap',
            render: function (data, type, row){
                let location = row.staff.location.name;
                if(row.staff.location.id == 1){
                    location = row.ddd.floor;
                }
                return replace_slots($('#t-block').html(), [
                    row.ddd.short, location
                ]);
            }
        },
        {data: 'ddd.id', name: 'ddd.id', visible: false},
        {data: 'ddd.floor', name: 'ddd.floor', visible: false},

        {
            title: 'Category',
            data: 'request_category.parent.name',
            name: 'request_category.parent.name',
            render: function (data, type, row){
                return replace_slots($('#t-block').html(), [
                    row.request_category.parent.name, row.request_category.name
                ]);
            }
        },
        {data: 'request_category.parent.id', name: 'request_category.parent.id', visible: false},
        {data: 'request_category.name', name: 'request_category.name', visible: false},

        {title: 'Description', data: 'description', width: '20px'},
        {
            title: 'Assign To',
            data: 'first_support.staff.staff_no',
            name: 'first_support.staff.staff_no',
            render: function (data, type, row){
                var template = '<a href="/helpdesk-supports?request=' + row.id + '">' +
                    $('#t-block').html() +
                '</a>';

                return replace_slots(template, [
                    row.first_support.staff.staff_no, row.first_support.staff.name
                ]);
            }
        },
        {data: 'first_support.staff.name', name: 'first_support.staff.name', visible: false},

        {
            title: 'Status',
            data: 'status',
            name: 'status',
            render: function (data, type, row){
                return format_label(row.status);
            }
        },
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
            searchable: false
        },
    ]);

    manage_records({url : base_url});

    $('#register-modal').on('show.bs.modal', function (e) {
        var opener=e.relatedTarget;
        $('#register-form').trigger("reset");
        $('#modal-response').html("");

        if(opener.name == "edit"){
            $("#register-modal #modal-title").html("Update {{ $label_s }}");
            $('#register-form').find('[name="_method"]').val('PUT');

            var $this = $('#register-form');
            $this.find('[name="id"]').val($(opener).data('id'));
            $this.find('[name="ddd_id"]').val($(opener).data('ddd_id'));
            $this.find('[name="ddd_id"]').trigger("change", function(){
                $this.find('[name="staff_id"]').val($(opener).data('staff_id'));
                original_form = $('#register-form').serialize();
            });

            $this.find('[name="parent_category_id"]').val($(opener).data('parent_category_id'));
            $this.find('[name="parent_category_id"]').trigger("change", function(){
                $this.find('[name="request_category_id"]').val($(opener).data('request_category_id'));
                original_form = $('#register-form').serialize();
            });

            $this.find('[name="description"]').val($(opener).data('description'));
            $this.find('[name="support_staff_id"]').val($(opener).data('support_staff_id'));

            $("#submit-btn").html("Update");
        }else{
            $("#register-modal #modal-title").html("New {{ $label_s }}");
            $('#register-form').find('[name="_method"]').val('POST');

            $("#submit-btn").html("Submit");
        }
        original_form = $('#register-form').serialize();
    });

    var ts_staff_id = new TomSelect('#register-form [name="staff_id"]');

    $(document).on('change', '#register-form [name="parent_category_id"], #register-form [name="ddd_id"]', function (e, callback) {
        if($(this).prop('name') == 'parent_category_id'){
            var $div = $('#register-form [name="request_category_id"]');
            var url = '/get-options/request-sub-cat/';
        }else{
            var $div = $('#register-form [name="staff_id"]');
            var url = '/get-options/staff/';
        }
        $div.find('option:not(:first)').remove();
        var _id = $(this).val();
        Pace.restart();
        Pace.track(function(){
            $.ajax({
                url: url + _id,
                method: 'GET',
                success: function(data) {
                    $div.append(data.record);

                    if(url == '/get-options/staff/'){
                        ts_staff_id.clear();
                        ts_staff_id.clearOptions();
                        ts_staff_id.sync();
                    }
                    if (typeof callback === "function")
                        callback();
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
    });
});
</script>
