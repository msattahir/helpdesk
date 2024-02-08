<script src="{{asset('assets/js/vendor.min.js')}}"></script>
<script src="{{asset('assets/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js')}}"></script>
<script src="{{asset('assets/js/theme.min.js')}}"></script>
<script src="{{asset('assets/js/hs.theme-appearance-charts.js')}}"></script>
<script src="{{asset('assets/js/highcharts.js')}}"></script>
<script src="{{asset('assets/js/pace.min.js')}}"></script>
<script>
    (function () {
        $(document).on('ready', function () {
            HSCore.components.HSDaterangepicker.init('.js-daterangepicker-clear', {
                minDate: '04/22/2010',
                maxDate: moment(),
                locale: {
                    format: 'DD/MM/YYYY'
                }
            });

            $('.js-daterangepicker-clear').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            })

            $('.js-daterangepicker-clear').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('')
            })
        });
    })();
    (function () {
        localStorage.removeItem('hs_theme')

        window.onload = function () {
            new HSSideNav('.js-navbar-vertical-aside').init()
        }
    })();
</script>
<script>
    (function () {
        const $dropdownBtn = document.getElementById('selectThemeDropdown')
        const $variants = document.querySelectorAll(`[aria-labelledby="selectThemeDropdown"] [data-icon]`)

        const setActiveStyle = function () {
            $variants.forEach($item => {
                if ($item.getAttribute('data-value') === HSThemeAppearance.getOriginalAppearance()) {
                    $dropdownBtn.innerHTML = `<i class="${$item.getAttribute('data-icon')}" />`
                    return $item.classList.add('active')
                }

                $item.classList.remove('active')
            })
        }

        $variants.forEach(function ($item) {
            $item.addEventListener('click', function () {
                HSThemeAppearance.setAppearance($item.getAttribute('data-value'))
            })
        })

        setActiveStyle()

        window.addEventListener('on-hs-appearance-change', function () {
            setActiveStyle()
        })
    })()
</script>

<script>
    var original_form;
    var datatable;
    var delete_id;

    var filter;
    var date_from;
    var date_to;
    var allocation_period;

    function initialize_datatable(
        table_id = '#records-table',
        columns = [],
        url = '',
        entries_id = '#records_per_page'
    ){

        $(document).on('click', '.btn-close-filter', function() {
            $(this).closest('.dropdown').find('.btn-open-filter').dropdown("toggle");
        });

        columns = [{name: 'id', data: 'id', visible: false}].concat(columns);

        HSCore.components.HSDatatables.init($(table_id), {
            destroy: true,
            processing: true,
            serverSide: true,
            order: [ [0, 'desc'] ],
            buttons: [
              {
                extend: 'copy',
                className: 'd-none',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
              },
              {
                extend: 'excel',
                className: 'd-none',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
              },
              {
                extend: 'csv',
                className: 'd-none',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
              },
              {
                extend: 'pdf',
                className: 'd-none',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
              },
              {
                extend: 'print',
                className: 'd-none',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
              },
            ],
            select: {
              style: 'multi',
              selector: 'td:first-child input[type="checkbox"]',
              classMap: {
                checkAll: '#datatableCheckAll',
                counter: '#datatableCounter',
                counterInfo: '#datatableCounterInfo'
              }
            },
            language: {
              zeroRecords: `<div class="text-center p-4">
                  <img class="mb-3" src="{{asset('assets/svg/illustrations/oc-error.svg')}}" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="default">
                  <img class="mb-3" src="{{asset('assets/svg/illustrations-light/oc-error.svg')}}" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="dark">
                <p class="mb-0">No record found</p>
                </div>`
            },
            ajax: {
                url: url,
                data:function (d) {
                    var filter_data = $('#filter-form').serializeArray();
                    $.each(filter_data, function(k, v) {
                        d[v.name] = v.value;
                    });
                    d.date_from = date_from;
                    d.date_to = date_to;
                    d.allocation_period = allocation_period;
                }
            },
            columns: columns
        });
        datatable = HSCore.components.HSDatatables.getItem(0);

        $('#export-copy').click(function () {
            datatable.button('.buttons-copy').trigger()
        });

        $('#export-excel').click(function () {
            datatable.button('.buttons-excel').trigger()
        });

        $('#export-csv').click(function () {
            datatable.button('.buttons-csv').trigger()
        });

        $('#export-pdf').click(function () {
            datatable.button('.buttons-pdf').trigger()
        });

        $('#export-print').click(function () {
            datatable.button('.buttons-print').trigger()
        });

        $('.js-datatable-filter').on('change', function () {
            var $this = $(this),
                val = $this.val(),
                target_col = $this.data('target-column-name');

            if (val === 'null' || val === ''){
                val = '.*';
            }else{
                val = val.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
            }
            var regExSearch = '^' + val + '$';

            datatable.column(target_col + ':name').search(regExSearch, true, false).draw();

            update_filter_count();
        });
        $(document).on("reset", "#filter-form", function(e) {
            setTimeout(function() {
                date_from = '';
                date_to = '';
                allocation_period = '';
                datatable.columns().search( '' ).draw();

                $('#filter-counter').html("");
            }, 1);
        });

        var update_entries = true;

        $(document).on('draw.dt', datatable, function() {
            if(update_entries){
                var records_total = datatable.page.info().recordsTotal;

                var options = '<option selected>10</option>';
                var entries = ['20','30','40','50','100'];

                $.each(entries, function(i, v) {
                    if(v < records_total){
                        options += '<option>' + v + '</option>';
                    }else{
                        return false;
                    }
                });
                options += '<option value="' + records_total + '">ALL</option>';
                $(entries_id).html(options);

                update_entries = false;
            }
        });

        $(document).on('processing.dt', datatable, function ( e, settings, processing ) {
            if(processing){
                Pace.start();
            } else {
                Pace.stop();
            }
        });

        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        });
    }
    function manage_records(args = {}){
        var url = ('url' in args) ? args['url'] : '';

        submit_form(args);

        $(document).on('click', '[name="delete"]', function(e) {
            delete_id = $(this).data('id');
            $('#delete-modal').modal('show');
        });

        $(document).on('click', '#delete-confirm', function(e) {
            Pace.restart();
            Pace.track(function(){
                $.ajax({
                    url: url+'/'+delete_id,
                    type: 'DELETE',
                    dataType: 'json',
                    data: {
                        "_token": "{{ csrf_token() }}",
                    }
                }).done(function (data) {
                    $('#response').html('<div class="alert alert-soft-' + data.status + ' msg">' + data.message + '</div>')
                    .fadeIn(0)
                    .delay(5000)
                    .fadeOut(2000);
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $("#response").offset().top - 150}, 500);

                    $('#delete-modal').modal('hide');
                    datatable.ajax.reload();
                    console.log(data.error);
                }).fail(function(jqXHR, ajaxOptions, thrownError){
                    console.log(jqXHR);

                    if (
                        typeof jqXHR.responseJSON !== 'undefined' &&
                        typeof jqXHR.responseJSON.errors !== 'undefined'
                    ) {
                        $("#response").html(display_error(jqXHR.responseJSON.errors))
                        .fadeIn(0)
                        .delay(5000)
                        .fadeOut(2000);
                    }
                });
            });
        });
    }

    function submit_form(args = {}){
        var form_selector = ('form_selector' in args) ? args['form_selector'] : '#register-form';
        var modal_selector = ('modal_selector' in args) ? args['modal_selector'] : '#register-modal';
        var response_selector = ('response_selector' in args) ? args['response_selector'] : '#modal-response';
        var url = ('url' in args) ? args['url'] : '';

        $(document).on('submit', form_selector, function(e) {
            e.preventDefault();
            if(original_form == $(form_selector).serialize()){
                $(response_selector).html('<div class="alert alert-soft-danger msg mb-5">No change made on the form</div>')
                .fadeIn(0)
                .delay(1000)
                .fadeOut(1000);

                if(modal_selector != ""){
                    $(modal_selector).animate({scrollTop: $(response_selector).offset().top - 150}, 500);
                }
                return;
            }
            original_form = $(form_selector).serialize();
            $(response_selector).html("");
            var fd = new FormData(this);

            var submit_url = url;

            if($(form_selector).find('[name="_method"]').val() == "PUT"){
                submit_url = url + '/' + $(form_selector).find('[name="id"]').val();
            }

            Pace.restart();
            Pace.track(function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: submit_url,
                    type: 'POST',
                    dataType: 'json',
                    data: fd,
                    cache: false,
                    processData: false,
                    contentType: false,
                    async: false
                }).done(function (data) {
                    $(response_selector).html('<div class="alert alert-soft-' + data.status + ' msg">' + data.message + '</div>')
                    .fadeIn(0)
                    .delay(5000)
                    .fadeOut(2000);

                    if(modal_selector != ""){
                        $(modal_selector).animate({
                            scrollTop: $(response_selector).offset().top - 150
                        }, 500);

                        datatable.ajax.reload();
                    }else{
                        $([document.documentElement, document.body]).animate({
                            scrollTop: $(response_selector).offset().top - 150
                        }, 500);
                    }
                    console.log(data);
                }).fail(function(jqXHR, ajaxOptions, thrownError){
                    console.log(jqXHR);

                    if (
                        typeof jqXHR.responseJSON !== 'undefined' &&
                        typeof jqXHR.responseJSON.errors !== 'undefined'
                    ) {
                        $(response_selector).html(display_error(jqXHR.responseJSON.errors))
                        .fadeIn(0)
                        .delay(5000)
                        .fadeOut(2000);
                    }
                });
            });
        });
    }

    function update_filter_count(){
        var filter_count = $('#filter-form input, #filter-form select').filter(function(){
            return $(this).val();
        }).length;

        if(filter_count == 0){
            filter_count = "";
        }
        $('#filter-counter').html(filter_count);
    }

    function display_error(errors){
        var $return = '';
        var count = Object.keys(errors).length;

        if(count == 1){
            $return = '<div class="alert alert-soft-danger msg">' + Object.values(errors)[0] + '</div>';
        }else if(count > 1){
            $return = '<div class="alert alert-soft-danger msg" style="text-align: left;"><span>The following error(s) occurred:</span><ol style="margin-bottom:10px;font-weight:100;">';
            $.each(errors, function (key, val) {
                $return += '<li>' + val + '</li>';
            });
        }

        return $return;
    }
    $(function() {
        $(document).on('click', '.toggle-password', function() {
            var passwordField = $(this).siblings('input');
            var passwordFieldType = passwordField.prop('type');
            var newPasswordFieldType = (passwordFieldType === 'password') ? 'text' : 'password';
            passwordField.prop('type', newPasswordFieldType);

            var iconClass = (newPasswordFieldType === 'password') ? 'bi-eye-slash' : 'bi-eye';
            $(this).html('<i class="' + iconClass + '"></i>');
        });
    });
    $(function() {
        $(document).on('input', '.validate-uppercase', function(){
            this.value = this.value
                .toUpperCase();
        });
        $(document).on('input', '.validate-office-no', function() {
        this.value = this.value
            .replace(/[^\d.]/g, '')
            .replace(/(^[\d]{4})[\d]/g, '$1');
        });
        $(document).on('input', '.validate-staff-no', function() {
        this.value = this.value
            .replace(/[^\d.]/g, '')
            .replace(/(^[\d]{5})[\d]/g, '$1');
        });
        $(document).on('input', '.validate-quantity', function() {
        this.value = this.value
            .replace(/[^\d.]/g, '')
            .replace(/(^[\d]{8})[\d]/g, '$1')
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        });
        $(document).on('blur', '.validate-quantity', function(){
            this.value = this.value
            .replace(/[^\d.]/g, '');
        });
        $(document).on('focus', '.validate-quantity', function(){
            this.value = this.value
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        });
    });
    $(function() {
        $(document).on('input', '.validate-decimal', function(){
            this.value = this.value
            .replace(/[^\d.]/g, '')
            .replace(/^[^\d]/g, '')
            .replace(/(^[\d]{12})[\d]/g, '$1')
            .replace(/^(0)0+/g, '$1')
            .replace(/^[0]([1-9])/g, '$1')
            .replace(/(\..*)\./g, '$1')
            .replace(/(\.[\d]{2})./g, '$1')
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        });
        $(document).on('blur', '.decimalValue', function(){
            this.value = this.value
            .replace(/[^\d.]/g, '');
        });
        $(document).on('focus', '.decimalValue', function(){
            this.value = this.value
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        });
    });

    function format_date_time(timestamp) {
        var date = new Date(Date.parse(timestamp)),
            month = '' + (date.getMonth() + 1),
            day = '' + date.getDate(),
            year = date.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return '<h5>' + [year, month, day].join('-') + '</h5>' +
        date.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }

    function format_date_time_local(datetime_str) {
        var date = new Date(datetime_str);

        return date.getFullYear() + '-' +
            (date.getMonth() + 1).toString().padStart(2, '0') + '-' +
            date.getDate().toString().padStart(2, '0') + 'T' +
            date.getHours().toString().padStart(2, '0') + ':' +
            date.getMinutes().toString().padStart(2, '0');
    }

    function format_label(label) {
        if (["Pending", "Blocked", "Allocated"].includes(label)) {
            return '<span class="badge bg-soft-warning text-warning">' +
                '<span class="legend-indicator bg-warning"></span>' + label +
            '</span>';
        } else if (["Escalated", "In-Progress", "Configured"].includes(label)) {
            return '<span class="badge bg-soft-primary text-primary">' +
                '<span class="legend-indicator bg-primary"></span>' + label +
            '</span>';
        } else if (["Resolved", "Active", "Now", "Installed", "Distributed"].includes(label)) {
            return '<span class="badge bg-soft-success text-success">' +
                '<span class="legend-indicator bg-success"></span>' + label +
            '</span>';
        } else if (["Unresolved", "Retired", "Returned"].includes(label)) {
            return '<span class="badge bg-soft-danger text-danger">' +
                '<span class="legend-indicator bg-danger"></span>' + label +
            '</span>';
        }
        return label;
    }

    function format_integer(num, include_zero = false) {
        num = parseInt(num);
        if (num === 0) {
            return include_zero ? '0' : '';
        } else {
            let tmp = num.toString().split('.');
            let dp = (tmp[1] && tmp[1].length > 2) ? 2 : (tmp[1] ? tmp[1].length : 0);
            return num.toFixed(dp).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
    }


    function get_report_by_options (default_val = "") {
        let returnHTML = "";
        let options = [];

        if(default_val == 'Helpdesk Supports'){
            options = ["Categories", "Floors", "DDDs", "Staff"];
        } else {
            options = ["Floors", "DDDs"];
        }

        if (default_val === "validate") {
            return options;
        }

        options.forEach(value => {
            returnHTML += `<option ${default_val === value ? "selected" : ""}>${value}</option>`;
        });

        return returnHTML;
    }

    function replace_template_values(html, row) {
        const regex = /row\.([a-z._]+)/gi;
        let updated_html = html;

        const matches = html.match(regex);
        if (matches) {
            matches.forEach((match) => {
                const propName = match.replace('row.', '');
                const propValue = get_prop_by_string(row, propName);
                updated_html = updated_html.replaceAll(match, propValue);
            });
        }

        return updated_html;
    }

    function get_prop_by_string(obj, prop_str) {
        const prop_arr = prop_str.split('.');
        let property = obj;

        prop_arr.forEach((prop) => {
            if (property.hasOwnProperty(prop)) {
                property = property[prop];
            } else {
                property = '';
                return;
            }
        });

        return property;
    }

    function replace_slots(input_string, replacements) {
        var slot_index = 0;

        var output_string = input_string.replace(/\$slot/g, function(match) {
            if (slot_index < replacements.length) {
                var replacement = replacements[slot_index];
                slot_index++;
                return replacement;
            }
            return match;
        });

        return output_string;
    }

    function call_php_function(function_name, data, callback) {
        var result = null;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/call-php-function',
            type: 'POST',
            data: {
                function_name: function_name,
                data: data
            },
            async: false,
            success: function(response) {
                result = response;
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
        return result;
    }
</script>
