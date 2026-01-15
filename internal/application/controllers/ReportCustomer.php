<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h4><?= $title ?></h4>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Customer Retention Report -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h5 class="card-title">📌 Customer Retention Report</h5>
                        </div>
                        <div class="card-body">
                            <form id="filterFormCustomerRetentionReport" method="get" action="" class="mb-3">
                                <div class="row">
                                    <!-- Customer Name -->
                                    <div class="col-md-3 my-3">
                                        <label for="customerSelectCustomerRetentionReport">Customer</label>
                                        <select id="customerSelectCustomerRetentionReport" class="form-control">
                                            <option value="">-- All Customers --</option>
                                        </select>
                                    </div>

                                    <!-- Total job -->
                                    <div class="col-md-3 my-3">
                                        <label for="totalJobInputCustomerRetentionReport">Total Job (Min)</label>
                                        <input type="number" id="totalJobInputCustomerRetentionReport" class="form-control" placeholder="e.g. 60" min="0" >
                                    </div>

                                    <!-- First Job -->
                                    <div class="col-md-3 my-3">
                                        <label>First Job</label>
                                        <input type="date" name="fromCustomerRetentionReport" id="fromCustomerRetentionReport" value="<?= $this->input->get('fromCustomerRetentionReport') ?>" class="form-control">
                                    </div>

                                    <!-- Last Job -->
                                    <div class="col-md-3 my-3">
                                        <label>Last Job</label>
                                        <input type="date" name="untilCustomerRetentionReport" id="untilCustomerRetentionReport" value="<?= $this->input->get('untilCustomerRetentionReport') ?>" class="form-control">
                                    </div>

                                    <div class="col-md-3 my-3">
                                        <label for="retentionDaysInputCustomerRetentionReport">Retention Days (Min)</label>
                                        <input type="number" id="retentionDaysInputCustomerRetentionReport" class="form-control" placeholder="e.g. 60" min="0">
                                    </div>

                                    <!-- Status Customer -->
                                    <div class="col-md-3 my-3">
                                        <label>Status Customer</label>
                                        <select name="statusCustomerRetentionReport" id="statusCustomerRetentionReport" class="form-control">
                                            <option value="">-- All Status Customers --</option>
                                            <option value="Active" <?= $this->input->get('statusCustomerRetentionReport')=='Active'?'selected':'' ?>>Active</option>
                                            <option value="Inactive" <?= $this->input->get('statusCustomerRetentionReport')=='Inactive'?'selected':'' ?>>Inactive</option>
                                        </select>
                                    </div>

                                    <!-- Button -->
                                    <div class="col-md-3 my-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-info mr-2">🔍 Filter</button>
                                        <button type="button" id="resetFilterCustomerRetentionReport" class="btn btn-secondary">🔄 Reset</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered dt-responsive display responsive nowrap" cellspacing="0" width="100%" id="tableCustomerRetentionReport">
                                    <thead>
                                        <tr>
                                            <th style="width:5%; text-align:center;">No</th>
                                            <th>Customer</th>
                                            <th style="white-space: nowrap !important; ">Total Job</th>
                                            <th>First Job</th>
                                            <th>Last Job</th>
                                            <th>Retention Days</th>
                                            <th>Status Customer</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Detail -->
            <div class="modal fade" id="customerDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Customer Detail</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Job Number</th>
                                    <th>Job Date</th>
                                    <th>Job Type</th>
                                    <th>Job Status</th>
                                    <th>Pickup Location</th>
                                    <th>Destination</th>
                                    <th>Handled By</th>
                                    <th>Cancel Reason</th>
                                </tr>
                            </thead>
                            <tbody id="customerDetailBody"></tbody>
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
    </section>
</div>

<style>
    .content-wrapper {
        min-height: max-content !important;
    }
    
    #tableCustomerRetentionReport th {
        text-align: center;
    }

    #tableCustomerRetentionReport td {
        text-align: left; 
    }

    #tableCustomerRetentionReport td:nth-child(1),
    #tableCustomerRetentionReport td:nth-child(3),
    #tableCustomerRetentionReport td:nth-child(6) {
        text-align: center !important;
    }

    #tableCustomerRetentionReport tr.child td {
        text-align: left !important;
        background-color: #ffffffff;
    }

    #tableCustomerRetentionReport tr.child td {
        white-space: nowrap;
    }

    table.dataTable>tbody>tr.child span.dtr-title {
        display: inline-block;
        min-width: 275px;
        font-weight: bold;
    }

    /* .dt-buttons {
        display: flex;
        gap: 8px;
    } */
    div div.dt-buttons {
        float: left !important;
        display: flex !important;
        justify-content: space-between !important;
        flex-direction: row;
        flex-wrap: nowrap;
        gap: 10px !important;
        align-items: flex-end;
    }

    .btn-group>.btn-group:not(:last-child)>.btn, .btn-group>.btn:not(:last-child):not(.dropdown-toggle) {
        border-radius: 0.5rem;
    }
    .btn-group>.btn-group:not(:first-child)>.btn, .btn-group>.btn:not(:first-child) {
        border-radius: 0.5rem;
    }

    /* Mobile & Tablet (< 992px) */
    @media (max-width: 991.98px) {
        #tableCustomerRetentionReport th {
            text-align: center;
        }
        
        #tableCustomerRetentionReport td:nth-child(1),
        #tableCustomerRetentionReport td:nth-child(3),
        #tableCustomerRetentionReport td:nth-child(6) {
            text-align: center;
        }

        #tableCustomerRetentionReport td:not(:nth-child(1)):not(:nth-child(3)):not(:nth-child(6)) {
            text-align: left !important;
        }

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
            url: '<?= base_url('ReportCustomer/getCustomers') ?>', 
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
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= site_url('ReportCustomer/CustomerRetentionReport') ?>",
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
                // { 
                //     data: null, 
                //     render: function (data, type, row, meta) {
                //         return meta.row + meta.settings._iDisplayStart + 1; 
                //     },
                //     className: "text-center"
                // },
                { data: "No", className: "text-center"},
                { data: "CustomerName" },
                { data: "TotalJob" },
                { data: "FirstJob" },
                { data: "LastJob" },
                { data: "RetentionDays" },
                { data: "StatusCustomer" },
                { data: "Action" }
            ],
            responsive: false,
            pageLength: 5,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '📘 Exce',
                    title: `Customer Retention Report (${(today.getMonth()+1).toString().padStart(2, '0')}/${today.getDate().toString().padStart(2, '0')}/${today.getFullYear()})`,
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
                // lengthMenu: "Tampilkan _MENU_ data",
                // info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                // infoEmpty: "Tidak ada data tersedia",
                // zeroRecords: "Tidak ditemukan data yang sesuai",
                // paginate: {
                //     first: "Pertama",
                //     last: "Terakhir",
                //     next: "→",
                //     previous: "←"
                // },
            },
            order: [[4, "desc"]]
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
            showCustomerDetail(customerID);
        });

    });

    function showCustomerDetail(customerID) {
        $.ajax({
            url: "<?= base_url('ReportCustomer/getCustomerDetail') ?>",
            type: "GET",
            data: { CustomerID: customerID },
            dataType: "json",
            success: function(res) {
                var tbody = $('#customerDetailBody');
                tbody.empty();

                if (res.Jobs && res.Jobs.length > 0) {
                    res.Jobs.forEach(function(job) {
                        var row = '<tr>' +
                            '<td>' + job.JobDate + '</td>' +
                            '<td>' + (job.HandledBy || '-') + '</td>' +
                            '<td>' + (job.CancelReason || '-') + '</td>' +
                            '</tr>';
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="3" class="text-center">No jobs found</td></tr>');
                }

                $('#customerDetailModal').modal('show');
            },
            error: function(err) {
                console.error(err);
                alert('Failed to load customer detail.');
            }
        });
    }


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

        fromInput.addEventListener("change", updateUntilMin); // Update on change
    });
</script>
