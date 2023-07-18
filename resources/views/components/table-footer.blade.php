@props(['totalQty_id', 'entries_id', 'pagination'])
<div class="card-footer">
    <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
        <div class="col-sm mb-2 mb-sm-0">
            <div class="d-flex justify-content-center justify-content-sm-start align-items-center">
                <span class="me-2">Showing:</span>

                <div class="tom-select-custom">
                    <select id="{{ $entries_id ?? "records_per_page" }}" class="js-select form-select form-select-borderless w-auto"
                        autocomplete="off" data-hs-tom-select-options='{"searchInDropdown": false, "hideSearch": true}'>
                    </select>
                </div>

                <span class="text-secondary me-2">of</span>

                <span id="{{ $totalQty_id ?? "datatableWithPaginationInfoTotalQty" }}"></span>
            </div>
        </div>

        <div class="col-sm-auto">
            <div class="d-flex justify-content-center justify-content-sm-end">
                <nav id="{{ $pagination ?? "datatablePagination" }}" aria-label="Activity pagination"></nav>
            </div>
        </div>
    </div>
</div>
