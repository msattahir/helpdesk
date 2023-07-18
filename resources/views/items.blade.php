@php
    $label_p = "Items";
    $label_s = "Item";
    $add_button_label = "Add {$label_s}";
@endphp

<x-layout :page_title="$label_p" :add_button_label="$add_button_label">
    <div class="card">
        <x-table-header>

            <x-filter>
            {!! get_filter_form([
                [
                    'name' => 'name',
                    'label' => 'Name',
                    'options' => get_item_name_options()
                ],
                [
                    'name' => 'category',
                    'label' => 'Category',
                    'options' => get_item_category_options()
                ]
            ]) !!}
            </x-filter>

        </x-table-header>

        <div id="response" class="m-2"></div>
        <x-table/>

        <x-table-footer/>
    </div>
</x-layout>

<div class="modal fade" id="register-modal" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <form id="register-form">
            @method('POST')

            <div class="modal-header">
                <h3 class="modal-title" id="modal-title">Add {{$label_s}}</h3>
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
                        <select class="form-select" name="name" required>
                            <option value="" selected disabled>Select Name ...</option>
                            {!!get_item_name_options()!!}
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Model</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" name="model" class="form-control" placeholder="Model" required maxlength="100">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Category</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="category" required>
                            <option value="" selected disabled>Select Category</option>
                            {!!get_item_category_options()!!}
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
        {title: 'Name', data: 'name-view', searchable: false},
        {title: 'Model', data: 'model', name: 'model'},
        {title: 'Category', data: 'category-view', searchable: false},
        {title: 'Action', data: 'action', name: 'action', width: '1px', orderable: false, searchable: false},

        {data: 'name', name: 'name', visible: false},
        {data: 'category', name: 'category', visible: false}
    ]);

    manage_records({url : '/items'});


    $('#register-modal').on('show.bs.modal', function (e) {
        var opener=e.relatedTarget;
        $('#register-form').trigger("reset");
        $('#modal-response').html("");

        if(opener.name == "edit"){
            $("#modal-title").html("Update <?=@$label_s?>");
            $('#register-form').find('[name="_method"]').val('PUT');

            $('#register-form').find('[name="id"]').val($(opener).data('id'));
            $('#register-form').find('[name="name"]').val($(opener).data('name'));
            $('#register-form').find('[name="model"]').val($(opener).data('model'));
            $('#register-form').find('[name="category"]').val($(opener).data('category'));

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
