@php
    $label_p = "Helpdesk Request Details";
@endphp

<x-layout :page_title="$label_p">
    <div class="row align-items-center mb-2">
        <div class="col-sm-auto">
            <a class="btn btn-primary" href="/helpdesk-requests">
                <i class="bi-arrow-left-short me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row justify-content-lg-center">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <div class="card mb-3 mb-lg-5">
                <div class="card-header card-header-content-between">
                    <h4 class="card-header-title">{{ @$label_p }}</h4>
                    {!! format_label(@$data->status) !!}
                </div>

                <div class="card-body table-responsive datatable-custom position-relative">
                    <table class="table table-lg table-bordered table-align-middle card-table">
                        <tbody class="thead-light">
                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Request&nbsp;Category
                                    </span>
                                </th>
                                <td>
                                    <h5>
                                        {{@$data->request_category->parent->name}}
                                    </h5>
                                    {{@$data->request_category->name}}
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Description
                                    </span>
                                </th>
                                <td>
                                    {{@$data->description}}
                                </td>
                            </tr>

                            <tr>
                                <th width="1">
                                    <span class="d-block h5 mb-0">
                                        Request&nbsp;Time
                                    </span>
                                </th>
                                <td>
                                    {!! transform_time($data->time) !!}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-lg table-bordered table-align-middle card-table mt-5">
                        <thead class="thead-light">
                            <tr>
                                <th>
                                    Attended by
                                </th>
                                <th>
                                    Remark
                                </th>
                                <th>
                                    Status
                                </th>
                                <th>
                                    From
                                </th>
                                <th>
                                    To
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->supports as $row)
                            <tr>
                                <td>
                                    <span class="d-block h5 text-inherit mb-0">
                                        {{ $row->staff->staff_no }}
                                    </span>
                                    {{ $row->staff->name }}
                                </td>
                                <td>
                                    {{ $row->remark }}
                                </td>
                                <td>
                                    {!! format_label($row->status) !!}
                                </td>
                                <td>
                                    {!! transform_time($row->created_at) !!}
                                </td>
                                <td>
                                    @if($row->created_at == $row->updated_at)
                                    {!! format_label('Now') !!}
                                    @else
                                    {!! transform_time($row->updated_at) !!}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-header-title">Initiator Details</h4>
                </div>

                <div class="card-body">
                    <ul class="list-group list-group-flush list-group-no-gutters">

                        <li class="list-group-item pt-0">
                            <div class="d-flex justify-content-between align-items-center pb-2">
                                <h3>Submitted by:</h3>
                            </div>
                            <ul class="list-unstyled list-py-2 text-body">
                                <li>
                                    <i class="bi-person-badge me-2"></i>
                                    <span class="h5 mb-0">
                                        {{ @$data->staff->staff_no }}
                                    </span>
                                </li>
                                <li>
                                    <i class="bi-person me-2"></i>
                                    {{ @$data->staff->name }}
                                </li>
                                <li>
                                    <i class="bi-envelope me-2"></i>
                                    {{ @$data->staff->email }}
                                </li>
                            </ul>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center pb-2">
                                <h3>Requested for:</h3>
                            </div>
                            <ul class="list-unstyled list-py-2 text-body">
                                <li>
                                    <i class="bi-building me-2"></i>
                                    {{ @$data->ddd->short }}
                                </li>
                                <li>
                                    <i class="bi-pin-map me-2"></i>
                                    {{ @$data->staff->location->name }}
                                </li>
                                @if (@$data->staff->location->id == 1)
                                    <li>
                                        <i class="bi-layers me-2"></i>
                                        {{ @$data->ddd->floor }}
                                    </li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layout>
