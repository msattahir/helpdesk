@php
    $label_p = "Item Request Details";
@endphp

<x-layout :page_title="$label_p">
    <div class="row justify-content-lg-center">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <div class="card mb-3 mb-lg-5">
                <div class="card-header card-header-content-between">
                    <h4 class="card-header-title">{{ @$label_p }}</h4>
                    {!! format_label(@$request->status) !!}
                </div>

                <div class="card-body">
                    <table class="table table-lg table-bordered table-align-middle card-table">
                        <tbody class="thead-light">
                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Requested&nbsp;Item
                                    </span>
                                </th>
                                <td>
                                    <span class="d-block h5 mb-0">
                                        {{@$request->request_item_name}}
                                    </span>
                                    {{@$request->request_item_model}}
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Requested&nbsp;Quantity
                                    </span>
                                </th>
                                <td>
                                    <span class="d-block h5 mb-0">
                                        {{integer_format(@$request->request_quantity)}}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Description
                                    </span>
                                </th>
                                <td>
                                    {{@$request->description}}
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Request Time
                                    </span>
                                </th>
                                <td>
                                    {{date(
                                        "jS F, Y h:i:s",
                                        strtotime(@$request->request_time)
                                    )}}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2"></td>
                            </tr>

                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Supplied&nbsp;Item
                                    </span>
                                </th>
                                <td>
                                    <span class="d-block h5 mb-0">
                                        {{@$request->supply_item_name}}
                                    </span>
                                    {{@$request->supply_item_model}}
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Supplied&nbsp;Quantity
                                    </span>
                                </th>
                                <td>
                                    <span class="d-block h5 mb-0">
                                        {{integer_format(@$request->supply_quantity)}}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Reference&nbsp;No
                                    </span>
                                </th>
                                <td>
                                    {{@$request->reference_no}}
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Remark
                                    </span>
                                </th>
                                <td>
                                    {{@$request->remark}}
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Supply Time
                                    </span>
                                </th>
                                <td>
                                    {{
                                    (@$request->supply_time) ?
                                    date(
                                        "jS F, Y h:i:s",
                                        strtotime(@$request->supply_time)
                                    ) : ''
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-header-title">Staff</h4>
                </div>

                <div class="card-body">
                    <ul class="list-group list-group-flush list-group-no-gutters">

                        <li class="list-group-item pt-0">
                            <div class="d-flex justify-content-between align-items-center pb-2">
                                <h3>Requested by:</h3>
                            </div>
                            <ul class="list-unstyled list-py-2 text-body">
                                <li>
                                    <i class="bi-person-badge me-2"></i>
                                    <span class="h5 mb-0">
                                        {{@$request->request_staff_no}}
                                    </span>
                                </li>
                                <li>
                                    <i class="bi-person me-2"></i>
                                    {{@$request->request_staff_name}}
                                </li>
                                <li>
                                    <i class="bi-envelope me-2"></i>
                                    {{@$request->request_staff_email}}
                                </li>
                                <li>
                                    <i class="bi-building me-2"></i>
                                    {{@$request->request_ddd}}
                                </li>
                                <li>
                                    <i class="bi-layers me-2"></i>
                                    {{@$request->request_floor}}
                                </li>
                            </ul>
                        </li>

                        @if(@$request->supply_staff_no != "")
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center pb-2">
                                <h3>Authorized by:</h3>
                            </div>
                            <ul class="list-unstyled list-py-2 text-body">
                                <li>
                                    <i class="bi-person-badge me-2"></i>
                                    <span class="h5 mb-0">
                                        {{@$request->supply_staff_no}}
                                    </span>
                                </li>
                                <li>
                                    <i class="bi-person me-2"></i>
                                    {{@$request->supply_staff_name}}
                                </li>
                                <li>
                                    <i class="bi-envelope me-2"></i>
                                    {{@$request->supply_staff_email}}
                                </li>
                                <li>
                                    <i class="bi-building me-2"></i>
                                    {{@$request->supply_ddd}}
                                </li>
                                <li>
                                    <i class="bi-layers me-2"></i>
                                    {{@$request->supply_floor}}
                                </li>
                            </ul>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layout>
