@php
$label_p = 'Reports';
$label_s = 'Report';
@endphp

<x-layout :page_title="$label_p">
    <div class="card print-hide">
        <div class="card-header">

            <form id="report-form">
                <div class="mb-2 row">
                    <div class="col-sm-10 row">
                        <div class="col-sm-3 mb-2">
                            <select name="report_type" class="form-select" required>
                                <option value="" disabled selected>Report Type</option>
                                {!! get_report_type_options() !!}
                            </select>
                        </div>
                        <div class="col-sm-3 mb-2">
                            <select name="report_by" class="form-select" required>
                                <option value="" disabled selected>Report By</option>
                            </select>
                        </div>
                        <div class="col-sm-3 mb-2">
                            <input type="text" name="date_from" class="js-flatpickr form-control flatpickr-custom"
                                placeholder="Date From" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-sm-3 mb-2">
                            <input type="text" name="date_to" class="js-flatpickr form-control flatpickr-custom"
                                placeholder="Date To" max="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <button class="btn btn-primary col-sm-12" name="generate" type="submit">
                            Generate
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <div id="response" class="m-2 print-hide"></div>
    <div id="print-div" class="print-wide"></div>
</x-layout>

<script>
    (function() {
        HSCore.components.HSFlatpickr.init('.js-flatpickr');
        $('.js-flatpickr').flatpickr({
            "minDate": "2010-4-22",
            "maxDate": "today"
        });
    })();

    $(document).ready(function() {
        $(document).on('submit', '#report-form', function(e) {
            e.preventDefault();
            $('#response').html("");

            var fd = new FormData(this);
            fd.append("_token", "{{ csrf_token() }}");

            Pace.restart();
            Pace.track(function() {
                $.ajax({
                    url: '/reports/generate',
                    type: "POST",
                    dataType: 'json',
                    data: fd,
                    cache: false,
                    processData: false,
                    contentType: false,
                    async: false
                }).done(function(data) {
                    $('#print-div').html(data.result);

                    $([document.documentElement, document.body]).animate({
                        scrollTop: $('#response').offset().top - 150
                    }, 500);
                }).fail(function(jqXHR, ajaxOptions, thrownError) {
                    console.log(jqXHR);

                    if (jqXHR.responseJSON.errors) {
                        $(response_selector).html(display_error(jqXHR.responseJSON
                            .errors));
                    }
                });
            });
        });

        $(document).on('change', '#report-form [name="report_type"]', function (e, callback) {
            let $val = $(this).val();
            let $div = $('#report-form [name="report_by"]');

            Pace.restart();
            Pace.track(function(){
                $div.find('option:not(:first)').remove();
                $div.append(get_report_by_options($val));
            });
        });
    });

</script>
