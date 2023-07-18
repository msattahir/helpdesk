@php
    $page_title = 'Change Password';
@endphp

<x-layout :page_title="$page_title">
    <div class="mx-auto" style="max-width: 40rem;">
        <div class="card card-lg mb-5">
            <div class="card-body">
                <form id="auth-form">
                    @csrf
                    @method('POST')

                    <div class="text-center">
                        <div class="mb-5">
                            <h1 class="display-5">
                                <i class="bi-pencil-square">&nbsp;</i>
                                Change Password
                            </h1>
                        </div>
                    </div>
                    <hr>

                    <div id="response" class="mb-4"></div>

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
                        <button type="submit" class="btn btn-primary btn-lg">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>

<script>
$(document).ready(function () {
    submit_form({
        form_selector: '#auth-form',
        modal_selector: '',
        response_selector: '#response',
        url: ''
    });
});
</script>
