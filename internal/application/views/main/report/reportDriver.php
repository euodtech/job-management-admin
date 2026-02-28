<style>
    /* Full-width table with centered cells */
    #tableUserLogin {
        width: 100% !important;
    }
    table#tableUserLogin.dataTable thead th {
        text-align: center !important;
    }
    #tableUserLogin tbody tr td {
        vertical-align: middle;
        text-align: center;
    }

    /* Compact search label and input */
    #tableUserLogin_wrapper .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        margin: 0;
    }

    /* Child row styles for DataTables responsive mode */
    #tableUserPerformance tr.child td {
        text-align: left !important;
        background-color: #ffffffff;
        white-space: nowrap;
    }

    table.dataTable>tbody>tr.child span.dtr-title {
        display: inline-block;
        font-weight: bold;
    }

    /* Mobile & Tablet (< 992px) */
    @media (max-width: 991.98px) {
        table.dataTable>tbody>tr.child ul.dtr-details {
            display: flex;
            list-style-type: none;
            margin: 0;
            padding: 0;
            align-content: flex-start;
            flex-wrap: nowrap;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
        }

        table.dataTable>tbody>tr.child ul.dtr-details>li {
            border-bottom: 1px solid #efefef;
            padding: 0.5em 0;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: flex-start;
            align-items: flex-start;
        }

        table.dataTable>tbody>tr.child span.dtr-data {
            text-align: center !important;
            width: 100px !important;
            margin-left: 100px;
            flex: 3;
        }
    }
</style>

<div class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-xl font-bold text-gray-800"><?= $title ?></h1>
        <nav class="flex">
            <ol class="flex items-center gap-1.5 text-sm">
                <?php foreach ($breadcrumbs as $i => $crumb): ?>
                    <?php if ($i > 0): ?>
                        <li class="text-gray-400">/</li>
                    <?php endif; ?>
                    <?php if ($crumb['url']): ?>
                        <li><a href="<?= $crumb['url'] ?>" class="text-primary hover:underline"><?= $crumb['label'] ?></a></li>
                    <?php else: ?>
                        <li class="text-gray-500"><?= $crumb['label'] ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>
</div>

<div class="px-4 sm:px-6 lg:px-8 pb-6">
    <div class="bg-white rounded-xl shadow-sm border border-cyan-200">
        <div class="px-5 py-4 border-b border-cyan-200">
            <div class="flex flex-wrap items-center justify-between gap-2.5">
                <h5 class="text-base font-semibold text-gray-800">Rider Report</h5>
                <button type="button" id="btn_export_driver_report" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                    <i class="fa fa-file-excel text-green-600"></i> Export Excel
                </button>
            </div>
        </div>
        <div class="px-5 py-4">
            <!-- Preset date filter buttons -->
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="text-sm font-medium text-gray-600 mr-1">Period:</span>
                <button type="button" class="date-preset-btn rounded-full px-4 py-1.5 text-sm font-medium border transition-all duration-200 cursor-pointer" data-preset="1day">Today</button>
                <button type="button" class="date-preset-btn rounded-full px-4 py-1.5 text-sm font-medium border transition-all duration-200 cursor-pointer" data-preset="7days">7 Days</button>
                <button type="button" class="date-preset-btn rounded-full px-4 py-1.5 text-sm font-medium border transition-all duration-200 cursor-pointer" data-preset="1month">1 Month</button>
                <button type="button" class="date-preset-btn rounded-full px-4 py-1.5 text-sm font-medium border transition-all duration-200 cursor-pointer" data-preset="custom">Custom</button>
            </div>
            <!-- Custom date range (hidden by default) -->
            <form id="formFilterUserLoginActivityReport" method="GET" action="">
                <div id="customDateRange" class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-3 mb-3 overflow-hidden transition-all duration-300 ease-in-out" style="max-height: 0; opacity: 0; margin-bottom: 0;">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" name="from_UserLoginActivityReport" id="from_UserLoginActivityReport" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" value="<?= date('Y-m-d', strtotime('-6 days')) ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Until Date</label>
                        <input type="date" name="until_UserLoginActivityReport" id="until_UserLoginActivityReport" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors cursor-pointer"><svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg> Apply</button>
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer" id="resetFilterUserLoginActivityReport">Reset</button>
                    </div>
                </div>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full text-sm dt-responsive nowrap" width="100%" id="tableUserLogin">
                    <thead class="bg-gray-50">
                        <tr class="text-center whitespace-nowrap">
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Fullname</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Email</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Cancel Job</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Total Job</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Complete Job</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Ongoing Job</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>
</div>


<div id="modal_detail_job" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modal_detail_job_header">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-4xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800" id="modal_detail_job_header"></h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal_detail_job">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 overflow-y-auto">
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function() {
        $.fn.dataTable.ext.errMode = 'none';
        $(document).on('error.dt', function(e, settings, techNote, message) {
            console.error('DataTables error:', message);
        });
    });

    // User Login Activity
    $(document).ready(function() {

        // --- Date preset helpers ---
        function formatDate(date) {
            return date.getFullYear() + '-' +
                String(date.getMonth() + 1).padStart(2, '0') + '-' +
                String(date.getDate()).padStart(2, '0');
        }

        function getPresetRange(preset) {
            var today = new Date();
            var from = new Date();
            switch (preset) {
                case '1day':
                    // today only
                    break;
                case '7days':
                    from.setDate(today.getDate() - 6);
                    break;
                case '1month':
                    from.setDate(today.getDate() - 30);
                    break;
            }
            return { from: formatDate(from), until: formatDate(today) };
        }

        function setActivePreset(preset) {
            $('.date-preset-btn').each(function() {
                if ($(this).data('preset') === preset) {
                    $(this).removeClass('border-gray-300 bg-white text-gray-700 hover:bg-gray-50')
                           .addClass('bg-primary border-primary text-white');
                } else {
                    $(this).removeClass('bg-primary border-primary text-white')
                           .addClass('border-gray-300 bg-white text-gray-700 hover:bg-gray-50');
                }
            });
        }

        function showCustomDateRange(show) {
            var el = $('#customDateRange');
            if (show) {
                el.css({ 'max-height': '200px', 'opacity': '1', 'margin-bottom': '0.75rem' });
            } else {
                el.css({ 'max-height': '0', 'opacity': '0', 'margin-bottom': '0' });
            }
        }

        function applyPreset(preset) {
            var range = getPresetRange(preset);
            $('#from_UserLoginActivityReport').val(range.from);
            $('#until_UserLoginActivityReport').val(range.until);
            $('#until_UserLoginActivityReport').attr('min', range.from);
            setActivePreset(preset);
            showCustomDateRange(false);
            table.ajax.reload();
        }

        // --- Initialize with 7 Days active ---
        setActivePreset('7days');

        // --- Preset button clicks ---
        $('.date-preset-btn').on('click', function() {
            var preset = $(this).data('preset');
            if (preset === 'custom') {
                setActivePreset('custom');
                showCustomDateRange(true);
            } else {
                applyPreset(preset);
            }
        });

        // --- Date input validation ---
        $('#from_UserLoginActivityReport').on('change', function() {
            var fromDate = $(this).val();
            $('#until_UserLoginActivityReport').attr('min', fromDate);
        });

        $('#until_UserLoginActivityReport').on('change', function() {
            var untilDate = $(this).val();
            var fromDate = $('#from_UserLoginActivityReport').val();
            if (untilDate < fromDate) {
                alert('Until Date cannot be earlier than From Date.');
                $(this).val(fromDate);
            }
        });

        // --- DataTable setup ---
        var silentSearch = false;

        var table = $('#tableUserLogin').DataTable({
            processing: true,
            serverSide: true,
            order: [[1, 'asc']],
            columnDefs: [
                { targets: [0, 3, 4, 5, 6], orderable: false }
            ],
            ajax: {
                url: "<?= base_url('ReportDriver/UserLoginActivityReport') ?>",
                type: "GET",
                data: function(d) {
                    d.from_UserLoginActivityReport = $('input[name=from_UserLoginActivityReport]').val();
                    d.until_UserLoginActivityReport = $('input[name=until_UserLoginActivityReport]').val();
                },
            },
            columns: [
                { data: "no", className: "text-center" },
                { data: "Fullname" },
                { data: "Email" },
                {
                    "data": "CancelJob",
                    "className": "text-center",
                    "render": function(data, type, row) {
                        let total = 0;
                        let jobID = '';
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(element => {
                                total += 1;
                                jobID += element.JobID + ', ';
                            });
                            jobID = jobID.replace(/, $/, '');
                        } else {
                            jobID = 0;
                        }
                        return `<button type='button' class='btn-tw-danger btn_detail_cancel_job' data-job-id='${jobID}' data-user-id='${row.UserID}'>${total}</button>`;
                    }
                },
                { "data": "TotalJob", className: "text-center" },
                {
                    "data": "CompleteJob",
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `<button type='button' class='btn-tw-primary btn_detail_complete_job' data-user-id='${row.UserID}' data-from-date='${row.FromDate}' data-until-date='${row.UntilDate}'>${data}</button>`;
                    }
                },
                {
                    "data": "OngoingJob",
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `<button type='button' class='btn-tw-primary btn_detail_ongoing_job' data-user-id='${row.UserID}' data-from-date='${row.FromDate}' data-until-date='${row.UntilDate}'>${data}</button>`;
                    }
                }
            ],
            responsive: false,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            dom: '<"d-flex justify-content-end align-items-center mb-2"f>rtip',
            searching: true,
            searchDelay: 500,
        });

        // Suppress loading overlay when the user types in the search box
        // NOTE: We listen on the actual search input (not search.dt) because
        // DataTables fires search.dt internally during sort/redraw, which would
        // incorrectly set silentSearch=true and prevent the overlay from hiding.
        $('#tableUserLogin_wrapper').on('input', '.dataTables_filter input', function() { silentSearch = true; });
        $('#tableUserLogin').on('processing.dt', function(e, settings, processing) {
            if (silentSearch) {
                e.stopPropagation();
                if (!processing) silentSearch = false;
            }
        });

        // Export Excel
        $('#btn_export_driver_report').on('click', function() {
            Swal.fire({
                title: 'Generating Excel...',
                text: 'Please wait a moment',
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false
            });

            $.ajax({
                url: '<?= base_url("ReportDriver/exportDriverExcel") ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    from_date: $('input[name="from_UserLoginActivityReport"]').val(),
                    until_date: $('input[name="until_UserLoginActivityReport"]').val()
                },
                success: function(res) {
                    Swal.close();
                    if (res.status === true && res.file_url) {
                        window.location.href = res.file_url;
                    } else {
                        Swal.fire('Failed', res.message || 'Could not generate file', 'error');
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error', 'Server error while generating file', 'error');
                }
            });
        });

        // Submit Filter (custom date range)
        $('#formFilterUserLoginActivityReport').on('submit', function(e) {
            e.preventDefault();
            setActivePreset('custom');
            table.ajax.reload();
        });

        // Reset Filter — go back to 7 Days default
        $('#resetFilterUserLoginActivityReport').on('click', function() {
            applyPreset('7days');
        });
    });

    $(document).on("click", ".btn_detail_complete_job", function() {

        const userID = $(this).data('user-id'); // ambil ID job dari tombol
        const fromDate = $(this).data('from-date');
        const untilDate = $(this).data('until-date');
        // Tampilkan modal dulu
        showModal('#modal_detail_job');
        $('#modal_detail_job #modal_detail_job_header').text('Detail Complete Job');



        // Load konten dari controller (kirim jobId ke backend)
        $("#modal_detail_job .p-4.overflow-y-auto").load("<?= base_url('ReportDriver/detail_job/') ?>" + userID + "/2/" + fromDate + "/" + untilDate, function() {
            var table = $("#modal_detail_job [data-paginated-table]")[0];
            if (table && window.initPaginatedTable) window.initPaginatedTable(table);
        });
    });

    $(document).on("click", ".btn_detail_ongoing_job", function() {

        const userID = $(this).data('user-id');
        const fromDate = $(this).data('from-date');
        const untilDate = $(this).data('until-date');
        // Tampilkan modal dulu
        showModal('#modal_detail_job');
        $('#modal_detail_job #modal_detail_job_header').text('Detail Ongoing Job');



        // Load konten dari controller (kirim jobId ke backend)
        $("#modal_detail_job .p-4.overflow-y-auto").load("<?= base_url('ReportDriver/detail_job/') ?>" + userID + "/1/" + fromDate + "/" + untilDate, function() {
            var table = $("#modal_detail_job [data-paginated-table]")[0];
            if (table && window.initPaginatedTable) window.initPaginatedTable(table);
        });
    });

    $(document).on("click", ".btn_detail_cancel_job", function() {

        const jobID = $(this).data('job-id');
        const userID = $(this).data('user-id');
        // Tampilkan modal dulu
        showModal('#modal_detail_job');
        $('#modal_detail_job #modal_detail_job_header').text('Detail Cancel Job');

        // Load konten dari controller (kirim jobId ke backend)
        $("#modal_detail_job .p-4.overflow-y-auto").load("<?= base_url('ReportDriver/detail_job_cancel?job_id=') ?>"  + encodeURIComponent(jobID) + "&user_id=" + encodeURIComponent(userID), function() {
            var table = $("#modal_detail_job [data-paginated-table]")[0];
            if (table && window.initPaginatedTable) window.initPaginatedTable(table);
        });
    });

</script>