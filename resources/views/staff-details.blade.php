@php
    $initial = substr(@$staff->name, 0, 1);
@endphp

<x-layout :page_title="$page_title">
    <div class="row justify-content-lg-center">
        <div class="col-lg-10">
            <div class="profile-cover">
                <div class="profile-cover-img-wrapper">
                    <img class="profile-cover-img" src="{{asset('assets/img/1920x400/img2.jpg')}}" alt="Image Description">
                </div>
            </div>
            <div class="text-center mb-5">
                <div class="avatar avatar-xxl avatar-circle profile-cover-avatar">
                    <div class="avatar avatar-soft-primary avatar-circle">
                        <span class="avatar-initials">{{$initial}}</span>
                    </div>
                </div>

                <h1 class="page-header-title">{{@$staff->staff_no}}</h1>

                <ul class="list-inline list-px-2">
                    <li class="list-inline-item">
                        <i class="bi-person me-1"></i>
                        <span>{{@$staff->name}}</span>
                    </li>

                    <li class="list-inline-item">
                        <i class="bi-calendar-week me-1"></i>
                        <span>Registered on {{date("jS F, Y", strtotime(@$staff->created_at))}}</span>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="js-sticky-block card mb-3 mb-lg-5">
                        <div class="card-header">
                            <h4 class="card-header-title">Details</h4>
                        </div>

                        <div class="card-body">
                            <ul class="list-unstyled list-py-2 text-dark mb-0">
                                <li class="pb-0"><span class="card-subtitle">Account</span></li>
                                <li><i class="bi-person dropdown-item-icon"></i> {{@$staff->name}}</li>
                                <li><i class="bi-diagram-3 dropdown-item-icon"></i> {{@$staff->role}}</li>
                                <li><i class="bi-shield-lock dropdown-item-icon"></i> {!! format_label(@$staff->status) !!}</li>

                                <li class="pt-4 pb-0"><span class="card-subtitle">Contacts</span></li>
                                <li><i class="bi-building dropdown-item-icon"></i> {{@$staff->ddd->name}} ({{@$staff->ddd->short}})</li>
                                <li><i class="bi-pin-map dropdown-item-icon"></i> {{@$staff->location->name}} </li>
                                @if (@$staff->location->id == 1)
                                    <li><i class="bi-layers dropdown-item-icon"></i> {{@$staff->ddd->floor}} </li>
                                @endif
                                <li><i class="bi-at dropdown-item-icon"></i> {{@$staff->email}}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="d-grid gap-3 gap-lg-5">
                        <div class="card">
                            <div class="card-header card-header-content-between">
                                <h4 class="card-header-title">Worktools Assigned</h4>
                                <span class="badge bg-success rounded-pill ms-1">{{ count($devices) > 0 ? count($devices) : ''}}</span>
                            </div>
                            <div class="card-body card-body-height" style="height: 15rem;">
                                @if(count($devices) == 0)
                                    <x-norecord/>
                                @else
                                <div class="table-responsive">
                                    <table
                                        class="table table-borderless table-thead-bordered table-nowrap card-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Worktool</th>
                                                <th>Status/Ref</th>
                                                <th width="1">Date/Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($devices as $row)
                                                @php
                                                    $timestamp = strtotime(@$row->time);
                                                @endphp

                                                <tr>
                                                    <td>
                                                        <h5>{{@$row->item->name}}</h5>
                                                        {{@$row->item->model}}
                                                    </td>
                                                    <td>
                                                        <h5>{!!format_label(@$row->status)!!}</h5>
                                                        {{@$row->reference_no}}
                                                    </td>
                                                    <td>
                                                        <h5 class="text-nowrap">{{date('Y-m-d', $timestamp)}}</h5>

                                                        {{date('h:i:s', $timestamp)}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>

                                    </table>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header card-header-content-between">
                                <h4 class="card-header-title">Support Rendered</h4>
                                <span class="badge bg-primary rounded-pill ms-1">{{ count($helpdesks) > 0 ? count($helpdesks) : ''}}</span>
                            </div>

                            <div class="card-body card-body-height" style="height: 12rem;">
                                @if(count($helpdesks) == 0)
                                    <x-norecord/>
                                @else
                                <div class="table-responsive">
                                    <table
                                        class="table table-borderless table-thead-bordered card-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Remark</th>
                                                <th>Status</th>
                                                <th width="1">Date/Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($helpdesks as $row)

                                                @php
                                                    $timestamp = strtotime(@$row->date_time);
                                                @endphp
                                                <tr>
                                                    <td>{{@$row->remark}}</td>
                                                    <td>{!!format_label(@$row->status)!!}</td>
                                                    <td>
                                                        <h5 class="text-nowrap">{{date('Y-m-d', $timestamp)}}</h5>

                                                        {{date('h:i:s', $timestamp)}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header card-header-content-between">
                                <h4 class="card-header-title">Consumables Assigned</h4>
                                <span class="badge bg-info rounded-pill ms-1">{{ count($consumables) > 0 ? count($consumables) : ''}}</span>
                            </div>

                            <div class="card-body card-body-height" style="height: 12rem;">
                                @if(count($consumables) == 0)
                                    <x-norecord/>
                                @else
                                <div class="table-responsive">
                                    <table
                                        class="table table-borderless table-thead-bordered table-nowrap card-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Consumable</th>
                                                <th>Status/Ref</th>
                                                <th width="1">Date/Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($consumables as $row)
                                                @php
                                                    $timestamp = strtotime(@$row->time);
                                                @endphp

                                                <tr>
                                                    <td>
                                                        <h5>{{@$row->name}}</h5>
                                                        {{@$row->model}}
                                                    </td>
                                                    <td>
                                                        <h5>{!!format_label(@$row->status)!!}</h5>
                                                        {{@$row->reference_no}}
                                                    </td>
                                                    <td>
                                                        <h5 class="text-nowrap">{{date('Y-m-d', $timestamp)}}</h5>

                                                        {{date('h:i:s', $timestamp)}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
