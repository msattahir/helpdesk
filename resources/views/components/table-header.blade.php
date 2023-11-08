@props(['label'])
<div class="card-header card-header-content-md-between">
    <div class="mb-2 mb-md-0">
        <form>
            <div class="input-group input-group-merge input-group-flush">
                <div class="input-group-prepend input-group-text">
                    <i class="bi-search"></i>
                </div>
                <input id="datatableSearch" type="search" class="form-control" placeholder="Search ..." aria-label="Search ...">
            </div>
        </form>
    </div>

    <div class="d-grid d-sm-flex justify-content-md-end align-items-sm-center gap-2">
        <div id="datatableCounterInfo" style="display: none;">
            <div class="d-flex align-items-center">
                <span class="fs-5 me-3">
                    <span id="datatableCounter">0</span>
                    Selected
                </span>
                <a class="btn btn-outline-danger btn-sm" href="javascript:;">
                    <i class="bi-trash"></i> Delete
                </a>
            </div>
        </div>

        <div class="dropdown">
            <button type="button" class="btn btn-white btn-sm dropdown-toggle w-100" id="usersExportDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi-download me-2"></i> Export
            </button>

            <div class="dropdown-menu dropdown-menu-sm-end" aria-labelledby="usersExportDropdown">
                <span class="dropdown-header">Options</span>
                <a id="export-copy" class="dropdown-item" href="javascript:;">
                    <img class="avatar avatar-xss avatar-4x3 me-2"
                        src="{{asset('assets/svg/illustrations/copy-icon.svg')}}" alt="Image Description">
                    Copy
                </a>
                <a id="export-print" class="dropdown-item" href="javascript:;">
                    <img class="avatar avatar-xss avatar-4x3 me-2"
                        src="{{asset('assets/svg/illustrations/print-icon.svg')}}" alt="Image Description">
                    Print
                </a>
                <div class="dropdown-divider"></div>
                <span class="dropdown-header">Download options</span>
                <a id="export-excel" class="dropdown-item" href="javascript:;">
                    <img class="avatar avatar-xss avatar-4x3 me-2" src="{{asset('assets/svg/brands/excel-icon.svg')}}"
                        alt="Image Description">
                    Excel
                </a>
                <a id="export-csv" class="dropdown-item" href="javascript:;">
                    <img class="avatar avatar-xss avatar-4x3 me-2"
                        src="{{asset('assets/svg/components/placeholder-csv-format.svg')}}" alt="Image Description">
                    .CSV
                </a>
                <a id="export-pdf" class="dropdown-item" href="javascript:;">
                    <img class="avatar avatar-xss avatar-4x3 me-2" src="{{asset('assets/svg/brands/pdf-icon.svg')}}"
                        alt="Image Description">
                    PDF
                </a>
            </div>
        </div>
        {{$slot}}
    </div>
</div>
