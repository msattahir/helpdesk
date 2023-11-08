@php
    $label_p = "Offices";
    $label_s = "Office";
    $add_button_label = "Add {$label_s}";
@endphp

<x-layout :page_title="$label_p" :add_button_label="check_route_access('offices.store') ? $add_button_label : null">
    <div class="card">
        <x-table-header>

            <x-filter>
            {!! get_filter_form([
                [
                    'name' => 'location_id',
                    'label' => 'Location',
                    'options' => get_location_options()
                ],
                [
                    'name' => 'ddd_id',
                    'label' => 'DDD',
                    'options' => get_ddd_options()
                ]
            ]) !!}
            </x-filter>

        </x-table-header>

        <div id="response" class="m-2"></div>
        <x-table/>

        <x-table-footer/>
    </div>
</x-layout>

<table id="template-table" style="display: none;">
    <td id="t-block">
        <span class="d-block h5 text-inherit mb-0">$slot</span>
        $slot
    </td>
    <td id="t-action">
        @if(check_route_access('offices.update'))
            @php
            $attrs = 'data-id="row.id"
                data-office_no="row.office_no"
                data-description="row.description"
                data-location_id="row.location_id"
                data-ddd_id="row.ddd_id"';
            @endphp

            <div class="btn-group">
                <button type="button" class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#register-modal" name="edit" {!! $attrs !!}>
                    <i class="bi bi-pencil-square"></i>
                </button>
                <div class="or or-xs"></div>
                <button type="button" class="btn btn-danger btn-xs" name="delete" data-id="row.id">
                    <i class="bi bi-trash"></i>
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
                <h5 class="modal-title h4" id="modal-title">Add {{$label_s}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-response"></div>
                <input type="hidden" name="id">
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Office No</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" class="form-control validate-office-no" name="office_no" placeholder="Office No"/>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Description</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="description" placeholder="Description" maxlength="50"/>
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
                        <label class="form-label">DDD</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="ddd_id" required>
                            <option value="" selected disabled>Select DDD</option>
                            {!!get_ddd_options()!!}
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

<x-delete-modal/>

<script>
$(document).ready(function () {
    initialize_datatable('#records-table', columns = [
        {title: '#', data: 'DT_RowIndex', width: '1px', orderable: false, searchable: false},
        {
            title: 'Office',
            data: 'office_no',
            name: 'office_no',
            render: function (data, type, row){
                return replace_slots($('#t-block').html(), [
                    row.office_no, row.description
                ]);
            }
        },
        {title: 'Location', data: 'location.name', name: 'location.name'},
        {data: 'location_id', name: 'location_id', visible: false},
        {
            title: 'DDD',
            data: 'ddd.short',
            name: 'ddd.short',
            render: function (data, type, row){
                return replace_slots($('#t-block').html(), [
                    row.ddd.short, row.ddd.name
                ]);
            }
        },
        {data: 'description', name: 'description', visible: false},
        {data: 'ddd_id', name: 'ddd_id', visible: false},
        {data: 'ddd.name', name: 'ddd.name', visible: false},

        @if(check_route_access('offices.update'))
        {
            title: 'Action',
            render: function (data, type, row){
                return replace_template_values($('#t-action').html(), row);
            },
            orderable: false,
            searchable: false,
            className: 'no-export'
        }
        @endif
    ]);

    manage_records({url : '/offices'});

    $('#register-modal').on('show.bs.modal', function (e) {
        var opener=e.relatedTarget;
        $('#register-form').trigger("reset");
        $('#modal-response').html("");

        if(opener.name == "edit"){
            $("#modal-title").html("Update <?=@$label_s?>");
            $('#register-form').find('[name="_method"]').val('PUT');

            $('#register-form').find('[name="id"]').val($(opener).data('id'));
            $('#register-form').find('[name="office_no"]').val($(opener).data('office_no'));
            $('#register-form').find('[name="description"]').val($(opener).data('description'));
            $('#register-form').find('[name="ddd_id"]').val($(opener).data('ddd_id'));
            $('#register-form').find('[name="location_id"]').val($(opener).data('location_id'));

            $("#submit-btn").html("Update");
        }else{
            $("#modal-title").html("Add <?=@$label_s?>");
            $('#register-form').find('[name="_method"]').val('POST');

            $("#submit-btn").html("Submit");
        }
        original_form = $('#register-form').serialize();
    });
});
</script>
