<aside
    class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered bg-white print-hide">
    <div class="navbar-vertical-container print-hide">
        <div class="navbar-vertical-footer-offset">
            <div class="navbar-brand d-flex justify-content-center">

                <x-navbar-brand />

                <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
                    <i class="bi-arrow-bar-left navbar-toggler-short-align"
                        data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Collapse"></i>
                    <i class="bi-arrow-bar-right navbar-toggler-full-align"
                        data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Expand"></i>
                </button>
            </div>

            <div class="navbar-vertical-content">
                <div class="navbar-nav nav-compact">
                    <div id="navbarVerticalMenu" class="nav nav-vertical card-navbar-nav">

                        @php
                            use Illuminate\Support\Facades\DB;

                            $current_uri = Request::getRequestUri();

                            $dashboard_link = ['Dashboard', 'bi-house-door', '/'];

                            $helpdesks_link = ['Helpdesk', 'bi-headset', '/helpdesk-requests'];

                            $helpdesk_requests_link = ['Requests', 'bi-headset', '/helpdesk-requests'];

                            $staff_link = ['Staff', 'bi-people', '/staff'];

                            $ddds_link = ['DDDs', 'bi-buildings', '/ddds'];

                            $offices_link = ['Offices', 'bi-door-open', '/offices'];

                            $reports_link = ['Reports', 'bi-clipboard-data', '/reports'];

                            $worktools_link = ['Worktools', 'bi-pc-display', '/item-distributions'];

                            $add_links = [];

                            $role = auth()->user()->role;
                            if ($role == 'Admin') {
                                $helpdesks_count = App\Models\HelpdeskRequest::where('status', 'Pending')->count();

                                $distributions_count = App\Models\ItemRequest::where('status', 'Pending')->count();

                                $helpdesks_link = ['Helpdesk' . format_count($helpdesks_count), 'bi-headset', '/helpdesk-requests'];

                                $inventory_link = ['Inventory' . format_count($distributions_count), 'bi-pc-display', [
                                    ['Items', '/items'],
                                    ['Inventory', '/inventory'],
                                    ['Distributions' . format_count($distributions_count), '/item-distributions']
                                ]];

                                $activities_link = ['Activities', 'bi-activity', '/activities'];

                                $menus = [$dashboard_link, $helpdesks_link, $inventory_link, $staff_link, $offices_link, $ddds_link, $reports_link, $activities_link];
                            } elseif (in_array($role, ["Helpdesk Admin", "Adhoc Staff"])) {
                                $helpdesks_count = App\Models\HelpdeskSupport::where('status', 'Pending')
                                    ->count();

                                $helpdesks_link = ['Helpdesk' . format_count($helpdesks_count), 'bi-headset', '/helpdesk-requests'];

                                $menus = [$dashboard_link, $helpdesks_link, $reports_link];
                            } elseif ($role == 'Inventory Admin') {
                                $distributions_count = App\Models\ItemRequest::where('status', 'Pending')->count();

                                $inventory_link = ['Inventory' . format_count($distributions_count), 'bi-pc-display', [
                                    ['Items', '/items'],
                                    ['Inventory', '/inventory'],
                                    ['Distributions' . format_count($distributions_count), '/item-distributions']
                                ]];

                                $menus = [$dashboard_link, $inventory_link, $reports_link];
                            } elseif ($role == 'Helpdesk Staff') {
                                $helpdesks_count = App\Models\HelpdeskSupport::where('status', 'Pending')
                                    ->where('staff_id', auth()->id())
                                    ->count();

                                $helpdesks_link = ['Helpdesk' . format_count($helpdesks_count), 'bi-headset', '/helpdesk-supports'];

                                $menus = [$dashboard_link, $helpdesks_link];
                            } else {
                                $menus = [$dashboard_link];
                            }

                            $links = [];

                            $i = 0;
                            foreach ($menus as $m) {
                                $i++;
                                if (is_array($m[2])) {
                                    $sub_menus = @$m[2];

                                    $sub_id = "submenu-id-{$i}";

                                    $sub = '<div id="' . @$sub_id . '" class="nav-collapse collapse " data-bs-parent="#navbarVerticalMenuPagesMenu" style="text-align: left!important;">';
                                    $main_active = '';
                                    foreach ($sub_menus as $s) {
                                        $label = @$s[0];
                                        $sub_link = @$s[1];

                                        if (str_contains($current_uri, $sub_link)) {
                                            $sub_active = 'active';
                                        } else {
                                            $sub_active = '';
                                        }

                                        $links[] = $sub_link;

                                        $main_active .= " {$sub_active}";

                                        $sub .= '<a href="' . $sub_link . '" class="nav-link ' . $sub_active . '" style="padding-left: 10px!important;">' . $label . '</a>';
                                    }
                                    $sub .= '</div>';

                                    $link = '#';
                                    $attrs =
                                        'class="nav-link ' .
                                        $main_active .
                                        '"
                                                    href="#' .
                                        $sub_id .
                                        '"
                                                    role="button"
                                                    data-bs-target="#' .
                                        $sub_id .
                                        '"
                                                    aria-expanded="false"
                                                    aria-controls="' .
                                        $sub_id .
                                        '"';
                                } else {
                                    $sub = '';
                                    $link = @$m[2];

                                    if (($link != '/' && str_contains($current_uri, $link)) || $current_uri == $link) {
                                        $main_active = 'active';
                                    } else {
                                        $main_active = '';
                                    }
                                    $links[] = $link;

                                    $attrs = 'class="nav-link ' . $main_active . '" href="' . $link . '"';
                                }

                                echo '<div class="nav-item">
                                                    <a ' .
                                    $attrs .
                                    '>
                                                        <i class="' .
                                    @$m[1] .
                                    ' nav-icon"></i>
                                                        <span class="nav-link-title">' .
                                    @$m[0] .
                                    '</span>
                                                    </a>' .
                                    $sub .
                                    '</div>';
                            }

                            // if(!in_array($current_uri, array_merge($links, $add_links))){
                            //     echo "<script>window.location = '/logout';</script>";
                            // }

                        @endphp

                    </div>
                </div>
            </div>

            <div class="navbar-vertical-footer">
                <ul class="navbar-vertical-footer-list">
                    <li class="navbar-vertical-footer-list-item">
                        <div class="dropdown dropup">
                            <button type="button" class="btn btn-ghost-secondary btn-icon rounded-circle"
                                id="selectThemeDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                data-bs-dropdown-animation>

                            </button>

                            <div class="dropdown-menu navbar-dropdown-menu navbar-dropdown-menu-borderless"
                                aria-labelledby="selectThemeDropdown">
                                <a class="dropdown-item" href="#" data-icon="bi-moon-stars" data-value="auto">
                                    <i class="bi-moon-stars me-2"></i>
                                    <span class="text-truncate" title="Auto (system default)">Auto (system
                                        default)</span>
                                </a>
                                <a class="dropdown-item" href="#" data-icon="bi-brightness-high" data-value="default">
                                    <i class="bi-brightness-high me-2"></i>
                                    <span class="text-truncate" title="Default (light mode)">Default (light mode)</span>
                                </a>
                                <a class="dropdown-item active" href="#" data-icon="bi-moon" data-value="dark">
                                    <i class="bi-moon me-2"></i>
                                    <span class="text-truncate" title="Dark">Dark</span>
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>
