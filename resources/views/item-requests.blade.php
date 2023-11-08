@php
    $label_p = "Item Requests";
    $label_s = "Item Request";
    $add_button_label = "New {$label_s}";
@endphp

<x-layout :page_title="$label_p" :add_button_label="$add_button_label">
    <div class="card">
        <x-table-header>

            <x-filter>
            {!! get_filter_form([
                [
                    'name' => 'item_id',
                    'label' => 'Item',
                    'options' => get_item_options()
                ],
                [
                    'name' => 'staff_id',
                    'label' => 'Staff',
                    'options' => get_staff_options()
                ],
                [
                    'name' => 'ddd_id',
                    'label' => 'DDD',
                    'options' => get_ddd_options()
                ],
                [
                    'name' => 'floor',
                    'label' => 'Floor',
                    'options' => get_floor_options()
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
                        <label class="form-label">Quantity</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" name="quantity" class="form-control validate-quantity" placeholder="Quantity" required value="1">
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

            </div>
            <div class="modal-footer">
                <button class="col-sm-3 btn btn-primary" type="submit" id="submit-btn">Submit</button>
            </div>

        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="authorize-modal" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <form id="authorize-form">
            @method('POST')

            <div class="modal-header">
                <h5 class="modal-title h4">Authorize Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="authorize-response"></div>
                <input type="hidden" name="id">
                <input type="hidden" name="item_request_id">
                <input type="hidden" name="staff_id">
                <input type="hidden" name="ddd_id">

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Status</label>
                    </div>
                    <div class="col-sm-8">
                        <select class="form-select" name="status" required>
                            <option value="" selected disabled>Select Status ...</option>
                            {!!get_request_status_options()!!}
                        </select>
                    </div>
                </div>

                <div class="dismissed-hide">
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <label class="form-label">Supply Item</label>
                        </div>
                        <div class="col-sm-8">
                            <select class="form-select" name="item_id">
                                <option value="" selected disabled>Select Item</option>
                                {!!get_item_options()!!}
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <label class="form-label">Quantity</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="quantity" class="form-control validate-quantity" placeholder="Quantity" value="1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <label class="form-label">Reference No</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="reference_no" placeholder="Reference No" maxlength="255"/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <label class="form-label">Remark</label>
                        </div>
                        <div class="col-sm-8">
                            <textarea class="form-control" name="remark" placeholder="Remark(Optional)" rows="5" maxlength="200"></textarea>
                        </div>
                    </div>
                    <x-datetime-field/>
                </div>

            </div>
            <div class="modal-footer">
                <button class="col-sm-3 btn btn-primary" type="submit" id="authorize-btn">Submit</button>
            </div>

        </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    initialize_datatable('#records-table', columns = [
        {title: '#', data: 'DT_RowIndex', orderable: false, searchable: false},
        {title: 'Staff', data: 'staff-view', searchable: false},
        {title: 'DDD', data: 'ddd', name: 'ddd', className: 'text-nowrap'},
        {title: 'Floor', data: 'floor', name: 'floor'},
        {title: 'Item', data: 'item-view', searchable: false},
        {title: 'Qty', data: 'quantity', name: 'quantity', className: 'dt-center text-nowrap'},
        {title: 'Status', data: 'status-view', searchable: false},
        {title: 'Date/Time', data: 'date-view', name: 'date-view'},
        {title: 'Action', data: 'action', name: 'action', orderable: false, searchable: false,
            className: 'no-export'},

        {data: 'staff_no', name: 'staff_no', visible: false},
        {data: 'staff_name', name: 'staff_name', visible: false},
        {data: 'item_name', name: 'item_name', visible: false},
        {data: 'item_model', name: 'item_model', visible: false},
        {data: 'status', name: 'status', visible: false},

        {data: 'item_id', name: 'item_id', visible: false},
        {data: 'staff_id', name: 'staff_id', visible: false},
        {data: 'ddd_id', name: 'ddd_id', visible: false},
        {data: 'time', name: 'time', visible: false}
    ]);

    manage_records({url : '/item-requests'});

    submit_form({
        form_selector: '#authorize-form',
        modal_selector: '#authorize-modal',
        response_selector: '#authorize-response',
        url: '/item-requests/authorize'
    });

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
            $('#register-form').find('[name="description"]').val($(opener).data('description'));

            $("#submit-btn").html("Update");
        }else{
            $("#register-modal #modal-title").html("New <?=@$label_s?>");
            $('#register-form').find('[name="_method"]').val('POST');

            $("#submit-btn").html("Submit");
        }
        original_form = $('#register-form').serialize();
    });

    $('#authorize-modal').on('show.bs.modal', function (e) {
        var opener=e.relatedTarget;
        $('#authorize-form').trigger("reset");
        $('#authorize-response').html("");

        $(this).find('[name="id"]').val('');
        $(this).find('[name="item_request_id"]').val($(opener).data('item_request_id'));
        $(this).find('[name="staff_id"]').val($(opener).data('staff_id'));
        $(this).find('[name="ddd_id"]').val($(opener).data('ddd_id'));
        $(this).find('[name="status"]').val($(opener).data('status'));

        var $this = $(this);

        Pace.restart();
        Pace.track(function(){
            $.ajax({
                url: '/get-record/item_distributions/item_request_id/' + $(opener).data('item_request_id'),
                method: 'GET',
                success: function(data) {
                    var record = data.record;
                    if(!$.isEmptyObject(record)){
                        $this.find('[name="id"]').val(record.id);
                        $this.find('[name="status"]').val(record.status);
                        $this.find('[name="item_id"]').val(record.item_id);
                        $this.find('[name="reference_no"]').val(record.reference_no);
                        $this.find('[name="remark"]').val(record.remark);
                        $this.find('[name="time"]').val(record.time);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

        $(this).find('[name="status"]').trigger('change');

        original_form = $('#authorize-form').serialize();
    });
    $(document).on('change', '#authorize-form [name="status"]', function(){
        if($(this).val() == "Dismissed"){
            $('#authorize-form .dismissed-hide').hide();
        }else{
            $('#authorize-form .dismissed-hide').show();
        }
    });
});
</script>
