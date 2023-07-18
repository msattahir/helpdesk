@php
    $label_p = "Helpdesk Support Details";
@endphp

<x-layout :page_title="$label_p">
    <div class="row justify-content-lg-center">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <div class="card mb-3 mb-lg-5">
                <div class="card-header card-header-content-between">
                    <h4 class="card-header-title">{{ @$label_p }}</h4>
                    {!! format_label(@$data->status) !!}
                </div>

                <div class="card-body">
                    <table class="table table-lg table-bordered table-align-middle card-table">
                        <tbody class="thead-light">
                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Remark
                                    </span>
                                </th>
                                <td>
                                    {{@$data->remark}}
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <span class="d-block h5 mb-0">
                                        Support&nbsp;Time
                                    </span>
                                </th>
                                <td>
                                    {{date(
                                        "jS F, Y h:i:s",
                                        strtotime(@$data->support_time)
                                    )}}
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
                                <h3>Support to:</h3>
                            </div>
                            <ul class="list-unstyled list-py-2 text-body">
                                <li>
                                    <i class="bi-person-badge me-2"></i>
                                    <span class="h5 mb-0">
                                        {{@$data->support_staff_no}}
                                    </span>
                                </li>
                                <li>
                                    <i class="bi-person me-2"></i>
                                    {{@$data->support_staff_name}}
                                </li>
                                <li>
                                    <i class="bi-envelope me-2"></i>
                                    {{@$data->support_staff_email}}
                                </li>
                                <li>
                                    <i class="bi-building me-2"></i>
                                    {{@$data->support_ddd}}
                                </li>
                                <li>
                                    <i class="bi-layers me-2"></i>
                                    {{@$data->support_floor}}
                                </li>
                            </ul>
                        </li>

                        @if(@$data->authorize_staff_no != "")
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center pb-2">
                                <h3>Authorized by:</h3>
                            </div>
                            <ul class="list-unstyled list-py-2 text-body">
                                <li>
                                    <i class="bi-person-badge me-2"></i>
                                    <span class="h5 mb-0">
                                        {{@$data->authorize_staff_no}}
                                    </span>
                                </li>
                                <li>
                                    <i class="bi-person me-2"></i>
                                    {{@$data->authorize_staff_name}}
                                </li>
                                <li>
                                    <i class="bi-envelope me-2"></i>
                                    {{@$data->authorize_staff_email}}
                                </li>
                                <li>
                                    <i class="bi-building me-2"></i>
                                    {{@$data->authorize_ddd}}
                                </li>
                                <li>
                                    <i class="bi-layers me-2"></i>
                                    {{@$data->authorize_floor}}
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
