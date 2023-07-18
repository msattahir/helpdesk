<div class="dropdown">
    <button type="button" class="btn btn-white btn-sm w-100 btn-open-filter" id="usersFilterDropdown"
        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <i class="bi-filter me-1"></i> Filter <span class="badge bg-soft-dark text-dark rounded-circle ms-1" id="filter-counter"></span>
    </button>

    <div class="dropdown-menu dropdown-menu-sm-end dropdown-card card-dropdown-filter-centered"
        style="min-width: 30rem;">
        <div class="card">
            <div class="card-header card-header-content-between">
                <h5 class="card-header-title">Filter</h5>

                <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm ms-2 btn-close-filter"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <i class="bi-x-lg"></i>
                </button>
            </div>

            <div class="card-body">
                {{$slot}}
            </div>
        </div>
    </div>
</div>