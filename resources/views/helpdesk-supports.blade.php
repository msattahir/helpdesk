@php
    $label_p = "Helpdesk Supports";
    $label_s = "Helpdesk Support";

    $base_url_name = "helpdesk-supports";
@endphp

<x-layout :page_title="$label_p">
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
                    'name' => 'request.ddd.id',
                    'label' => 'DDD',
                    'options' => get_ddd_options()
                ],
                [
                    'name' => 'request.ddd.floor',
                    'label' => 'Floor',
                    'options' => get_floor_options()
                ],
                [
                    'name' => 'status',
                    'label' => 'Status',
                    'options' => get_request_status_options("Filter"),
                    'custom-filter' => true
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
                data-status="row.status"
                data-remark="row.remark"
                data-time="row.time"';
        @endphp
        @if(check_route_access($base_url_name . '.delete'))
            <button class="btn btn-primary btn-xs dropdown dropdown-toggle" type="button" id="dropdownMenuButtonWithIcon" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi-sliders"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWithIcon">
                <a class="dropdown-item link-primary" href="/helpdesk-requests/row.request.id">
                    <i class="bi-headset dropdown-item-icon"></i> View Request
                </a>
                <a class="dropdown-item link-info" type="button" data-bs-toggle="modal" data-bs-target="#register-modal" name="authorize" {!! $attrs !!}>
                    <i class="bi-gear dropdown-item-icon"></i> Authorize Request
                </a>
                <a class="dropdown-item link-danger" type="button" name="delete" data-id="row.id">
                    <i class="bi bi-trash dropdown-item-icon"></i> Delete Request
                </a>
            </div>
        @else
            <div data-status="Pending">
                <button class="btn btn-primary btn-xs" type="button" name="start" data-id="row.id">
                    <i class="bi-play-circle"></i> Start
                </button>
            </div>
            <div data-status="In-Progress">
                <button class="btn btn-primary btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#register-modal" name="authorize" {!! $attrs !!} >
                    <i class="bi-gear"></i> Authorize
                </button>
            </div>
            <div data-status="Closed">
                <button class="btn btn-secondary disabled btn-xs" type="button">
                    <i class="bi-check-circle"></i> Authorized
                </button>
            </div>
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
                        <label class="form-label">Status</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="status" required>
                            <option value="" selected disabled>Select Status ...</option>
                            {!!get_request_status_options("Completed")!!}
                        </select>
                    </div>
                </div>
                <div id="escalate-to"></div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Remark</label>
                    </div>
                    <div class="col-sm-8">
                        <textarea class="form-control" name="remark" placeholder="Remark(Optional)" rows="5" maxlength="255"></textarea>
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
    var base_url = '/{{ $base_url_name }}';

    initialize_datatable('#records-table', columns = [
        {title: '#', data: 'DT_RowIndex', orderable: false, searchable: false},
        {
            title: 'Assign To',
            data: 'staff.staff_no',
            name: 'staff.staff_no',
            render: function (data, type, row){
                return replace_slots($('#t-block').html(), [
                    row.staff.staff_no, row.staff.name
                ]);
            }
        },
        {data: null, name: 'staff.name', visible: false},
        {data: null, name: 'staff.id', visible: false},

        {
            title: 'DDD',
            data: 'request.ddd.short',
            name: 'request.ddd.short',
            className: 'text-nowrap',
            render: function (data, type, row){
                return replace_slots($('#t-block').html(), [
                    row.request.ddd.short, row.request.ddd.floor
                ]);
            }
        },
        {data: 'request.ddd.id', name: 'request.ddd.id', visible: false},
        {data: 'request.ddd.floor', name: 'request.ddd.floor', visible: false},

        {
            title: 'Category',
            data: 'request.request_category.parent.name',
            name: 'request.request_category.parent.name',
            render: function (data, type, row){
                return replace_slots($('#t-block').html(), [
                    row.request.request_category.parent.name, row.request.request_category.name
                ]);
            }
        },

        {title: 'Description', data: 'request.description', width: '20px'},
        {title: 'Remark', data: 'remark', width: '20px'},

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
                var $this = $('#t-action');
                if($this.data('select') == 'status'){
                    if(row.status == "Pending"){
                        $this = $this.find('[data-status="Pending"]');
                    }else if(row.status == "In-Progress"){
                        $this = $this.find('[data-status="In-Progress"]');
                    }else{
                        $this = $this.find('[data-status="Closed"]');
                    }
                }
                return replace_template_values($this.html(), row);
            },
            orderable: false,
            searchable: false,
            className: 'no-export'
        },
    ]);

    manage_records({url : base_url});

    $('#register-modal').on('show.bs.modal', function (e) {
        var opener=e.relatedTarget;
        $('#register-form').trigger("reset");
        $('#modal-response').html("");
        var $this = $('#register-form');

        $('#register-form [name="status"]').trigger("change");
        if(opener.name == "authorize"){
            $("#register-modal #modal-title").html("Update <?=@$label_s?>");
            $this.find('[name="_method"]').val('PUT');
            $this.find('[name="operation"]').val(opener.name);

            var _id = $(opener).data('id');
            $this.find('[name="id"]').val($(opener).data('id'));
            $this.find('[name="status"]').val($(opener).data('status'));
            $this.find('[name="remark"]').val($(opener).data('remark'));
            $this.find('[name="time"]').val(format_date_time_local($(opener).data('time')));

            if($(opener).data('status') == "Escalated"){
                $('#register-form [name="status"]').trigger("change");
                $.ajax({
                    url: base_url + '/' + _id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        if(!$.isEmptyObject(data)){
                            $this.find('[name="escalate_staff_id"]').val(data.staff_id);
                        }
                    }
                });
            }

            $("#submit-btn").html("Update");
        }else{
            $("#register-modal #modal-title").html("New <?=@$label_s?>");
            $this.find('[name="_method"]').val('POST');

            $("#submit-btn").html("Submit");
        }
        original_form = $('#register-form').serialize();
    });

    $(document).on('click', '[name="start"]', function(){
        var _id = $(this).data('id');
        var $form = $('#register-form');

        $form.find('[name="_method"]').val('PUT');
        $form.find('[name="operation"]').val('start');

        $form.find('[name="id"]').val(_id);
        $form.find('[name="status"]').val("");
        $form.find('[name="remark"]').val("");
        $form.find('[name="time"]').val("");

        $form.trigger('submit');
    });

    $(document).on('change', '#register-form [name="status"]', function(){
        if($(this).val() == "Escalated"){
            $('#escalate-to').html(`<div class="row mb-3">
                <div class="col-sm-4">
                    <label class="form-label">Escalate To</label>
                </div>
                <div class="col-sm-8">
                    <select class="form-select" name="escalate_staff_id" required>
                        <option value="" selected disabled>Select Staff</option>
                        {!!get_staff_options(['ddd_id' => auth()->user()->ddd_id])!!}
                    </select>
                </div>
            </div>`);
        }else{
            $('#escalate-to').html('');
        }
    });

    $(document).on('change', '.custom-filter', function(e){
        datatable.ajax.reload();
    });
});
</script>
