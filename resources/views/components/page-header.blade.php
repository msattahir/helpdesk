@props(['page_title', 'add_button_label'])

<div class="page-header print-hide">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-header-title">{{@$page_title}}</h1>

            @if (@$page_title != 'Dashboard')

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-no-gutter">
                    <li class="breadcrumb-item"><a class="breadcrumb-link" href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{@$page_title}}</li>
                </ol>
            </nav>

            @endif

        </div>

        @if(@$add_button_label != '')
        <div class="col-sm-auto">
            <a class="btn btn-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#register-modal" name="add" id="add">
                <i class="bi-plus me-1"></i> {{@$add_button_label}}
            </a>
        </div>
        @endif
    </div>
</div>
