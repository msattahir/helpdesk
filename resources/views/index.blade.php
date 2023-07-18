@php
    $page_title = "Dashboard";
@endphp

<x-layout :page_title="$page_title">
    <x-toast-msg/>

    <div class="row">
        <x-dashboard-card-small title="Pending Helpdesk Requests" value="{{@$helpdesk_requests->pending}}" total="{{@$helpdesk_requests->total}}" class="danger" />

        <x-dashboard-card-small title="Helpdesk Requests Treated" value="{{@$helpdesk_requests->treated}}" total="{{@$helpdesk_requests->total}}" class="success" />

        <x-dashboard-card-small title="Pending Item Distributions" value="{{@$item_requests->pending}}" total="{{@$item_requests->total}}" class="danger" />

        <x-dashboard-card-small title="Item Distributions Treated" value="{{@$item_requests->treated}}" total="{{@$item_requests->total}}" class="success" />
    </div>

    @if (auth()->user()->role == "Admin")

    <div class="mb-3 mb-lg-5 card h-100">
        <div class="card-header card-header-content-sm-between">
            <h4 class="card-header-title mb-2 mb-sm-0">Inventory</h4>
        </div>

        <div class="card-body">
            <div class="chartjs-custom mx-auto" style="min-height: 20rem;">
                <canvas id="inventory-chart"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3 mb-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-header-title text-center">Recent Helpdesk Requests</h4>
                </div>

                <x-table table_id="helpdesk-requests-table" />
                <div class="card-footer text-center">
                    @if($helpdesk_requests->total > 5)
                    <a class="btn btn-primary" href="/helpdesk-requests">
                        <b>
                            View all helpdesk requests
                        </b>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3 mb-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-header-title text-center">Recent Item Distributions</h4>
                </div>

                <x-table table_id="item-requests-table" />

                <div class="card-footer text-center">
                    @if($item_requests->total > 5)
                    <a class="btn btn-primary" href="/item-distributions">
                        <b>
                            View all item distributions
                        </b>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @else
    <div class="mb-3 mb-lg-5 card h-100">
        <div class="card-header card-header-content-sm-between">
            <h4 class="card-header-title mb-2 mb-sm-0">Recent Activities</h4>
        </div>

        <x-table table_id="activities-table" totalQty_id="#activity-total-qty" entries_id="#activity-entries" pagination="activity-pagination"/>
        <x-table-footer totalQty_id="activity-total-qty" entries_id="activity-entries" pagination="activity-pagination"/>
    </div>
    @endif
</x-layout>

<table id="template-table" style="display: none;">
    <td id="t-block">
        <span class="d-block h5 text-inherit mb-0">$slot</span>
        $slot
    </td>
</table>

<script>
    $(document).ready(function () {
        @if (auth()->user()->role == "Admin")
        initialize_datatable(
            '#helpdesk-requests-table',
            columns = [
                { title: '#', data: 'DT_RowIndex', width: '1px', orderable: false },
                {
                    title: 'Staff',
                    data: 'staff.staff_no',
                    render: function (data, type, row){
                        return replace_slots($('#t-block').html(), [
                            row.staff.staff_no, row.staff.name
                        ]);
                    },
                    orderable: false
                },
                { title: 'DDD', data: 'ddd.short', orderable: false },
                { title: 'Floor', data: 'ddd.floor', orderable: false },
                {
                    title: 'Date/Time',
                    data: 'time',
                    className: 'text-nowrap',
                    render: function (data, type, row){
                        return format_date_time(row.time);
                    },
                    orderable: false
                },
            ],
            '/recent-helpdesk-requests'
        );

        initialize_datatable(
            '#item-requests-table',
            columns = [
                { title: '#', data: 'DT_RowIndex', width: '1px', orderable: false },
                {
                    title: 'Staff/Office',
                    data: 'staff.staff_no',
                    render: function (data, type, row){
                        if(row.distributionable && row.distributionable.staff_no){
                            return replace_slots($('#t-block').html(), [
                                row.distributionable.staff_no, row.distributionable.name
                            ]);
                        }
                        return replace_slots($('#t-block').html(), [
                            row.distributionable.office_no, 'Office No'
                        ]);
                    },
                    orderable: false
                },

                { title: 'DDD', data: 'distributionable.ddd.short', orderable: false },
                { title: 'Floor', data: 'distributionable.ddd.floor', orderable: false },
                {
                    title: 'Date/Time',
                    data: 'time',
                    className: 'text-nowrap',
                    render: function (data, type, row){
                        return format_date_time(row.time);
                    },
                    orderable: false
                },
            ],
            '/recent-item-requests'
        );


        (function () {
            const inventoryData = @json($inventory);

            const bubbleColors = ['#AA0000', '#fd7e14', '#6610f2', '#006ddd', '#006717'];
            var maxRemaining = Math.max(...inventoryData.map(item => item.inventory_balance));
            var minRemaining = Math.min(...inventoryData.map(item => item.inventory_balance));
            var colorRange = chroma.scale(['#cdffd8', '#00FF00', '#004400']).domain([minRemaining, maxRemaining]);

            var inventoryChart = new Chart(document.getElementById('inventory-chart'), {
                type: 'bubble',
                data: {
                    datasets: inventoryData.map((item, index) => ({
                        label: item.name,
                        data: [{
                            x: item.inventory_balance,
                            y: Math.random() * 100,
                            r: 10
                        }],
                        color: "#fff",

                        // backgroundColor: bubbleColors[Math.floor(Math.random() * bubbleColors.length)],
                        backgroundColor: colorRange(item.inventory_balance).hex(),

                        borderColor: 'transparent'
                    })),
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                display: false,
                                beginAtZero: true
                            }
                        },
                        x: {
                            grid: {
                                display: true,
                                drawBorder: false
                            },
                            ticks: {
                                display: true,
                                beginAtZero: true,
                                callback: function (value, index, values) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const item = inventoryData[context.datasetIndex];
                                    const remainingItems = context.dataset.data[context.dataIndex].x;
                                    return `${item.name}: ${remainingItems} remaining`;
                                }
                            }
                        }
                    }
                }
            });
        })()

        @else
        initialize_datatable(
            '#activities-table',
            columns = [
                { title: '#', data: 'DT_RowIndex', width: '1px', orderable: false },
                {
                    title: 'Staff',
                    data: 'staff.staff_no',
                    render: function (data, type, row){
                        return replace_slots($('#t-block').html(), [
                            row.staff.staff_no, row.staff.name
                        ]);
                    },
                    orderable: false
                },
                { title: 'Activity', data: 'activity', orderable: false },
                {
                    title: 'Date/Time',
                    data: 'created_at',
                    className: 'text-nowrap',
                    render: function (data, type, row){
                        return format_date_time(row.created_at);
                    },
                    orderable: false
                },
            ],
            '/staff-activities',
            '#activity-entries'
        );
        @endif
    });
</script>
