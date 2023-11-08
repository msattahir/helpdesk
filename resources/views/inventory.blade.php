<x-layout :page_title="$label_p">
    <div class="card">
        <x-table-header>

            <x-filter>
            {!! get_filter_form([
                [
                    'name' => $label_p == 'Inventory' ? 'category' : 'item.category',
                    'label' => 'Category',
                    'options' => get_item_category_options()
                ],
                [
                    'name' => $label_p == 'Inventory' ? 'name' : 'item.name',
                    'label' => 'Item Name',
                    'options' => get_item_name_options()
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
    <td id="t-add">
        <button type="button" class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#register-modal" name="add" data-item_id="$slot">
            +
        </button>
    </td>
    <td id="t-details">
        <a href="/{{ $base_url_name }}/$slot" type="button" class="btn btn-secondary btn-xs">
            View
        </a>
    </td>
    <td id="t-action">

        @php
            $attrs = 'data-id="row.id"
                data-item_id="row.item_id"
                data-quantity="row.quantity"';
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
                <input type="hidden" name="item_id">

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Quantity</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" name="quantity" class="form-control validate-quantity" placeholder="Quantity" required maxlength="100">
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
    var columns = [];

    if("<?=@$label_p?>" == "Inventory"){
        columns = [
            {title: '#', data: 'DT_RowIndex', orderable: false, searchable: false},
            {
                title: 'Name',
                data: 'name',
                name: 'name',
                className: 'text-nowrap',
                render: function (data, type, row){
                    var template = '<a href="/{{ $base_url_name }}/' + row.id + '">' +
                        $('#t-block').html() +
                    '</a>';

                    return replace_slots(template, [
                        row.name, row.model
                    ]);
                }
            },
            {data: 'model', name: 'model', visible: false},
            {title: 'Category', data: 'category', name: 'category', className: 'text-nowrap'},
            {
                title: 'Total',
                data: 'inventory_total',
                name: 'inventory_total',
                className: 'dt-center text-nowrap',
                render: function (data, type, row){
                    return '<h5>' + format_integer(row.inventory_total) + '</h5>';
                }
            },
            {
                title: 'Allocated',
                data: 'allocated',
                name: 'allocated',
                className: 'dt-center text-nowrap',
                render: function (data, type, row){
                    return format_integer(row.allocated);
                }
            },
            {
                title: 'Configured',
                data: 'configured',
                name: 'configured',
                className: 'dt-center text-nowrap',
                render: function (data, type, row){
                    return format_integer(row.configured);
                }
            },
            {
                title: 'Installed',
                data: 'installed',
                name: 'installed',
                className: 'dt-center text-nowrap',
                render: function (data, type, row){
                    return format_integer(row.installed);
                }
            },
            {
                title: 'Distributed',
                data: 'distributed',
                name: 'distributed',
                className: 'dt-center text-nowrap',
                render: function (data, type, row){
                    return format_integer(row.distributed);
                }
            },
            {
                title: 'Balance',
                data: 'inventory_balance',
                name: 'inventory_balance',
                className: 'dt-center text-nowrap',
                render: function (data, type, row){
                    return '<h5>' + format_integer(row.inventory_balance) + '</h5>';
                }
            },
            {
                title: 'Returned',
                data: 'returned',
                name: 'returned',
                className: 'dt-center text-nowrap',
                render: function (data, type, row){
                    return format_integer(row.returned);
                }
            },
            {
                title: 'Add',
                render: function (data, type, row){
                    return replace_slots($('#t-add').html(), [
                        row.id
                    ]);
                },
                className: 'dt-center',
                orderable: false,
                searchable: false
            },
            {
                title: 'Details',
                render: function (data, type, row){
                    return replace_slots($('#t-details').html(), [
                        row.id
                    ]);
                },
                className: 'dt-center',
                orderable: false,
                searchable: false
            },
        ];
    }else{
        columns = [
            {title: '#', data: 'DT_RowIndex', orderable: false, searchable: false},
            {
                title: 'Name',
                name: 'item.name',
                render: function (data, type, row){
                    return replace_slots($('#t-block').html(), [
                        row.item.name, row.item.model
                    ]);
                }
            },
            {data: 'item.model', name: 'item.model', visible: false},

            {title: 'Category', name: 'item.category', data: 'item.category'},
            {
                title: 'Quantity',
                data: 'quantity',
                name: 'quantity',
                className: 'dt-center',
                render: function (data, type, row){
                    return '<h5>' + format_integer(row.quantity) + '</h5>';
                }
            },
            {
                title: 'Uploaded by',
                name: 'staff.staff_no',
                data: 'staff.staff_no',
                render: function (data, type, row){
                    return replace_slots($('#t-block').html(), [
                        row.staff.staff_no, row.staff.name
                    ]);
                },
                searchable: false
            },
            {data: 'staff.name', name: 'staff.name', visible: false},

            {
                title: 'Date/Time',
                data: 'created_at',
                name: 'created_at',
                className: 'text-nowrap',
                render: function (data, type, row){
                    return format_date_time(row.created_at);
                }
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
        ];
    }

    initialize_datatable('#records-table', columns);

    manage_records({url : base_url});

    $('#register-modal').on('show.bs.modal', function (e) {
        var opener=e.relatedTarget;
        $('#register-form').trigger("reset");
        $('#modal-response').html("");

        if(opener.name == "edit"){
            $("#register-modal #modal-title").html("Update <?=@$label_s?>");
            $('#register-form').find('[name="_method"]').val('PUT');

            $('#register-form').find('[name="id"]').val($(opener).data('id'));
            $('#register-form').find('[name="item_id"]').val($(opener).data('item_id'));
            $('#register-form').find('[name="quantity"]').val($(opener).data('quantity'));

            $("#submit-btn").html("Update");
        }else{
            $("#register-modal #modal-title").html("Add <?=@$label_s?>");
            $('#register-form').find('[name="_method"]').val('POST');

            $('#register-form').find('[name="item_id"]').val($(opener).data('item_id'));

            $("#submit-btn").html("Submit");
        }

        original_form = $('#register-form').serialize();
    });
});
</script>
