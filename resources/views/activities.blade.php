@php
    $label_p = "Activity Logs";
    $label_s = "Activity Log";
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
                    'name' => 'staff.ddd.id',
                    'label' => 'DDD',
                    'options' => get_ddd_options()
                ],
                [
                    'name' => 'staff.ddd.floor',
                    'label' => 'Floor',
                    'options' => get_floor_options()
                ]
            ]) !!}
            </x-filter>

        </x-table-header>

        <x-table/>

        <x-table-footer/>
    </div>
</x-layout>

<table id="template-table" style="display: none;">
    <td id="t-block">
        <span class="d-block h5 text-inherit mb-0">$slot</span>
        $slot
    </td>
</table>

<script>
$(document).ready(function () {
    initialize_datatable('#records-table', columns = [
        {title: '#', data: 'DT_RowIndex', orderable: false, searchable: false},
        {
            title: 'Staff',
            data: 'staff.staff_no',
            name: 'staff.staff_no',
            render: function (data, type, row){
                return replace_slots($('#t-block').html(), [
                    row.staff.staff_no, row.staff.name
                ]);
            }
        },
        {data: 'staff.id', name: 'staff.id', visible: false},
        {data: 'staff.name', name: 'staff.name', visible: false},

        {
            title: 'DDD',
            data: 'ddd.short',
            name: 'staff.ddd.short',
            render: function (data, type, row){
                return replace_slots($('#t-block').html(), [
                    row.staff.ddd.short, row.staff.ddd.floor
                ]);
            }
        },
        {data: 'staff.ddd.id', name: 'staff.ddd.id', visible: false},
        {data: 'staff.ddd.floor', name: 'staff.ddd.floor', visible: false},
        {title: 'Activity', data: 'activity', name: 'activity'},

        {
            title: 'Date/Time',
            data: 'created_at',
            name: 'created_at',
            className: 'text-nowrap',
            render: function (data, type, row){
                return format_date_time(row.created_at);
            },
            searchable: false
        },
    ]);

    manage_records({url : '/activities'});
});
</script>
