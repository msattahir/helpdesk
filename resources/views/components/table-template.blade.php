@php
$role = auth()->user()->role;
@endphp

<table style="display: none;">
    <tr>
        <td id="t-block">
            <span class="d-block h5 text-inherit mb-0">$slot</span>
            $slot
        </td>
        <td id="t-support-action">
            @php
                $attrs = 'data-id="row.id"
                data-ddd_id="row.ddd_id"
                data-description="row.description"
                data-support_staff_id="row.first_support.staff_id"';
            @endphp
            @if(check_route_access('helpdesk-supports.update'))
                <button class="btn btn-primary btn-xs dropdown dropdown-toggle" type="button" id="dropdownMenuButtonWithIcon" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi-sliders"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWithIcon">
                    <a class="dropdown-item link-primary" href="/helpdesk-requests/row.request.id">
                        <i class="bi-person dropdown-item-icon"></i> View Request
                    </a>
                    <a class="dropdown-item link-info" type="button" data-bs-toggle="modal" data-bs-target="#register-modal" name="authorize" {!! $attrs !!}>
                        <i class="bi-clipboard-check dropdown-item-icon"></i> Authorize Request
                    </a>
                    <a class="dropdown-item link-danger" type="button" name="delete" data-id="row.id">
                        <i class="bi bi-trash dropdown-item-icon"></i> Delete Request
                    </a>
                </div>
            @else
                <button class="btn btn-primary" type="button" name="start" data-id="row.id">
                    <i class="bi-play-circle-fill"></i> Start
                </button>
            @endif
        </td>

        {{-- @php
            if($base_url_name == 'helpdesk-requests'){
                $attrs = 'data-id="row.id"
                data-ddd_id="row.ddd_id"
                data-description="row.description"
                data-support_staff_id="row.first_support.staff_id"';
            }else {
                $attrs = 'data-id="row.id"
                data-status="row.status"
                data-remark="row.remark"
                data-time="row.time"';
            }
        @endphp
        <td id="t-request-action">
            <button class="btn btn-primary btn-xs dropdown dropdown-toggle" type="button" id="dropdownMenuButtonWithIcon" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi-sliders"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWithIcon">

                @if(check_route_access($base_url_name.'.update'))

                @if (in_array($base_url_name, ['helpdesk-requests']))
                <a class="dropdown-item link-primary" href="/{{ $base_url_name }}/row.id">
                    <i class="bi-person dropdown-item-icon"></i> View Details
                </a>

                <a class="dropdown-item link-info" href="/helpdesk-supports?request=row.id">
                    <i class="bi-clipboard-check dropdown-item-icon"></i> View supports
                </a>

                <a class="dropdown-item link-blue" type="button" data-bs-toggle="modal" data-bs-target="#register-modal" name="edit" {!! $attrs !!}>
                    <i class="bi-pencil-square dropdown-item-icon"></i> Edit Request
                </a>

                @else
                <a class="dropdown-item link-primary" href="/helpdesk-requests/row.request.id">
                    <i class="bi-person dropdown-item-icon"></i> View Request
                </a>
                <a class="dropdown-item link-info" type="button" data-bs-toggle="modal" data-bs-target="#register-modal" name="authorize" {!! $attrs !!}>
                    <i class="bi-clipboard-check dropdown-item-icon"></i> Authorize Request
                </a>
                @endif

                <a class="dropdown-item link-danger" type="button" name="delete" data-id="row.id">
                    <i class="bi bi-trash dropdown-item-icon"></i> Delete Request
                </a>
                @endif

            </div>
        </td> --}}

    </tr>
</table>
