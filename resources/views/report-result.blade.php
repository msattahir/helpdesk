<div class="card mt-5 pt-3 print-wide">
    @if (count($data['table'][0]['records']) == 0)
        <x-norecord/>
    @else

    <div class="card-header report-header">
        <div class="report-heading" style="display: flex;">
            <div style="flex-grow: 1; text-align: left;">
                <span class="top">ICT PORTAL</span>
                <span class="middle">Nigerian Content Development & Monitoring Board</span>
            </div>
            <div style="margin-right: 12px;">
                <div class="folded-parallelogram" style="height: 80px;">
                    <img src="{{ asset('assets/img/logos/logo-short.png') }}" class="no-skew"
                        alt="ICT Portal" style="width: 70px; margin-top: 5px;">
                </div>
            </div>
        </div>

        <hr class="my-3">
        <h1 class="text-center" id="report-title">
            {{ strtoupper($data['report_type'] . ' BY ' . $data['report_by']) }}
        </h1>
        <hr class="my-3">
        <div class="row report-details print-wide">
            <div class="col-4 card">
                <div class="mt-2">Report From:</div>
                <div class="h2">
                    {{@$data['date_from']}}
                </div>
            </div>
            <div class="col-4 card">
                <div class="mt-2">Report To:</div>
                <div class="h2">
                    {{@$data['date_to']}}
                </div>
            </div>
            <div class="col-4 card">
                <div class="mt-2">Generated on:</div>
                <div>
                    <span class="h2">
                        {{@$data['date_generated']}}
                    </span><br>
                    <span class="fs-4">
                        {{@$data['time_generated']}}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @foreach($data['table'] as $table)
        @if($table['title'] != '')
        <div class="h2 mt-2 text-center text-uppercase" style="font-family: 'Segoe Cond Bold';">
            {{ $table['title']}}
        </div>
        @endif

        <div class="row" style="margin: auto;">
            <div class="report-chart" style="max-height: 400px; width:700px; margin: auto;"></div>
        </div>
        <div class="table-responsive print-wide">
            <table class="report-table table table-thead-bordered table-align-top card-table print-wide">
                <thead class="thead-light">
                    <tr>
                        @foreach($data['columns'] as $col)
                            <th {!! @$col['h-attr'] !!}>
                                <span class="h5 text-nowrap">
                                    {{@$col['label']}}
                                </span>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach($table['records'] as $row)
                        <tr>

                            @foreach($data['columns'] as $col)

                            <td {!! @$col['d-attr'] !!}>
                                @if(isset($col['formatter']))
                                    {!! $col['formatter'](@$row) !!}
                                @elseif(isset($col['format-value']))
                                    {!! $col['format-value'](@$row->{$col['name']}) !!}
                                @else
                                    {{ @$row->{$col['name']} }}
                                @endif
                            </td>

                            @endforeach
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        @foreach($data['columns'] as $col)
                            @if ($loop->first)
                                <td>
                                    <span class="h5">
                                    TOTAL
                                    </span>
                                </td>
                            @elseif (isset($col['total']))
                                <td {!! @$col['d-attr'] !!}>
                                    <span class="h5">

                                    @if(isset($col['formatter']))
                                        {!! $col['formatter'](@$table['records']->sum($col['name'])) !!}
                                    @elseif(isset($col['format-value']))
                                        {!! $col['format-value'](@$table['records']->sum($col['name'])) !!}
                                    @else
                                        {{ integer_format(@$table['records']->sum($col['name'])) }}
                                    @endif

                                    </span>
                                </td>
                            @else
                                <td>&nbsp;</td>
                            @endif
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
        <hr class="my-0">
    @endforeach

    <div class="report-sign">
        <table>
            <tbody>
                <tr>
                    <th>GENERATED BY:</th>
                    <th></th>
                    <th>APPROVED BY:</th>
                </tr>
                <tr>
                    <td>Name:</td>
                    <td></td>
                    <td>Name:</td>
                </tr>
                <tr>
                    <td>Designation:</td>
                    <td></td>
                    <td>Designation:</td>
                </tr>
                <tr>
                    <td>Signature:</td>
                    <td></td>
                    <td>Signature:</td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td></td>
                    <td>Date:</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card-footer text-center print-hide">
        <button type="button" class="btn btn-primary col-sm-3" onclick="window.print()">
            Print
            <i class="bi-printer ms-1"></i>
        </button>
    </div>

    @endif
</div>
<script>
(function () {
    const data_array = @json($data['table']);
    var report_type = "<?=@$data['report_type']?>";
    var report_by = "<?=@$data['report_by']?>";

    if(report_type != "Helpdesk Supports"){
        var chart_name = "Distributed <?=@$data['report_type']?>";
    }else if(report_by == "Staff"){
        var chart_name = "Resolved <?=@$data['report_type']?>";
    }else{
        var chart_name = "Total <?=@$data['report_type']?>";
    }

    $('.report-chart').each(function(index) {
        const container = this;
        const data = data_array[index].records;

        if(report_type != "Helpdesk Supports"){
            var chart_data = data.map(record => ({
                label: record.label,
                total: record.distributed
            }));
        }else if(report_by == "Categories"){
            var chart_data = [];
            data.forEach(record => {
                var existingRecord = chart_data.find(item => item.label === record.parent.name);
                if (existingRecord) {
                    existingRecord.total += record.total;
                } else {
                    chart_data.push({
                        label: record.parent.name,
                        total: record.total
                    });
                }
            });
        }else if(report_by == "Staff"){
            var chart_data = data.map(record => ({
                label: record.label + ' - ' + record.name,
                total: record.resolved
            }));
        }else{
            var chart_data = data.map(record => ({
                label: record.label,
                total: record.total
            }));
        }

        Highcharts.chart(container, {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: ''
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        connectorColor: 'silver'
                    },
                    colors: Highcharts.map(Highcharts.getOptions().colors, function (color) {
                        return {
                        radialGradient: {
                            cx: 0.5,
                            cy: 0.3,
                            r: 0.5
                        },
                        stops: [
                            [0, color],
                            [1, Highcharts.color(color).brighten(-0.3).get('rgb')]
                        ]
                        };
                    })
                }
            },
            series: [{
                name: chart_name,
                data: chart_data.map(record => ({
                    name: record.label,
                    y: record.total
                }))
            }]
        });
    });
})();
</script>
