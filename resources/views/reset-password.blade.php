@php
    $page_title = "Reset Password";
@endphp

<x-auth-layout :page_title="@$page_title">
    <x-toast-msg/>

    <form id="auth-form">
        @csrf
        @method('POST')
        <div class="text-center">
            <div class="mb-5">
                <h1 class="display-5">
                    <i class="bi-key">&nbsp;</i>
                    Reset Password
                </h1>
            </div>
        </div>
        <hr>

        <div id="response" class="mb-4"></div>

        <input type="hidden" staff_no="{{@$staff_no}}">
        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group input-group-merge">
                <input type="password" class="form-control form-control-lg" name="password" placeholder="Password">
                <button type="button" class="input-group-append input-group-text toggle-password">
                    <i class="bi-eye-slash"></i>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">New Password</label>
            <div class="input-group input-group-merge">
                <input type="password" class="form-control form-control-lg" name="new_password" placeholder="New Password">
                <button type="button" class="input-group-append input-group-text toggle-password">
                    <i class="bi-eye-slash"></i>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Confirm New Password</label>
            <div class="input-group input-group-merge">
                <input type="password" class="form-control form-control-lg" name="new_password_confirmation" placeholder="Confirm New Password">
                <button type="button" class="input-group-append input-group-text toggle-password">
                    <i class="bi-eye-slash"></i>
                </button>
            </div>
        </div>

        <div class="d-grid my-8">
            <button type="submit" class="btn btn-primary btn-lg">{{@$page_title}}</button>
        </div>
    </form>
</x-auth-layout>
<script>
    $(document).on('submit', '#auth-form', function(e) {
        e.preventDefault();
        if(original_form == $('#auth-form').serialize()){
            $('#response').html('<div class="alert alert-soft-danger msg mb-5">No change made on the form</div>');
            $([document.documentElement, document.body]).animate({
                scrollTop: $('#response').offset().top - 150
            }, 500);
            return;
        }
        original_form = $('#auth-form').serialize();
        $('#response').html("");
        var fd = new FormData(this);
        fd.append("_token", "{{ csrf_token() }}");

        Pace.restart();
        Pace.track(function(){
            $.ajax({
                url: "",
                type: "POST",
                dataType: 'json',
                data: fd,
                cache: false,
                processData: false,
                contentType: false,
                async: false
            }).done(function (data) {
                if(data.status == "success"){
                    window.location = '/';
                }else{
                    $('#response').html('<div class="alert alert-soft-danger msg">' + data.message + '</div>')
                    .fadeIn(0)
                    .delay(5000)
                    .fadeOut(2000);
                }

            }).fail(function(jqXHR, ajaxOptions, thrownError){
                console.log(jqXHR);

                if(jqXHR.responseJSON.errors){
                    $('#response').html(display_error(jqXHR.responseJSON.errors))
                    .fadeIn(0)
                    .delay(5000)
                    .fadeOut(2000);
                }
            });
        });
    });
</script>
