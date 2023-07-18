@php
    $page_title = "Log In";
@endphp

<x-auth-layout :page_title="@$page_title">
    <x-toast-msg/>

    <form id="auth-form">
        @csrf
        @method('POST')
        <div class="text-center">
            <div class="mb-5">
                <h1 class="display-5">
                    <i class="bi-box-arrow-in-right">&nbsp;</i>
                    Log In
                </h1>
            </div>
        </div>
        <hr>

        <div id="response" class="mb-4"></div>

        <div class="mb-4">
            <label class="form-label">Staff No</label>
            <input type="text" class="form-control form-control-lg" name="staff_no" placeholder="Staff No" required>
        </div>

        <div class="mb-10">
            <label class="form-label w-100">
                <span class="d-flex justify-content-between align-items-center">
                    <span>Password</span>
                    {{-- <a class="form-label-link mb-0"
                        href="./authentication-reset-password-basic.html">Forgot
                        Password?</a> --}}
                </span>
            </label>

            <div class="input-group input-group-merge">
                <input type="password" class="form-control form-control-lg" name="password" placeholder="Password">
                <button type="button" class="input-group-append input-group-text toggle-password">
                    <i class="bi-eye-slash"></i>
                </button>
            </div>
        </div>

        <div class="d-grid mb-8">
            <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
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
