@php
    $label_p = "Directorates/Divisions/Departments";
    $label_s = "DDD";
    $add_button_label = "Add {$label_s}";
@endphp

<x-layout :page_title="$label_p" :add_button_label="check_route_access('ddds.store') ? $add_button_label : null">
    <div class="card">
        <x-table-header>

            <x-filter>
            {!! get_filter_form([
                [
                    'name' => 'category',
                    'label' => 'Category',
                    'options' => get_ddd_category_options()
                ],
                [
                    'name' => 'floor',
                    'label' => 'Floor',
                    'options' => get_floor_options()
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
        @if(check_route_access('ddds.update'))
            @php
            $attrs = 'data-id="row.id"
                data-name="row.name"
                data-short="row.short"
                data-category="row.category"
                data-floor="row.floor"';
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
                        <label class="form-label">Name</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" name="name" class="form-control" placeholder="Name" required maxlength="100">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Short/Abbr</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" name="short" class="form-control validate-uppercase" placeholder="Short/Abbr" required maxlength="10">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Category</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="category" required>
                            <option value="" selected disabled>Select Category</option>
                            {!!get_ddd_category_options()!!}
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Floor</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="floor" required>
                            <option value="" selected disabled>Select Floor</option>
                            {!!get_floor_options()!!}
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
            title: 'Name',
            data: 'short',
            name: 'short',
            render: function (data, type, row){
                return replace_slots($('#t-block').html(), [
                    row.short, row.name
                ]);
            }
        },
        {data: 'name', name: 'name', visible: false},
        {
            title: 'Category',
            data: 'category',
            name: 'category',
            render: function (data, type, row){
                return '<h5>' + row.category + '</h5>';
            }
        },
        {title: 'Floor', data: 'floor', name: 'floor'},

        @if(check_route_access('ddds.update'))
        {
            title: 'Action',
            render: function (data, type, row){
                return replace_template_values($('#t-action').html(), row);
            },
            orderable: false,
            searchable: false
        }
        @endif
    ]);

    manage_records({url : '/ddds'});

    $('#register-modal').on('show.bs.modal', function (e) {
        var opener=e.relatedTarget;
        $('#register-form').trigger("reset");
        $('#modal-response').html("");

        if(opener.name == "edit"){
            $("#modal-title").html("Update <?=@$label_s?>");
            $('#register-form').find('[name="_method"]').val('PUT');

            $('#register-form').find('[name="id"]').val($(opener).data('id'));
            $('#register-form').find('[name="name"]').val($(opener).data('name'));
            $('#register-form').find('[name="short"]').val($(opener).data('short'));
            $('#register-form').find('[name="category"]').val($(opener).data('category'));
            $('#register-form').find('[name="floor"]').val($(opener).data('floor'));

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
