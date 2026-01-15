<style>

.btn_custom {
    padding: 3px 5px !important;
    font-size: 12px !important;
}  
.ongoing_job {
    background-color: #fff3cd;       /* kuning lembut */
    color: #856404;                  /* teks coklat gelap */
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    display: inline-block;
}

.finished_job {
    background-color: #d4edda;       /* hijau lembut */
    color: #155724;                  /* teks hijau tua */
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    display: inline-block;
}

.awaiting_job {
    background-color: #f8d7da;       /* merah muda lembut */
    color: #721c24;                  /* teks merah gelap */
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    display: inline-block;
}

#tableJobCustomer th,
#tableJobCustomer td,
#tableJobCompliance th,
#tableJobCompliance td {
    text-align: center;
}

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
    #tableJobCustomer th,
    #tableJobCustomer td,
    #tableJobCompliance th,
    #tableJobCompliance td {
        text-align: left !important;
    }
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h4>Efms | Report Job</h4>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Job Report per Customer -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h5 class="card-title">📌 Job Report</h5>
                        </div>
                        <div class="card-body">
                            <form id="formFilterJobReportperCustomer" method="GET" action="">
                                <div class="row mb-3">
                                    <!-- Customers -->
                                    <div class="col-md-3 my-1">
                                        <label for="filterFromDateJobReportperCustomer" class="mr-2">From Date:</label>
                                         <input class="form-control" type="date" id="filterFromDateJobReportperCustomer" name="from_date" value="<?= (isset($_GET['from_date'])) ?  $_GET['from_date'] : date('Y-m-d') ?>">
                                    </div>

                                    <div class="col-md-3 my-1">
                                        <label for="filterUntilDateJobReportperCustomer" class="mr-2">Until Date:</label>
                                         <input class="form-control" type="date" id="filterUntilDateJobReportperCustomer" name="until_date" value="<?= (isset($_GET['until_date'])) ?  $_GET['until_date'] : date('Y-m-d') ?>">
                                    </div>

                                    <!-- Jobs -->
                                    <div class="col-md-3 my-1">
                                        <label for="filterStatusJobReportperCustomer" class="mr-2">Status Job:</label>
                                        <select id="filterStatusJobReportperCustomer" class="form-control">
                                            <option value="all_status">-- All Status --</option>
                                            <option value="awaiting_job">Awaiting Driver</option>
                                            <option value="ongoing_job">Ongoing Job</option>
                                            <option value="finished">Finished Job</option>
                                        </select>
                                    </div>
    
                                    <!-- Button -->
                                    <div class="col-md-3 align-self-end my-1">
                                        <button type="submit" class="btn btn-info"> Filter</button>
                                        <button type="button" class="btn btn-secondary" id="resetFilterJobReportperCustomer">Reset</button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered dt-responsive display responsive nowrap" cellspacing="0" width="100%" id="tableJobCustomer">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Job Date</th>
                                            <th>Job Name</th>
                                            <th>Customer</th>
                                            <th>Driver</th>
                                            <th>Status Job</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Compliance Report -->
            <!-- <div class="row">
                <div class="col-md-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h5 class="card-title">📌 Job Compliance Report</h5>
                        </div>
                        <div class="card-body">
                            <form id="formFilterJobCompliance" method="GET" action="">
                                <div class="row mb-3">
                                    <div class="col-md-3 my-1">
                                        <label>Job Name</label>
                                        <select name="jobName" id="jobNameFilter" class="form-control">
                                            <option value="">-- All Job --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 my-1">
                                        <label>Type Job</label>
                                        <select name="typeJob" id="typeJobFilter" class="form-control">
                                            <option value="">-- All Type Job --</option>
                                            <option value="Line Interrupt">Line Interrupt</option>
                                            <option value="Reconnection">Reconnection</option>
                                            <option value="Short Circuit">Short Circuit</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 my-1">
                                        <label>Job Date</label>
                                        <input type="date" name="jobDate" id="jobDateFilter" class="form-control">
                                    </div>
                                    <div class="col-md-3 my-1">
                                        <label>Total Documentation</label>
                                        <input type="number" name="totalDokumentasi" id="totalDokumentasiFilter" class="form-control" placeholder="e.g. 60" min="0" >
                                    </div>

                                    <div class="col-md-4 my-1">
                                        <label>Status Documentation</label>
                                        <select name="statusJobComplianceReport" id="statusFilterJobComplianceReport"  class="form-control">
                                            <option value="">-- All Status Documentation --</option>
                                            <option value="Finished" <?= $this->input->get('statusJobComplianceReport') == 'Finished' ? 'selected' : '' ?>>Finished</option>
                                            <option value="No documentation yet" <?= $this->input->get('statusJobComplianceReport') == 'No documentation yet' ? 'selected' : '' ?>>No documentation yet</option>
                                        </select>
                                    </div>
    
                                    <div class="col-md-3 align-self-end my-1">
                                        <button type="submit" class="btn btn-info">🔍 Filter</button>
                                        <button type="button" class="btn btn-secondary" id="resetFilterJobCompliance">🔄 Reset</button>
                                    </div>
                                </div>
                            </form>
                            <table class="table table-striped table-bordered dt-responsive display responsive nowrap" cellspacing="0" width="100%" id="tableJobCompliance">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Job Name</th>
                                        <th>Type Job</th>
                                        <th>Job Date</th>
                                        <th>Total Documentation</th>
                                        <th>Status Documentation</th>
                                        <th>Photo</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div> -->


            <!-- Modal Preview Photo -->
            <!-- <div class="modal fade" id="JobCompliancePhotosModal" tabindex="-1" aria-labelledby="JobCompliancePhotosModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="JobCompliancePhotosModalLabel">📸 Job Compliance Report - Photos</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="jobPhoto" src="" alt="Job Photo" class="img-fluid rounded shadow-sm">
                        <p class="mt-2 text-muted" id="photoCaption"></p>
                    </div>
                    </div>
                </div>
            </div> -->



            <!-- Job Assignment Efficiency Report -->
            <!-- <div class="row">
                <div class="col-md-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h5 class="card-title">📌 Job Assignment Efficiency Report</h5>
                        </div>
                        <div class="card-body">
                            <form id="formFilterJobAssignmentEfficiencyReport" method="GET" action="">
                                <div class="row mb-3">
                                    <div class="col-md-3 mb-3">
                                        <label for="filterJobNameJobAssignmentEfficiencyReport">Job</label>
                                        <select id="filterJobNameJobAssignmentEfficiencyReport" class="form-control">
                                            <option value="">-- All Jobs --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="filterCustomerNameJobAssignmentEfficiencyReport">Customer</label>
                                        <select id="filterCustomerNameJobAssignmentEfficiencyReport" class="form-control">
                                            <option value="">-- All Customers --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>From (Created At)</label>
                                        <input type="date" id="fromDateJobAssignmentEfficiencyReport" name="fromJobAssignmentEfficiencyReport" class="form-control">
                                    </div>

                                    <div class="col-md-3">
                                        <label>Until (Created At)</label>
                                        <input type="date" id="toDateJobAssignmentEfficiencyReport" name="untilJobAssignmentEfficiencyReport" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label>From (Assign At)</label>
                                        <input type="date" id="fromAssignAtJobAssignmentEfficiencyReport" name="fromAssignAtJobAssignmentEfficiencyReport" class="form-control">
                                    </div>

                                    <div class="col-md-3">
                                        <label>Until (Assign At)</label>
                                        <input type="date" id="toAssignAtJobAssignmentEfficiencyReport" name="toAssignAtJobAssignmentEfficiencyReport" class="form-control">
                                    </div>

                                    <div class="col-md-3 align-self-end mt-3">
                                        <button type="submit" class="btn btn-info">🔍 Filter</button>
                                        <button type="button" class="btn btn-secondary" id="resetFilterJobAssignmentEfficiencyReport">🔄 Reset</button>
                                    </div>
                                </div>
                            </form>
                            <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%" id="tableJobAssignment">
                                <thead>
                                    <tr>
                                    <th>No</th>
                                    <th>Job Name</th>
                                    <th>Customer</th>
                                    <th>Created At</th>
                                    <th>Assigned At</th>
                                    <th>Duration (Minutes)</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- <div class="row">
                <div class="col-md-12">
                    <div class="card card-info card-outline">
                    <div class="card-header">
                        <h5 class="card-title">📌 Job Completion & Status Report</h5>
                    </div>
                    <div class="card-body">
                        <form id="formFilterJobCompletionStatusReport" method="GET" action="">
                            <div class="row mb-3">
                                <div class="col-md-3 my-1">
                                    <label for="filterJobNameJobCompletionStatusReport">Job</label>
                                    <select id="filterJobNameJobCompletionStatusReport" class="form-control">
                                        <option value="">-- All Jobs --</option>
                                    </select>
                                </div>

                                <div class="col-md-3 my-1">
                                    <label for="filterCustomerNameJobCompletionStatusReport">Customer</label>
                                    <select id="filterCustomerNameJobCompletionStatusReport" class="form-control">
                                        <option value="">-- All Customers --</option>
                                    </select>
                                </div>
                                <div class="col-md-3 my-1">
                                    <label for="statusFilterJobCompletionStatusReport">Status</label>
                                    <select id="statusFilterJobCompletionStatusReport" class="form-control">
                                        <option value="">-- All Status --</option>
                                        <option value="1">Pending</option>
                                        <option value="2">In Progress</option>
                                        <option value="3">Completed</option>
                                        <option value="4">Failed</option>
                                    </select>
                                </div>
                                <div class="col-md-3 my-1">
                                    <label>From (Job Date)</label>
                                    <input type="date" name="jobDateFromJobCompletionStatusReport" id="jobDateFromFilterJobCompletionStatusReport" class="form-control">
                                </div>

                                <div class="col-md-3 my-1">
                                    <label>Until (Job Date)</label>
                                    <input type="date" name="jobDateUntilJobCompletionStatusReport" id="jobDateUntilFilterJobCompletionStatusReport" class="form-control">
                                </div>

                                <div class="col-md-3 align-self-end my-1">
                                    <button type="submit" class="btn btn-info">🔍 Filter</button>
                                    <button type="button" class="btn btn-secondary" id="resetFilterJobCompletionStatusReport">🔄 Reset</button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%" id="tableJobCompletion">
                            <thead>
                                <tr>
                                <th>No</th>
                                <th>Job Name</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Job Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                </div>
            </div> -->
            <!-- <div class="row">
                <div class="col-md-12">
                    <div class="card card-info card-outline">
                    <div class="card-header">
                        <h5 class="card-title">📌 Job Timeline Report</h5>
                    </div>
                    <div class="card-body">
                        
                        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%" id="tableJobTimeline">
                            <thead>
                                <tr>
                                <th>No</th>
                                <th>Job Date</th>
                                <th>Total Job</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                </div>
            </div> -->

            <!-- <div class="row">
                <div class="col-md-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h5 class="card-title">📌 Job Evidence Report</h5>
                        </div>
                        <div class="card-body">
                            <form id="formFilterJobEvidenceReport" method="GET" action="">
                                <div class="row mb-3">
                                    <div class="col-md-3 my-1">
                                        <label for="filterJobName">Job</label>
                                        <select id="filterJobName" class="form-control">
                                            <option value="">-- All Jobs --</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 my-1">
                                        <label for="filterCustomerName">Customer</label>
                                        <select id="filterCustomerName" class="form-control">
                                            <option value="">-- All Customers --</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 my-1">
                                        <label for="filterTotalPhoto">Total Photo (Min)</label>
                                        <input type="number" id="filterTotalPhoto" class="form-control" placeholder="e.g. 60" min="0" >
                                    </div>

                                    <div class="col-md-3 my-1">
                                        <label for="filterFromDate">From (Last Photo Date)</label>
                                        <input type="date" name="filterFromDate" id="filterFromDate" class="form-control">
                                    </div>

                                    <div class="col-md-3 my-1">
                                        <label for="filterUntilDate">Until (Last Photo Date)</label>
                                        <input type="date" name="filterUntilDate" id="filterUntilDate" class="form-control">
                                    </div>
                                    <div class="col-md-3 align-self-end my-1">
                                        <button type="submit" class="btn btn-info">🔍 Filter</button>
                                        <button type="button" class="btn btn-secondary" id="resetFilterJobEvidenceReport">🔄 Reset</button>
                                    </div>
                                </div>
                            </form>
                            <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%" id="tableJobEvidenceReport">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Job Name</th>
                                        <th>Customer Name</th>
                                        <th>Total Photo</th>
                                        <th>Last Photo Date</th>
                                        <th>Photo</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Modal Job Evidence Report -->
            <!-- <div class="modal fade" id="JobEvidencePhotosModal" tabindex="-1" aria-labelledby="JobEvidencePhotosModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="JobEvidencePhotosModalLabel"> 📸 Job Evidence Report - Photos</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                    </div>
                    </div>
                </div>
            </div> -->


        </div>
    </section>
</div>

<div class="modal fade" id="modal_detail_job" tabindex="-1" aria-labelledby="modal_detail_job" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-info text-white">
            <h5 class="modal-title" id="modal_detail_job_header">Detail Job</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
        </div>
        </div>
    </div>
</div>


<script>
    // Job Report per Customer
    $(document).ready(function() {

        let form_date_until = $('#filterFromDateJobReportperCustomer').val();
        $('#filterUntilDateJobReportperCustomer').attr('min', form_date_until);

        // bayunovandrie
        $('#filterFromDateJobReportperCustomer').on('change', function() {
            let fromDate = $(this).val();
            $('#filterUntilDateJobReportperCustomer').attr('min', fromDate); // batas bawah until date
        });

        $('#filterUntilDateJobReportperCustomer').on('change', function() {
            let untilDate = $(this).val();
            let fromDate = $('#filterFromDateJobReportperCustomer').val();

            if (untilDate < fromDate) {
                alert('Until Date cannot be earlier than From Date.');
                $(this).val(fromDate); // reset jadi sama dengan from date
            }
        });

        let today = new Date();
        let now = `${(today.getMonth()+1).toString().padStart(2, '0')}_${today.getDate().toString().padStart(2, '0')}_${today.getFullYear()}`;
        let fileName = `Job_Report_per_Customer_Report_${now}`;

        // Muat isi dropdown dari backend
        function loadAllCustomers() {
            $.ajax({
                url: "<?= base_url('ReportJob/getCustomerNames') ?>",
                type: "GET",
                dataType: "json",
                success: function (customers) {
                    const $custSelect = $('#filterCustomerJobReportperCustomer');
                    $custSelect.empty().append('<option value="">-- All Customers --</option>');
                    customers.forEach(c => {
                        $custSelect.append(new Option(c.CustomerName, c.CustomerName));
                    });
                },
                error: function (xhr) {
                    console.error("❌ Gagal memuat customer:", xhr.responseText);
                }
            });
        }

        function loadAllJobs() {
            $.ajax({
                url: "<?= base_url('ReportJob/getJobNames') ?>",
                type: "GET",
                dataType: "json",
                success: function (jobs) {
                    const $jobSelect = $('#filterJobJobReportperCustomer');
                    $jobSelect.empty().append('<option value="">-- All Jobs --</option>');
                    jobs.forEach(j => {
                        $jobSelect.append(new Option(j.JobName, j.JobName));
                    });
                },
                error: function (xhr) {
                    console.error("❌ Gagal memuat job:", xhr.responseText);
                }
            });
        }

        loadAllCustomers();
        loadAllJobs();

        // $('#filterFromDateJobReportperCustomer').on('change', function () {
        //     const customerName = $(this).val();
        //     const $jobSelect = $('#filterJobJobReportperCustomer');
        //     $jobSelect.empty().append('<option value="">-- All Jobs --</option>');

        //     if (customerName) {
        //         $.ajax({
        //             url: "<?= base_url('ReportJob/getJobsByCustomerName') ?>",
        //             type: "GET",
        //             data: { customerName },
        //             dataType: "json",
        //             success: function (jobs) {
        //                 if (jobs.length > 0) {
        //                     jobs.forEach(j => {
        //                         $jobSelect.append(new Option(j.JobName, j.JobName));
        //                     });
        //                 } else {
        //                     $jobSelect.append('<option disabled>(No Jobs Found)</option>');
        //                 }
        //             },
        //             error: function (xhr) {
        //                 console.error("❌ Gagal filter job:", xhr.responseText);
        //             }
        //         });
        //     } else {
        //         loadAllJobs(); // tampilkan semua jika customer dikosongkan
        //     }
        // });

        var table = $('#tableJobCustomer').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true ,
            columnDefs: [
                { targets: [5, 6], orderable: false } // kolom 3–6 gak bisa di-sort
            ],
            ajax: {
                url: "<?= base_url('ReportJob/JobPerCustomerReport') ?>",
                type: "GET",
                data: function(d) {
                    // Ambil data dari form
                    d.filterFromDate = $('#filterFromDateJobReportperCustomer').val();
                    d.filterUntilDate = $('#filterUntilDateJobReportperCustomer').val();
                    d.filterStatusJob = $('#filterStatusJobReportperCustomer').val();
                    // d.filterJob = $('#filterJobJobReportperCustomer').val();
                }
            },
            columns: [
                { data: "no", className: "text-center" },
                {
                    "data": "JobDate",
                    "render": function(data, type, row) {
                        if (!data || data === '-') return '-';

                        const date = new Date(data);
                        const options = { 
                            weekday: 'long',   // Nama hari
                            day: '2-digit', 
                            month: 'long',     // Nama bulan lengkap
                            year: 'numeric' 
                        };

                        const formattedDate = date.toLocaleDateString('en-US', options);

                        // Tambahkan style agar tidak wrap
                         return '<span style="white-space: nowrap;">' + formattedDate + '</span>';
                    }
                },
                { data: "JobName" },
                { data: "CustomerName" },
                {
                    "data": "DriverName",
                    "render": function(data, type, row) {
                        let driverName; // pakai let biar bisa diubah dan tetap bisa diakses di luar blok

                        if (data == null || data === '') {
                            driverName = '-';
                        } else {
                            driverName = data;
                        }

                        return driverName;
                    }
                },
                { 
                    "data": "StatusJob",
                    "render" : function(data, type, row) {

                        let statusJob;

                        if(data == 1) {
                            statusJob = "<span class='ongoing_job'>Ongoing Job</span>";
                        } else if(data == 2) {
                            statusJob = "<span class='finished_job'>Finished Job</span>";
                        } else {
                            statusJob = "<span class='awaiting_job'>Awaiting Driver</span>";
                        }

                        return statusJob;

                    }
                },
                {
                    "data": "JobID",
                    "render": function(data, type, row) {

                        // kalau status job = 2 (Finished), munculkan tombol
                        if (row.StatusJob == 2) {
                            return `
                                <button type="button" class="btn btn-sm btn-primary btn_detail_job" data-id="${data}">
                                    <i class="fa fa-eye"></i>
                                </button>
                            `;
                        } else {
                            // selain itu tampilkan JobID biasa
                            return "-";
                        }
                    }
                }

            ],
            responsive: false,
            pageLength: 10,
            rowReorder: { selector: 'td:nth-child(2)' },
            lengthMenu: [10, 25, 50, 100],
            dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
            buttons: [
                {
                    text: 'Job Report Excel',
                    className: 'btn btn-sm btn-primary btn_custom',
                    action: function (e, dt, node, config) {

                        // Bisa kirim filter juga kalau mau (misal tanggal)
                        const fromDate = $('#filterFromDateJobReportperCustomer').val();
                        const untilDate = $('#filterUntilDateJobReportperCustomer').val();

                        Swal.fire({
                            title: 'Generating Excel...',
                            text: 'Please wait a moment',
                            didOpen: () => Swal.showLoading(),
                            allowOutsideClick: false
                        });

                        $.ajax({
                            url: '<?= base_url('ReportJob/export_job_report') ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                from_date: fromDate,
                                until_date: untilDate
                            },
                            success: function (res) {
                                Swal.close();

                                if (res.status === true && res.file_url) {
                                    // langsung download
                                    window.location.href = res.file_url;
                                } else {
                                    Swal.fire('Failed', 'Could not generate file', 'error');
                                }
                            },
                            error: function () {
                                Swal.close();
                                Swal.fire('Error', 'Server error while generating file', 'error');
                            }
                        });
                    }
                }
            ],

            ordering: true,
            searching: true,
            language: { 
                search: "Search:" 
            },
            order: [[3, "desc"]]
        });

        // Reload Otomatic
        $('#filterCustomerJobReportperCustomer, #filterJobJobReportperCustomer').on('change', function() {
            table.ajax.reload();
        });

        // Submit Filter
        $('#formFilterJobReportperCustomer').on('submit', function(e) {
            e.preventDefault();
            table.ajax.reload();
        });

        // Reset Filter
        $('#resetFilterJobReportperCustomer').on('click', function() {
            $('#formFilterJobReportperCustomer')[0].reset(); 
            $('#filterCustomerJobReportperCustomer').empty().append('<option value="">-- All Customers --</option>');
            loadAllCustomers();
            loadAllJobs();
            $('#tableJobCustomer').DataTable().ajax.reload();
        });

    });




    // Job Compliance Report
    // Job Compliance Report
    // Job Compliance Report
    $(document).ready(function() {
        let today = new Date();
        let now = `${(today.getMonth()+1).toString().padStart(2, '0')}_${today.getDate().toString().padStart(2, '0')}_${today.getFullYear()}`;
        let fileName = `Job_Compliance_Report_${now}`;

        $.ajax({
            url: "<?= base_url('ReportJob/getJobNames') ?>",
            type: "GET",
            success: function(data) {
                let jobNames = JSON.parse(data);
                jobNames.forEach(function(job) {
                    $('#jobNameFilter').append(new Option(job.JobName, job.JobName));
                });
            }
        });

        var table = $('#tableJobCompliance').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= base_url('ReportJob/JobComplianceReport') ?>",
                type: "GET",
                data: function(d) {
                    d.jobName = $('select[name=jobName]').val();
                    d.typeJob = $('select[name=typeJob]').val(); 
                    d.jobDate = $('input[name=jobDate]').val();  
                    d.totalDokumentasi = $('input[name=totalDokumentasi]').val(); 
                    d.statusJobComplianceReport = $('select[name=statusJobComplianceReport]').val();
                }
            },
            columns: [
                { data: "no", className: "text-center" },
                { data: "JobName" },
                { data: "TypeJob" },
                {
                    data: "JobDate",
                    render: function(data, type, row) {
                        return data.split(' ')[0]; 
                    }
                },
                { data: "TotalDokumentasi"},
                { data: "StatusDokumentasi" },
                {
                    data: "Dokumentasi",
                    render: function(data, type, row) {
                        if (!data) return '<button class="btn btn-sm btn-secondary" disabled>No Photo</button>';
                        
                        let photos = [];
                        try {
                            photos = JSON.parse(data);
                        } catch (e) {
                            console.warn('Invalid JSON:', data);
                        }

                        const valid = photos.filter(p => p.photo && p.photo !== "null");

                        if (!valid.length)
                            return '<button class="btn btn-sm btn-secondary" disabled>No Photo</button>';

                        // simpan data foto ke tombol
                        return `
                            <button class="btn btn-sm btn-info view-photo" 
                                    data-photos='${JSON.stringify(valid)}'>
                                📷 View
                            </button>
                        `;
                    },
                    className: "text-center"
                }
            ],
            responsive: true,
            pageLength: 5,
            rowReorder: { selector: 'td:nth-child(2)' },
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
                    title: `Job Compliance Report (${(today.getMonth()+1).toString().padStart(2, '0')}/${today.getDate().toString().padStart(2, '0')}/${today.getFullYear()})`,
                    filename: fileName,
                    className: 'btn btn-success me-2',
                    exportOptions: {
                        // Cara 1: manual, ekspor kolom ke-0 s.d ke-5
                        columns: [0, 1, 2, 3, 4, 5]

                        // Cara 2 (opsional): otomatis ekspor semua kecuali kolom terakhir
                        // columns: ':visible:not(:last-child)'
                    },
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
                search: "🔍 Search:" 
            },
            order: [[3, "desc"]]
        });

        // Reload Otomatic
        $('#statusFilterJobComplianceReport, #typeJobFilter, #jobDateFilter, #totalDokumentasiFilter, #jobNameFilter').on('change', function() {
            $('#tableJobCompliance').DataTable().ajax.reload();
        });

        // Submit Filter
        $('#formFilterJobCompliance').on('submit', function(e) {
            e.preventDefault();
            table.ajax.reload();
        });

        // Reset Filter
        $('#resetFilterJobCompliance').on('click', function() {
            $('#formFilterJobCompliance')[0].reset(); 
            $('#tableJobCompliance').DataTable().ajax.reload();
        });

    });



    // Job Assignment Efficiency
    // Job Assignment Efficiency
    // Job Assignment Efficiency
    $(document).ready(function() {
        // $.ajax({
        //     url: "<?= base_url('ReportJob/getCustomers') ?>",
        //     type: "GET",
        //     dataType: "json",
        //     success: function(data) {
        //         let options = '<option value="">-- All Customer --</option>';
        //         data.forEach(function(item) {
        //             options += `<option value="${item.CustomerName}">${item.CustomerName}</option>`;
        //         });
        //         $('#customerFilterJobAssignmentEfficiencyReport').html(options);
        //     }
        // });
        let today = new Date();
        let now = `${(today.getMonth()+1).toString().padStart(2, '0')}_${today.getDate().toString().padStart(2, '0')}_${today.getFullYear()}`;
        let fileName = `Job_Assignment_Efficiency_Report_${now}`;


        $.ajax({
            url: "<?= base_url('ReportJob/getJobNames') ?>",
            type: "GET",
            success: function(data) {
                let jobNames = JSON.parse(data);
                jobNames.forEach(function(job) {
                    $('#filterJobNameJobAssignmentEfficiencyReport').append(new Option(job.JobName, job.JobName));
                });
            }
        });

        $('#filterJobNameJobAssignmentEfficiencyReport').change(function() {
            var jobName = $(this).val();
            
            // Reset customer name filter
            $('#filterCustomerNameJobAssignmentEfficiencyReport').empty();  // Kosongkan customer name
            $('#filterCustomerNameJobAssignmentEfficiencyReport').append('<option value="">-- All Customers --</option>');  // Tambahkan opsi default
            
            if (jobName) {
                // Fetch customer names yang terkait dengan Job Name
                $.ajax({
                    url: "<?= base_url('ReportJob/getCustomersByJobName') ?>", 
                    type: "GET",
                    data: { jobName: jobName },
                    success: function(data) {
                        let customers = JSON.parse(data);
                        customers.forEach(function(customer) {
                            $('#filterCustomerNameJobAssignmentEfficiencyReport').append(new Option(customer.CustomerName, customer.CustomerName));
                        });
                    }
                });
            } else {
                loadCustomerNamesJobAssignmentEfficiency();
            }
        });

        function loadCustomerNamesJobAssignmentEfficiency(){
            $.ajax({
                url: "<?= base_url('ReportJob/getCustomerNames') ?>", 
                type: "GET",
                success: function(data) {
                    let customers = JSON.parse(data);
                    // Reset customer name filter
                    $('#filterCustomerNameJobAssignmentEfficiencyReport').empty(); // Kosongkan customer name
                    $('#filterCustomerNameJobAssignmentEfficiencyReport').append('<option value="">-- All Customers --</option>'); // Tambahkan opsi default
                    customers.forEach(function(customer) {
                        $('#filterCustomerNameJobAssignmentEfficiencyReport').append(new Option(customer.CustomerName, customer.CustomerName));
                    });
                }
            });
        }

        if ($('#filterJobNameJobAssignmentEfficiencyReport').val() === '') {
            loadCustomerNamesJobAssignmentEfficiency();
        }


        $('#tableJobAssignment').DataTable({
            processing: true, 
            serverSide: true,
            ajax: { 
                url: "<?= base_url('ReportJob/JobAssignmentEfficiencyReport') ?>", 
                type: "GET",
                data: function(d) {
                    d.jobName = $('#filterJobNameJobAssignmentEfficiencyReport').val();
                    d.customerName = $('#filterCustomerNameJobAssignmentEfficiencyReport').val();
                    d.from_date = $('#fromDateJobAssignmentEfficiencyReport').val();
                    d.to_date = $('#toDateJobAssignmentEfficiencyReport').val();
                    d.fromAssignAt = $('#fromAssignAtJobAssignmentEfficiencyReport').val()
                    d.toAssignAt = $('#toAssignAtJobAssignmentEfficiencyReport').val();
                }
            },
            columns: [
                { data: "no", className: "text-center" },
                { data: "JobName" },
                { data: "CustomerName" },
                { data: "CreatedAt" },
                { data: "AssignWhen" },
                { data: "DurationMinutes" }
            ],
            responsive: true, 
            pageLength: 5, 
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
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
                    title: `Job Assignment Efficiency Report (${(today.getMonth()+1).toString().padStart(2, '0')}/${today.getDate().toString().padStart(2, '0')}/${today.getFullYear()})`,
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
            order: [[4, "desc"]]
        });

        // Reload Otomatic
        $('#filterJobNameJobAssignmentEfficiencyReport, #filterCustomerNameJobAssignmentEfficiencyReport, #fromDateJobAssignmentEfficiencyReport, #toDateJobAssignmentEfficiencyReport, #fromAssignAtJobAssignmentEfficiencyReport, #toAssignAtJobAssignmentEfficiencyReport').on('change', function() {
            $('#tableJobAssignment').DataTable().ajax.reload();
        });

        // Submit Filter
        $('#formFilterJobAssignmentEfficiencyReport').on('submit', function(e){
            e.preventDefault();
            $('#tableJobAssignment').DataTable().ajax.reload();
        });

        // Reset Filter
        $('#resetFilterJobAssignmentEfficiencyReport').on('click', function() {
            $('#formFilterJobAssignmentEfficiencyReport')[0].reset(); 
            $('#tableJobAssignment').DataTable().ajax.reload();
            const untilInput = document.querySelector('input[name="untilJobAssignmentEfficiencyReport"]');
            const untilAssignInput = document.querySelector('input[name="toAssignAtJobAssignmentEfficiencyReport"]');
            untilInput.removeAttribute("min");
            untilAssignInput.removeAttribute("min");
        });

    });



    // Job Completion & Status
    // Job Completion & Status
    // Job Completion & Status
    $(document).ready(function() {
        let today = new Date();
        let now = `${(today.getMonth()+1).toString().padStart(2, '0')}_${today.getDate().toString().padStart(2, '0')}_${today.getFullYear()}`;
        let fileName = `Job_Completion_Status_Report_${now}`;

        $.ajax({
            url: "<?= base_url('ReportJob/getJobNames') ?>",
            type: "GET",
            success: function(data) {
                let jobNames = JSON.parse(data);
                jobNames.forEach(function(job) {
                    $('#filterJobNameJobCompletionStatusReport').append(new Option(job.JobName, job.JobName));
                });
            }
        });

        $('#filterJobNameJobCompletionStatusReport').change(function() {
            var jobName = $(this).val();
            
            // Reset customer name filter
            $('#filterCustomerNameJobCompletionStatusReport').empty();  // Kosongkan customer name
            $('#filterCustomerNameJobCompletionStatusReport').append('<option value="">-- All Customers --</option>');  // Tambahkan opsi default
            
            if (jobName) {
                // Fetch customer names yang terkait dengan Job Name
                $.ajax({
                    url: "<?= base_url('ReportJob/getCustomersByJobName') ?>", 
                    type: "GET",
                    data: { jobName: jobName },
                    success: function(data) {
                        let customers = JSON.parse(data);
                        customers.forEach(function(customer) {
                            $('#filterCustomerNameJobCompletionStatusReport').append(new Option(customer.CustomerName, customer.CustomerName));
                        });
                    }
                });
            } else {
                loadCustomerNamesJobCompletionStatusReport();
            }
        });

        function loadCustomerNamesJobCompletionStatusReport(){
            $.ajax({
                url: "<?= base_url('ReportJob/getCustomerNames') ?>", 
                type: "GET",
                success: function(data) {
                    let customers = JSON.parse(data);
                    // Reset customer name filter
                    $('#filterCustomerNameJobCompletionStatusReport').empty(); // Kosongkan customer name
                    $('#filterCustomerNameJobCompletionStatusReport').append('<option value="">-- All Customers --</option>'); // Tambahkan opsi default
                    customers.forEach(function(customer) {
                        $('#filterCustomerNameJobCompletionStatusReport').append(new Option(customer.CustomerName, customer.CustomerName));
                    });
                }
            });
        }

        if ($('#filterJobNameJobCompletionStatusReport').val() === '') {
            loadCustomerNamesJobCompletionStatusReport();
        }
        
        $('#tableJobCompletion').DataTable({
            processing: true, 
            serverSide: true,
            ajax: { 
                url: "<?= base_url('ReportJob/JobCompletionStatusReport') ?>",
                type: "GET",
                data: function(d) {
                    d.jobName = $('#filterJobNameJobCompletionStatusReport').val();
                    d.customerName = $('#filterCustomerNameJobCompletionStatusReport').val();
                    d.status = $('#statusFilterJobCompletionStatusReport').val();
                    d.jobDateFrom = $('#jobDateFromFilterJobCompletionStatusReport').val();
                    d.jobDateUntil = $('#jobDateUntilFilterJobCompletionStatusReport').val();
                }
            },
            columns: [
                { data: "no", className: "text-center" },
                { data: "JobName" },
                { data: "CustomerName" },
                { data: "Status" },
                { data: "JobDate" }
            ],
            responsive: true, 
            pageLength: 5,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
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
                    title: `Job Completion Status Report (${(today.getMonth()+1).toString().padStart(2, '0')}/${today.getDate().toString().padStart(2, '0')}/${today.getFullYear()})`,
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
            order: [[4, "desc"]]
        });

        // Reload Otomatic
        $('#filterJobNameJobCompletionStatusReport, #filterCustomerNameJobCompletionStatusReport, #statusFilterJobCompletionStatusReport, #jobDateFromFilterJobCompletionStatusReport, #jobDateUntilFilterJobCompletionStatusReport').on('change', function() {
            $('#tableJobCompletion').DataTable().ajax.reload();
        })

        // Submit Filter
        $('#formFilterJobCompletionStatusReport').submit(function(e) {
            e.preventDefault();
            $('#tableJobCompletion').DataTable().ajax.reload();
        });

        // Reset Filter
        $('#resetFilterJobCompletionStatusReport').click(function() {
            $('#formFilterJobCompletionStatusReport')[0].reset();
            loadCustomerNamesJobCompletionStatusReport();
            $('#tableJobCompletion').DataTable().ajax.reload();
            const untilInput = document.querySelector('input[name="jobDateUntilJobCompletionStatusReport"]');
            untilInput.removeAttribute("min");
        });
    });


    // Job Timeline
    $(document).ready(function() {
        let today = new Date();
        let now = `${(today.getMonth()+1).toString().padStart(2, '0')}_${today.getDate().toString().padStart(2, '0')}_${today.getFullYear()}`;
        let fileName = `Job_Timeline_Report_${now}`;

        $('#tableJobTimeline').DataTable({
            processing: true, 
            serverSide: true,
            ajax: { 
                url: "<?= base_url('ReportJob/JobTimelineReport') ?>", 
                type: "GET" 
            },
            columns: [
                { data: "no", className: "text-center" },
                { data: "JobDate" },
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
                    title: `Job Timeline Report (${(today.getMonth()+1).toString().padStart(2, '0')}/${today.getDate().toString().padStart(2, '0')}/${today.getFullYear()})`,
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
            order: [[1, "desc"]]
        });
    });




    // Job Evidence Report
    $(document).ready(function() {
        let today = new Date();
        let now = `${(today.getMonth()+1).toString().padStart(2, '0')}_${today.getDate().toString().padStart(2, '0')}_${today.getFullYear()}`;
        let fileName = `Job_Evidence_Report_${now}`;

        $.ajax({
            url: "<?= base_url('ReportJob/getJobNames') ?>",
            type: "GET",
            success: function(data) {
                let jobNames = JSON.parse(data);
                jobNames.forEach(function(job) {
                    $('#filterJobName').append(new Option(job.JobName, job.JobName));
                });
            }
        });

        $('#filterJobName').change(function() {
            var jobName = $(this).val();
            
            // Reset customer name filter
            $('#filterCustomerName').empty();  // Kosongkan customer name
            $('#filterCustomerName').append('<option value="">-- All Customers --</option>');  // Tambahkan opsi default
            
            if (jobName) {
                // Fetch customer names yang terkait dengan Job Name
                $.ajax({
                    url: "<?= base_url('ReportJob/getCustomersByJobName') ?>", 
                    type: "GET",
                    data: { jobName: jobName },
                    success: function(data) {
                        let customers = JSON.parse(data);
                        customers.forEach(function(customer) {
                            $('#filterCustomerName').append(new Option(customer.CustomerName, customer.CustomerName));
                        });
                    }
                });
            } else {
                loadCustomerNames();
            }
        });

        function loadCustomerNames(){
            $.ajax({
                url: "<?= base_url('ReportJob/getCustomerNames') ?>", 
                type: "GET",
                success: function(data) {
                    let customers = JSON.parse(data);
                    // Reset customer name filter
                    $('#filterCustomerName').empty(); // Kosongkan customer name
                    $('#filterCustomerName').append('<option value="">-- All Customers --</option>'); // Tambahkan opsi default
                    customers.forEach(function(customer) {
                        $('#filterCustomerName').append(new Option(customer.CustomerName, customer.CustomerName));
                    });
                }
            });
        }

        if ($('#filterJobName').val() === '') {
            loadCustomerNames();
        }


        $('#tableJobEvidenceReport').DataTable({
            processing: true, 
            serverSide: true,
            ajax: { 
                url: "<?= base_url('ReportJob/JobEvidenceReport') ?>", 
                type: "GET",
                data: function (d) {
                    d.jobNameFilter = $('#filterJobName').val();
                    d.customerNameFilter = $('#filterCustomerName').val(); 
                    d.totalPhotoFilter = $('#filterTotalPhoto').val();
                    d.fromDateFilter = $('#filterFromDate').val(); 
                    d.untilDateFilter = $('#filterUntilDate').val();
                }
            },
            columns: [
                { data: "no" },
                { data: "JobName" },
                { data: "CustomerName" },
                { data: "TotalPhoto" },
                { data: "LastPhotoDate" },
                { 
                    data: "Photos", 
                    render: function(data, type, row) {
                        if (!data) return '<button class="btn btn-sm btn-secondary" disabled>No Photo</button>';
                        let photos = [];
                        try {
                            photos = JSON.parse(data);
                        } catch (e) {
                            console.warn('Invalid JSON:', data);
                        }

                        const valid = photos.filter(p => p.photo && p.photo !== "null");

                        // if (!valid.length)
                        //     return '<button class="btn btn-sm btn-secondary" disabled>No Photo</button>';

                        // // simpan data foto ke tombol
                        // return `
                        //     <button class="btn btn-sm btn-info view-photo" 
                        //             data-photos='${JSON.stringify(valid)}'>
                        //         📷 View
                        //     </button>
                        // `;

                        if (type === 'display') {
                            if (!valid.length)
                                return '<button class="btn btn-sm btn-secondary" disabled>No Photo</button>';
                            return `
                                <button class="btn btn-sm btn-info view-photo" 
                                        data-photos='${JSON.stringify(valid)}'>
                                    📷 View
                                </button>
                            `;
                        }

                        // saat export ke Excel → tampilkan URL fotonya
                        if (type === 'export') {
                            return valid.map(v => v.photo).join('\n'); // newline antar URL
                        }

                        return '';

                    },
                    className: "text-center"
                }
            ],
            columnDefs: [
                {
                    targets: [2],  
                    type: 'string', 
                },
                {
                    targets: [3],  
                    type: 'num',   
                },
                {
                    targets: [4],  
                    type: 'datetime', 
                    render: function(data) {
                        return data === '-' ? '-' : moment(data).format('YYYY-MM-DD HH:mm:ss');
                    }
                }
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
                    title: `Job Evidence Report (${(today.getMonth()+1).toString().padStart(2, '0')}/${today.getDate().toString().padStart(2, '0')}/${today.getFullYear()})`,
                    filename: fileName,
                    className: 'btn btn-success me-2',
                    exportOptions: {
                        // columns: ':visible',
                        columns: [0, 1, 2, 3, 4],
                        orthogonal: 'export' // <--- ini kuncinya!
                    },
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
                            $('row c[r^="F"]', sheet).attr('s', '4');

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
            order: [[4, "desc"]]
        });

        // Reload Otomatic
        $('#filterJobName, #filterCustomerName, #filterTotalPhoto, #filterFromDate, #filterUntilDate').change(function() {
            $('#tableJobEvidenceReport').DataTable().ajax.reload();  
        });

        // Submit Filter
        $('#formFilterJobEvidenceReport').submit(function(e) {
            e.preventDefault();
            $('#tableJobEvidenceReport').DataTable().ajax.reload();
        });

        // Reset Filter
        $('#resetFilterJobEvidenceReport').click(function() {
            $('#formFilterJobEvidenceReport')[0].reset(); 
            loadCustomerNames();
            $('#tableJobEvidenceReport').DataTable().ajax.reload();
            const untilInput = document.querySelector('input[name="filterUntilDate"]');
            untilInput.removeAttribute("min");
        });
    });

    // From .... Until .... Job Assignment Efficiency
    document.addEventListener("DOMContentLoaded", function () {
        const fromInput = document.querySelector('input[name="fromAssignAtJobAssignmentEfficiencyReport"]');
        const untilInput = document.querySelector('input[name="toAssignAtJobAssignmentEfficiencyReport"]');

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

    // From .... Until .... Job Assignment Efficiency
    document.addEventListener("DOMContentLoaded", function () {
        const fromInput = document.querySelector('input[name="fromJobAssignmentEfficiencyReport"]');
        const untilInput = document.querySelector('input[name="untilJobAssignmentEfficiencyReport"]');

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


    // From .... Until .... Job Completion Status Report
    document.addEventListener("DOMContentLoaded", function () {
        const fromInput = document.querySelector('input[name="jobDateFromJobCompletionStatusReport"]');
        const untilInput = document.querySelector('input[name="jobDateUntilJobCompletionStatusReport"]');

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

    // From .... Until .... Job Evidence Report
    document.addEventListener("DOMContentLoaded", function () {
        const fromInput = document.querySelector('input[name="filterFromDate"]');
        const untilInput = document.querySelector('input[name="filterUntilDate"]');

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
        

    // Event tombol "View Photo"
    $('#tableJobCompliance').on('click', '.view-photo', function() {
        const photos = JSON.parse($(this).attr('data-photos'));
        
        // kalau cuma 1 foto
        if (photos.length === 1) {
            $('#JobCompliancePhotosModal .modal-body').html(`
                <img src="${photos[0].photo}" class="img-fluid rounded shadow-sm mb-2" style="max-height:70vh;object-fit:contain;">
                <p class="text-muted small">${photos[0].caption || ''}</p>
            `); 
            $('#JobCompliancePhotosModal').modal('show');
            return;
        }

        // kalau banyak foto → tampilkan navigasi
        let html = `
        <div id="jobCompliancePhotosCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="false">
            <div class="carousel-inner">
                ${photos.map((p, i) => `
                    <div class="carousel-item ${i === 0 ? 'active' : ''}">
                        <div class="d-flex flex-column align-items-center justify-content-center" style="max-height:75vh;">
                            <img src="${p.photo}" 
                                alt="photo-${i}" 
                                class="img-fluid rounded shadow-sm"
                                style="max-height:65vh; object-fit:contain;">
                            <p class="mt-2 text-center small text-muted bg-dark bg-opacity-50 px-2 rounded">${p.caption || ''}</p>
                        </div>
                    </div>
                `).join('')}
            </div>

            <!-- Tombol Navigasi -->
            <a class="carousel-control-prev" href="#jobCompliancePhotosCarousel" role="button" data-slide="prev"
            style="width:10%; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-circle-chevron-left fa-2x text-dark "></i>
            </a>
            <a class="carousel-control-next" href="#jobCompliancePhotosCarousel" role="button" data-slide="next"
            style="width:10%; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-circle-chevron-right fa-2x text-dark "></i>
            </a>
        </div>
        `;

        $('#JobCompliancePhotosModal .modal-body').html(html);
        $('#JobCompliancePhotosModal').modal('show');
    });


    // 
    $('#tableJobEvidenceReport').on('click', '.view-photo', function() {
        const photos = JSON.parse($(this).attr('data-photos'));

        if (photos.length === 1) {
            $('#JobEvidencePhotosModal .modal-body').html(`
                <img src="${photos[0].photo}" class="img-fluid rounded shadow-sm mb-2" style="max-height:70vh;object-fit:contain;">
                <p class="text-muted small">${photos[0].caption || ''}</p>
            `);
            $('#JobEvidencePhotosModal').modal('show');
            return;
        }

        let html = `
        <div id="jobEvidencePhotosCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="false">
            <div class="carousel-inner">
                ${photos.map((p, i) => `
                    <div class="carousel-item ${i === 0 ? 'active' : ''}">
                        <div class="d-flex flex-column align-items-center justify-content-center" style="max-height:75vh;">
                            <img src="${p.photo}" 
                                alt="photo-${i}" 
                                class="img-fluid rounded shadow-sm"
                                style="max-height:65vh; object-fit:contain;">
                            <p class="mt-2 text-center small text-muted bg-dark bg-opacity-50 px-2 rounded">${p.caption || ''}</p>
                        </div>
                    </div>
                `).join('')}
            </div>

            <!-- Tombol Navigasi -->
            <a class="carousel-control-prev" href="#jobEvidencePhotosCarousel" role="button" data-slide="prev"
            style="width:10%; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-circle-chevron-left fa-2x text-dark"></i>
            </a>
            <a class="carousel-control-next" href="#jobEvidencePhotosCarousel" role="button" data-slide="next"
            style="width:10%; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-circle-chevron-right fa-2x text-dark"></i>
            </a>
        </div>
        `;

        $('#JobEvidencePhotosModal .modal-body').html(html);
        $('#JobEvidencePhotosModal').modal('show');
    });


    $(document).on("click", ".btn_detail_job", function() {

        const jobId = $(this).data('id'); // ambil ID job dari tombol

        // Tampilkan modal dulu
        $('#modal_detail_job').modal('show');

        // Load konten dari controller (kirim jobId ke backend)
        $("#modal_detail_job .modal-body").load("<?= base_url('ReportJob/detail_job/') ?>" + jobId);
    });

</script>