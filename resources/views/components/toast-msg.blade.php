@php
    $previous_uri = parse_url(url()->previous(), PHP_URL_PATH);
    $current_uri = request()->path();

    if($current_uri == '/' && auth()->check()){
        if($previous_uri == "/login"){
            $toast_msg = 'Welcome! Logged in successfully';
        }elseif($previous_uri == "/reset-password"){
            $toast_msg = 'Welcome! Password reset successfully';
        }
    }elseif(!auth()->check()){
        if(
            $current_uri == 'login' &&
            !in_array($previous_uri, ['', '/login', '/reset-password'])
        ){
            $toast_msg = 'Logged out successfully';
        }elseif($current_uri == 'reset-password'){
            $toast_msg = 'Reset password first';
        }
    }
@endphp
@if(@$toast_msg != "")
<div class="position-fixed toast show bg-white" role="alert" aria-live="assertive" aria-atomic="true" style="top: 20px; right: 20px; z-index: 1000; border-color:var(--bs-border-color)">
    <div class="toast-body">
        <div class="d-flex align-items-center flex-grow-1">
            <div class="flex-grow-1 ms-3">
                <h5 class="mb-0" style="color:#006717!important">
                    {{$toast_msg}}
                </h5>
            </div>
            <div class="text-end">
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>
@endif
