@props(['table_id', 'totalQty_id', 'search_id', 'entries_id', 'pagination'])
<div class="table-responsive datatable-custom position-relative">
    <table id="{{ $table_id ?? "records-table" }}"
        class="table table-lg table-thead-bordered table-align-top card-table" data-hs-datatables-options='{
               "order": [],
               "info": {
                 "totalQty": "{{ $totalQty_id ?? "#datatableWithPaginationInfoTotalQty" }}"
               },
               "search": "{{ $search_id ?? "#datatableSearch" }}",
               "entries": "{{ $entries_id ?? "#records_per_page" }}",
               "pageLength": 10,
               "isResponsive": false,
               "isShowPaging": false,
               "pagination": "{{ $pagination ?? "datatablePagination" }}"
             }'>
        <thead class="thead-light">
        </thead>
    </table>
</div>
