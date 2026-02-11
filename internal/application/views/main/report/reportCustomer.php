<div class="px-4 sm:px-6 lg:px-8 py-4">
    <h4 class="text-xl font-bold text-gray-800"><?= $title ?></h4>
</div>

<div class="px-4 sm:px-6 lg:px-8 pb-6">

    <!-- Customer Retention Report -->
    <div class="bg-white rounded-xl shadow-sm border border-cyan-200">
        <div class="px-5 py-4 border-b border-cyan-200">
            <h5 class="text-base font-semibold text-gray-800">📌 Customer Retention Report</h5>
        </div>
        <div class="px-5 py-4">
            <form id="filterFormCustomerRetentionReport" method="get" action="" class="mb-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Customer Name -->
                    <div class="my-3">
                        <label for="customerSelectCustomerRetentionReport" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <select id="customerSelectCustomerRetentionReport" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors">
                            <option value="">-- All Customers --</option>
                        </select>
                    </div>

                    <!-- Total job -->
                    <div class="my-3">
                        <label for="totalJobInputCustomerRetentionReport" class="block text-sm font-medium text-gray-700 mb-1">Total Job (Min)</label>
                        <input type="number" id="totalJobInputCustomerRetentionReport" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" placeholder="e.g. 60" min="0" >
                    </div>

                    <!-- First Job -->
                    <div class="my-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Job</label>
                        <input type="date" name="fromCustomerRetentionReport" id="fromCustomerRetentionReport" value="<?= $this->input->get('fromCustomerRetentionReport') ?>" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors">
                    </div>

                    <!-- Last Job -->
                    <div class="my-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Job</label>
                        <input type="date" name="untilCustomerRetentionReport" id="untilCustomerRetentionReport" value="<?= $this->input->get('untilCustomerRetentionReport') ?>" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors">
                    </div>

                    <div class="my-3">
                        <label for="retentionDaysInputCustomerRetentionReport" class="block text-sm font-medium text-gray-700 mb-1">Retention Days (Min)</label>
                        <input type="number" id="retentionDaysInputCustomerRetentionReport" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" placeholder="e.g. 60" min="0">
                    </div>

                    <!-- Status Customer -->
                    <div class="my-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Customer</label>
                        <select name="statusCustomerRetentionReport" id="statusCustomerRetentionReport" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors">
                            <option value="">-- All Status Customers --</option>
                            <option value="Active" <?= $this->input->get('statusCustomerRetentionReport')=='Active'?'selected':'' ?>>Active</option>
                            <option value="Inactive" <?= $this->input->get('statusCustomerRetentionReport')=='Inactive'?'selected':'' ?>>Inactive</option>
                        </select>
                    </div>

                    <!-- Button -->
                    <div class="my-3 flex items-end">
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors mr-2"><svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg> Filter</button>
                        <button type="button" id="resetFilterCustomerRetentionReport" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Reset</button>
                    </div>
                </div>
            </form>

            <table class="w-full text-sm" width="100%" id="tableCustomerRetentionReport">
                <thead class="bg-gray-50">
                    <tr class="text-center whitespace-nowrap">
                        <th class="w-[5%] px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Customer</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Company</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Total Job</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">First Job</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Last Job</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Retention Days</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Status Customer</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="customerDetailModal" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="customerNameHeader">
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-4xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
            <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">
                <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                    <h3 id="customerNameHeader" class="font-semibold text-gray-800">Customer Detail</h3>
                    <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#customerDetailModal">
                        <span class="sr-only">Close</span>
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-4 overflow-y-auto">
                    <table id="customerDetailTable" class="w-full text-sm dt-responsive" width="100%">
                    <thead class="bg-gray-50">
                        <tr class="text-center whitespace-nowrap">
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Job Name</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Job Date</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Type Job</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Notes</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Handled By</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Company Name</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Cancel Reason</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <!-- Customer Engagement Report -->
    <!-- <div class="row">
        <div class="col-md-12">
            <div class="card card-info card-outline">
            <div class="card-header">
                <h5 class="card-title">📌 Customer Engagement Report</h5>
            </div>
            <div class="card-body">
                <form id="filterFormCustomerEngagementReport" method="GET" action="" class="mb-3">
                    <div class="row"> -->
                        <!-- Customer Name -->
                        <!-- <div class="col-md-3">
                            <label for="customerSelect">Customer</label>
                            <select id="customerSelect" class="form-control">
                                <option value="">-- All Customers --</option>
                            </select>
                        </div> -->

                        <!-- Total job -->
                        <!-- <div class="col-md-3">
                            <label for="totalJobInput">Total Job (Min)</label>
                            <input type="number" id="totalJobInput" class="form-control" placeholder="e.g. 60" min="0" >
                        </div> -->

                        <!-- Button -->
                        <!-- <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-info mr-2">🔍 Filter</button>
                            <button type="button" id="resetFilterCustomerEngagementReport" class="btn btn-secondary">🔄 Reset</button>
                        </div>
                    </div>
                </form>
                <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%" id="tableCustomerEngagement">
                    <thead>
                        <tr>
                        <th>No</th>
                        <th>Customer</th>
                        <th>Total Job</th>
                        </tr>
                    </thead>
                </table>
            </div>
            </div>
        </div>
    </div> -->

</div>

<style>
    /* Child row styles for DataTables responsive mode */
    #tableCustomerRetentionReport tr.child td {
        text-align: left !important;
        background-color: #ffffffff;
        white-space: nowrap;
    }

    table.dataTable>tbody>tr.child span.dtr-title {
        display: inline-block;
        min-width: 275px;
        font-weight: bold;
    }

    /* Mobile & Tablet (< 992px) */
    @media (max-width: 991.98px) {
        table.dataTable>tbody>tr.child span.dtr-title {
            display: inline-block;
            min-width: 240px;
            font-weight: bold;
        }

        table.dataTable>tbody>tr.child span.dtr-data {
            text-align: center !important;
        }
    }
</style>


<script>


    // Customer Retention Report
    // Customer Retention Report
    // Customer Retention Report
    $(document).ready(function() {
        let today = new Date();
        let now = `${(today.getMonth()+1).toString().padStart(2, '0')}_${today.getDate().toString().padStart(2, '0')}_${today.getFullYear()}`;
        let fileName = `Customer_Retention_Report_${now}`;

        $.ajax({
            url: '<?= base_url('ReportCustomer/getCustomersSession') ?>',
            type: 'GET',
            success: function(data) {
                var customers = JSON.parse(data);
                var customerSelect = $('#customerSelectCustomerRetentionReport');
                customers.forEach(function(customer) {
                    customerSelect.append(new Option(customer.CustomerName, customer.CustomerID));
                });
            }
        });

        var table = $('#tableCustomerRetentionReport').DataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= base_url('ReportCustomer/CustomerRetentionReport') ?>",
                type: "GET",
                data: function(d) {
                    d.customerIDCustomerRetentionReport     = $('#customerSelectCustomerRetentionReport').val();
                    d.totalJobCustomerRetentionReport       = $('#totalJobInputCustomerRetentionReport').val();
                    d.fromCustomerRetentionReport           = $('input[name="fromCustomerRetentionReport"]').val();
                    d.untilCustomerRetentionReport          = $('input[name="untilCustomerRetentionReport"]').val();
                    d.retentionDaysCustomerRetentionReport  = $('#retentionDaysInputCustomerRetentionReport').val();
                    d.statusCustomerRetentionReport         = $('select[name="statusCustomerRetentionReport"]').val();
                }
            },
            columns: [
                { data: "No", className: "text-center whitespace-nowrap align-middle" },
                { data: "CustomerName", className: "text-center whitespace-nowrap align-middle" },
                { data: "CompanyName", className: "text-center whitespace-nowrap align-middle" },
                { data: "TotalJob", className: "text-center whitespace-nowrap align-middle" },
                {
                    data: "FirstJob",
                    className: "text-center whitespace-nowrap align-middle",
                    render: function(data, type, row) {
                        if (!data || data === '-') return '-';
                        const date = new Date(data);
                        const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
                        return date.toLocaleDateString('en-US', options);
                    }
                },
                {
                    data: "LastJob",
                    className: "text-center whitespace-nowrap align-middle",
                    render: function(data, type, row) {
                        if (!data || data === '-') return '-';
                        const date = new Date(data);
                        const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
                        return date.toLocaleDateString('en-US', options);
                    }
                },
                { data: "RetentionDays", className: "text-center whitespace-nowrap align-middle" },
                { data: "StatusCustomer", className: "text-center whitespace-nowrap align-middle" },
                { data: "Action", className: "text-center whitespace-nowrap align-middle" }
            ],
            responsive: false,
            pageLength: 10,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    title: `Customer Retention Report (${(today.getMonth()+1).toString().padStart(2, '0')}/${today.getDate().toString().padStart(2, '0')}/${today.getFullYear()})`,
                    filename: fileName,
                    className: 'btn btn-success me-2',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    action: function (e, dt, button, config) {
                        let params = {
                            customerIDCustomerRetentionReport: $('#customerSelectCustomerRetentionReport').val(),
                            totalJobCustomerRetentionReport: $('#totalJobInputCustomerRetentionReport').val(),
                            fromCustomerRetentionReport: $('input[name="fromCustomerRetentionReport"]').val(),
                            untilCustomerRetentionReport: $('input[name="untilCustomerRetentionReport"]').val(),
                            retentionDaysCustomerRetentionReport: $('#retentionDaysInputCustomerRetentionReport').val(),
                            statusCustomerRetentionReport: $('select[name="statusCustomerRetentionReport"]').val()
                        };

                        // Ubah object jadi query string
                        let query = $.param(params);

                        // Redirect ke controller export dengan filter yang sama
                        window.location.href = "<?= base_url('ReportCustomer/exportCustomerRetentionExcel?') ?>" + query;
                    }
                },
            ],
            ordering: true,
            searching: true,
            language: {
                search: "Search:",
            },
            order: [[4, "desc"]],
        });

        // Reload Otomatic
        $('#customerSelectCustomerRetentionReport, #totalJobInputCustomerRetentionReport, #fromCustomerRetentionReport, #untilCustomerRetentionReport, #retentionDaysInputCustomerRetentionReport, #statusCustomerRetentionReport').on('change', function() {
            $('#tableCustomerRetentionReport').DataTable().ajax.reload();
        });

        // Submit Filter
        $('#filterFormCustomerRetentionReport').on('submit', function(e){
            e.preventDefault();
            table.ajax.reload();
        });

        // Reset Filter
        $('#resetFilterCustomerRetentionReport').on('click', function(){
            $('#filterFormCustomerRetentionReport')[0].reset();
            $('#customerSelectCustomerRetentionReport').val('').trigger('change');
            $('#statusCustomerRetentionReport').val('').trigger('change');
            $('#tableCustomerRetentionReport').DataTable().ajax.reload();

            const untilInput = document.querySelector('input[name="untilCustomerRetentionReport"]');
            untilInput.removeAttribute("min");
        });

        $('#tableCustomerRetentionReport').on('click', '.btn-detail', function() {
            let customerID = $(this).data('id');
            $.ajax({
                url: "<?= base_url('ReportCustomer/getCustomerDetail') ?>",
                type: "GET",
                data: { CustomerID: customerID },
                dataType: "json",
                success: function(res){
                    $('#customerNameHeader').text(res.CustomerName || '-');

                    // Jika sudah pernah di-init DataTable, destroy dulu
                    if ($.fn.DataTable.isDataTable('#customerDetailTable')) {
                        $('#customerDetailTable').DataTable().clear().destroy();
                    }

                    // Inisialisasi DataTable baru
                    detailTable = $('#customerDetailTable').DataTable({
                        data: res.Jobs || [],
                        columns: [
                            {
                                data: null,
                                render: (data, type, row, meta) => meta.row + 1,
                                className: "text-center"
                            },
                            { data: "JobName" },
                            { data: "JobDate" },
                            { data: "TypeJob" },
                            { data: "JobStatus" },
                            { data: "Notes" },
                            { data: "HandledBy" },
                            { data: "CompanyName" },
                            { data: "CancelReason" }
                        ],
                        responsive: true,
                        searching: true,
                        paging: true,
                        lengthMenu: [5, 10, 25, 50],
                        pageLength: 5,
                        order: [[2, 'desc']] // urut berdasarkan JobDate
                    });

                    showModal('#customerDetailModal');
                },
                //     var tbody = $('#customerDetailBody');
                //     tbody.empty();
                //     if(res.Jobs && res.Jobs.length > 0){
                //         res.Jobs.forEach((job, i) => {
                //             tbody.append('<tr>' +
                //                 `<td>${i + 1}</td>` +
                //                 `<td>${job.JobName || '-'}</td>` +
                //                 `<td>${job.JobDate || '-'}</td>` +
                //                 `<td>${job.TypeJob || '-'}</td>` +
                //                 `<td>${job.JobStatus || '-'}</td>` +
                //                 `<td>${job.Notes || '-'}</td>` +
                //                 `<td>${job.HandledBy || '-'}</td>` +
                //                 `<td>${job.CancelReason || '-'}</td>` +
                //             '</tr>');
                //         });
                //     } else {
                //         tbody.append('<tr><td colspan="8" class="text-center">No jobs found</td></tr>');
                //     }
                //     $('#customerDetailModal').modal('show');
                // },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

    });


    // Customer Engagement
    // Customer Engagement
    // Customer Engagement
    $(document).ready(function() {
        let today = new Date();
        let now = `${(today.getMonth()+1).toString().padStart(2, '0')}_${today.getDate().toString().padStart(2, '0')}_${today.getFullYear()}`;
        let fileName = `Customer_Engagement_${now}`;

        $.ajax({
            url: '<?= base_url('ReportCustomer/getCustomers') ?>',
            type: 'GET',
            success: function(data) {
                var customers = JSON.parse(data);
                var customerSelect = $('#customerSelect');
                customers.forEach(function(customer) {
                    customerSelect.append(new Option(customer.CustomerName, customer.CustomerID));
                });
            }
        });

        $('#tableCustomerEngagement').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= base_url('ReportCustomer/CustomerEngagementReport') ?>",
                type: "GET",
                data: function(d) {
                    d.customerID = $('#customerSelect').val();
                    d.totalJob = $('#totalJobInput').val();
                }
            },
            columns: [
                { data: "no", className: "text-center" },
                { data: "CustomerName" },
                { data: "TotalJob" }
            ],
            responsive: true,
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50, 100],
            dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
            buttons: [
                {
                    extend: 'pageLength',
                    className: 'btn btn-secondary me-2'
                },
                {
                    extend: 'excelHtml5',
                    text: '📘 Excel',
                    title: `Customer Engagement (${(today.getMonth()+1).toString().padStart(2, '0')}/${today.getDate().toString().padStart(2, '0')}/${today.getFullYear()})`,
                    filename: fileName,
                    className: 'btn btn-success me-2',
                    customize: function (xlsx) {
                        try {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];

                            // Tambahkan padding via spasi agar tidak dempet
                            $('row c t', sheet).each(function () {
                                var text = $(this).text();
                                $(this).text('  ' + text + '  ');
                            });

                            // Lebarkan kolom (default 25 karakter)
                            $('col', sheet).attr('width', 25);
                        } catch (e) {
                            console.warn('⚠️ Failed to modify Excel XML:', e.message);
                        }
                    },
                },
            ],
            ordering: true,
            searching: true,
            language: {
                search: "🔍 Search:",
            },
            order: [[2, "desc"]]
        });

        // Reload Otomatic
        $('#customerSelect, #totalJobInput').on('change', function() {
            $('#tableCustomerEngagement').DataTable().ajax.reload();
        });

        // Submit Filter
        $('#filterFormCustomerEngagementReport').on('submit', function(e){
            e.preventDefault();
            table.ajax.reload();
        });

        // Reset Filter
        $('#resetFilterCustomerEngagementReport').on('click', function(){
            $('#filterFormCustomerEngagementReport')[0].reset();
            $('#tableCustomerEngagement').DataTable().ajax.reload();
        });
    });


    // From .... Until .... User Performance Report
    document.addEventListener("DOMContentLoaded", function () {
    const fromInput = document.querySelector('input[name="fromCustomerRetentionReport"]');
    const untilInput = document.querySelector('input[name="untilCustomerRetentionReport"]');

    // STOP if inputs don't exist on this page
    if (!fromInput || !untilInput) return;

    function addOneDay(dateStr) {
        const date = new Date(dateStr);
        date.setDate(date.getDate() + 1);
        return date.toISOString().split('T')[0];
    }

    function updateUntilMin() {
        if (fromInput.value) {
            const minUntil = addOneDay(fromInput.value);
            untilInput.setAttribute("min", minUntil);

            if (untilInput.value && untilInput.value < minUntil) {
                untilInput.value = minUntil;
            }
        } else {
            untilInput.removeAttribute("min");
        }
    }

    updateUntilMin();
    fromInput.addEventListener("change", updateUntilMin);
});

</script>
