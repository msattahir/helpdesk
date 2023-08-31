@php
    $page_title = "Forgot Password";
@endphp

<x-auth-layout :page_title="@$page_title">
    <x-toast-msg/>

    <form id="auth-form">
        @csrf
        @method('POST')
        <div class="text-center">
            <div class="mb-5">
                <h1 class="display-5">
                    <i class="bi-shield-lock">&nbsp;</i>
                    {{ $page_title }}
                </h1>
                <p>Enter your official e-mail address registered on the system and we'll send you instructions to reset your password.</p>
            </div>
        </div>
        <hr>

        <div id="response" class="mb-4"></div>

        <div class="mb-10">
            <label class="form-label">E-mail Address</label>
            <input type="text" class="form-control form-control-lg" name="email" placeholder="Email Address" required>
        </div>

        <div class="d-grid mb-8">
            <button type="submit" class="btn btn-primary btn-lg">Sign In</button>

            <x-login-link />
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
                }else if(data.status == "reset"){
                    window.location = '/reset-password?staff_no=' + encodeURIComponent(data.staff_no);
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
