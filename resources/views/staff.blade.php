@php
    $label_p = "Staff";
    $label_s = "Staff";

    $base_url_name = "staff";
    $add_button_label = (check_route_access($base_url_name . '.store') ? "New {$label_s}" : '');
@endphp

<x-layout :page_title="$label_p" :add_button_label="$add_button_label">
    <div class="card">
        <x-table-header>

            <x-filter>
            {!! get_filter_form([
                [
                    'name' => 'ddd.id',
                    'label' => 'DDD',
                    'options' => get_ddd_options()
                ],
                [
                    'name' => 'location.id',
                    'label' => 'Location',
                    'options' => get_location_options()
                ],
                [
                    'name' => 'role',
                    'label' => 'Role',
                    'options' => get_account_role_options()
                ],
                [
                    'name' => 'status',
                    'label' => 'Status',
                    'options' => get_account_status_options()
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
    <td id="t-ddd">
        <span class="d-block h5 text-inherit mb-0">$slot</span>
        $slot
        <span class="d-block h5 text-inherit mb-0">$slot</span>
    </td>
    <td id="t-name">
        <a class="d-flex align-items-center" href="/{{ $base_url_name }}/$slot">
            <div class="avatar avatar-soft-primary avatar-circle">
                <span class="avatar-initials">$slot</span>
            </div>
            <div class="ms-3">
                <span class="d-block h5 text-inherit mb-0">$slot</span>
                <span class="d-block fs-5 text-body">$slot</span>
            </div>
        </a>
    </td>
    <td id="t-action">
        <button class="btn btn-primary btn-xs dropdown dropdown-toggle" type="button" id="dropdownMenuButtonWithIcon" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi-sliders"></i>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWithIcon">
            <a class="dropdown-item link-primary" href="/{{ $base_url_name }}/row.id">
                <i class="bi-person dropdown-item-icon"></i> View Profile
            </a>

            @if(check_route_access($base_url_name . '.update'))
                @php
                $attrs = 'data-id="row.id"
                    data-staff_no="row.staff_no"
                    data-name="row.name"
                    data-email="row.email"
                    data-ddd_id="row.ddd_id"
                    data-location_id="row.location_id"
                    data-role="row.role"
                    data-status="row.status"';
                @endphp
                <a class="dropdown-item link-blue" type="button" data-bs-toggle="modal" data-bs-target="#register-modal" name="edit" {!! $attrs !!}>
                    <i class="bi-pencil-square dropdown-item-icon"></i> Edit Staff
                </a>
                <a class="dropdown-item link-info" type="button" data-bs-toggle="modal" data-bs-target="#register-modal" name="reset-password" {!! $attrs !!}>
                    <i class="bi-braces-asterisk dropdown-item-icon"></i> Password Reset
                </a>
                <a class="dropdown-item link-danger" type="button" name="delete" data-id="row.id">
                    <i class="bi bi-trash dropdown-item-icon"></i> Delete Staff
                </a>
            @endif
        </div>
    </td>
</table>

<div class="modal fade" id="register-modal" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <form id="register-form">
            @method('POST')

            <div class="modal-header">
                <h5 class="modal-title h4" id="modal-title">Add {{$label_s}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-response"></div>
                <input type="hidden" name="operation">
                <input type="hidden" name="id">

                <div class="reset-password-remove">
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <label class="form-label">Staff No</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="staff_no" class="form-control validate-staff-no" placeholder="Staff No" required maxlength="5">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <label class="form-label">Name</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="name" class="form-control" placeholder="Name" required maxlength="100">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <label class="form-label">Email</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="email" class="form-control" placeholder="Email" required maxlength="100">
                        </div>
                    </div>
                    <hr>
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
                            <label class="form-label">Location</label>
                        </div>
                        <div class="col-sm-8">
                            <select class="form-select" name="location_id" required>
                                <option value="" selected disabled>Select Location</option>
                                {!!get_location_options()!!}
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <label class="form-label">Role</label>
                        </div>
                        <div class="col-sm-8">
                            <select class="form-select" name="role" required>
                                <option value="" selected disabled>Select Role</option>
                                {!!get_account_role_options()!!}
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <label class="form-label">Status</label>
                        </div>
                        <div class="col-sm-8">
                            <select class="form-select" name="status" required>
                                <option value="" selected disabled>Select Status</option>
                                {!!get_account_status_options("Active")!!}
                            </select>
                        </div>
                    </div>
                </div>

                <div class="reset-password-remove edit-remove">
                    <hr>
                </div>

                <div class="edit-remove">
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <label class="form-label">New Password</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="password" name="password" class="form-control" placeholder="New Password" required maxlength="40" minlength="6">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <label class="form-label">Confirm Password</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required maxlength="40">
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
        {title: '#', data: 'DT_RowIndex', width: '1px', orderable: false, searchable: false},
        {
            title: 'Staff No',
            data: 'staff_no',
            name: 'staff_no',
            className: 'text-nowrap',
            render: function (data, type, row){
                return '<h5>' + row.staff_no + '</h5>';
            }
        },
        {
            title: 'Name',
            data: 'name',
            name: 'name',
            render: function (data, type, row){
                return replace_slots($('#t-name').html(), [
                    row.id, row.name[0], row.name, row.email
                ]);
            }
        },
        {name: 'email', data: 'email', visible: false},

        {
            title: 'DDD/Location',
            data: 'ddd.short',
            name: 'ddd.short',
            className: 'text-nowrap',
            render: function (data, type, row){
                return replace_slots($('#t-ddd').html(), [
                    row.ddd.short, row.ddd.name, row.location.name
                ]);
            }
        },
        {name: 'ddd.id', data: 'ddd.id', visible: false},
        {name: 'ddd.name', data: 'ddd.name', visible: false},
        {name: 'location.id', data: 'location.id', visible: false},
        {name: 'location.name', data: 'location.name', visible: false},

        {title: 'Role', data: 'role', name: 'role', className: 'text-nowrap'},
        {
            title: 'Status',
            name: 'status',
            data: 'status',
            className: 'text-nowrap',
            render: function (data, type, row){
                return format_label(row.status);
            }
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

        $(this).find('.reset-password-remove').show();
        $(this).find('.edit-remove').show();
        $(this).find('.reset-password-remove input, .reset-password-remove select').attr('required', true);
        $(this).find('.edit-remove input, .edit-remove select').attr('required', true);

        if(opener.name == "edit"){
            $(this).find('.edit-remove').hide();
            $(this).find('.edit-remove input, .edit-remove select').removeAttr('required');

            $("#register-modal #modal-title").html("Update <?=@$label_s?>");
            $('#register-form').find('[name="_method"]').val('PUT');
            $("#register-form input[name=operation]").val(opener.name);

            $('#register-form').find('[name="id"]').val($(opener).data('id'));
            $('#register-form').find('[name="staff_no"]').val($(opener).data('staff_no'));
            $('#register-form').find('[name="name"]').val($(opener).data('name'));
            $('#register-form').find('[name="email"]').val($(opener).data('email'));
            $('#register-form').find('[name="ddd_id"]').val($(opener).data('ddd_id'));
            $('#register-form').find('[name="location_id"]').val($(opener).data('location_id'));
            $('#register-form').find('[name="role"]').val($(opener).data('role'));
            $('#register-form').find('[name="status"]').val($(opener).data('status'));

            $("#submit-btn").html("Update");
        }else if(opener.name == "reset-password"){
            $(this).find('.reset-password-remove').hide();
            $(this).find('.reset-password-remove input, .reset-password-remove select').removeAttr('required');

            $("#register-modal #modal-title").html("Password Reset");
            $('#register-form').find('[name="_method"]').val('PUT');
            $("#register-form input[name=operation]").val(opener.name);
            $('#register-form').find('[name="id"]').val($(opener).data('id'));

            $("#submit-btn").html("Update");
        }else{
            $("#register-modal #modal-title").html("Add <?=@$label_s?>");
            $('#register-form').find('[name="_method"]').val('POST');
            $("#register-form input[name=operation]").val("add");

            $("#submit-btn").html("Submit");
        }
        original_form = $('#register-form').serialize();
    });
});
</script>
