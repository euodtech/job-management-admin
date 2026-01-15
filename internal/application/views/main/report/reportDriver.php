<style>
    .content-wrapper {
        min-height: max-content !important;
    }

    #tableUserPerformance th, 
    #tableUserActivity th, 
    #tableUserWorkloadReport th {
        text-align: center;
    }

    /* #tableUserPerformance td
    #tableUserActivity td,
    #tableUserWorkloadReport td {
        text-align: left;
    }

    #tableUserPerformance td:nth-child(1),
    #tableUserPerformance td:nth-child(3),
    #tableUserPerformance td:nth-child(4),
    #tableUserPerformance td:nth-child(5),
    #tableUserPerformance td:nth-child(6) {
        text-align: center !important;
    } */

    #tableUserPerformance tr.child td {
        text-align: left !important;
        background-color: #ffffffff;
        white-space: nowrap;
    }
    
    table.dataTable>tbody>tr.child span.dtr-title {
        display: inline-block;
        font-weight: bold;
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
        /*  */
        #tableUserActivity th{
            text-align: center;
        }

        #tableUserActivity td:nth-child(2)
        #tableUserActivity td:nth-child(3),
        #tableUserActivity td:nth-child(4) {
            text-align: center;
        }
        
        #tableUserActivity td:nth-child(1),
        #tableUserActivity td:nth-child(5) {
            text-align: left !important;
        }

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

        table.dataTable tbody tr.child span.dtr-title {
            display: inline-block;
            /* min-width: 200px; */
            font-weight: bold;
            /* flex: 2; */
        }


        table.dataTable>tbody>tr.child span.dtr-data {
            text-align: center !important;
            width: 100px !important;
            margin-left: 100px;
            flex: 3;
        }
    }
</style>

<div class="content-wrapper" >
    <section class="content-header">
        <div class="container-fluid">
            <h4><?= $title ?></h4>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info card-outline">
                    <div class="card-header">
                        <h5 class="card-title">📌 Report Rider</h5>
                    </div>
                    <div class="card-body">
                        <form id="formFilterUserLoginActivityReport" method="GET" action="">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label>From Date</label>
                                    <input type="date" name="from_UserLoginActivityReport" id="from_UserLoginActivityReport" class="form-control" value="<?= (isset($_GET['from_UserLoginActivityReport'])) ? $_GET['from_UserLoginActivityReport'] : date('Y-m-01') ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>Until Date</label>
                                    <input type="date" name="until_UserLoginActivityReport" id="until_UserLoginActivityReport" class="form-control" value="<?= (isset($_GET['until_UserLoginActivityReport'])) ? $_GET['until_UserLoginActivityReport'] : date('Y-m-d')?>" >
                                </div>
                                <div class="col-md-3 align-self-end mt-3">
                                    <!-- <button type="submit" class="btn btn-info">Filter</button> -->
                                    <button type="button" class="btn btn-secondary" id="resetFilterUserLoginActivityReport" >Reset</button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%" id="tableUserLogin">
                                <thead>
                                    <tr>
                                    <th>No</th>
                                    <th>Fullname</th>
                                    <th>Email</th>
                                    <th>Cancel Job</th>
                                    <th>Total Job</th>
                                    <th>Complete Job</th>
                                    <th>Ongoing Job</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                        
                    </div>
                    </div>
                </div>
            </div>



        </div>
    </section>
</div>


<div class="modal fade" id="modal_detail_job" tabindex="-1" aria-labelledby="modal_detail_job" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
        <div class="modal-header bg-info text-white">
            <h5 class="modal-title" id="modal_detail_job_header"></h5>
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

    // User Login Activity
    $(document).ready(function() {

        let form_date_until = $('#from_UserLoginActivityReport').val();
        $('#until_UserLoginActivityReport').attr('min', form_date_until);

        $('#from_UserLoginActivityReport').on('change', function() {
            let fromDate = $(this).val();
            $('#until_UserLoginActivityReport').attr('min', fromDate); // batas bawah until date
        });

        $('#until_UserLoginActivityReport').on('change', function() {
            let untilDate = $(this).val();
            let fromDate = $('#from_UserLoginActivityReport').val();

            if (untilDate < fromDate) {
                alert('Until Date cannot be earlier than From Date.');
                $(this).val(fromDate); // reset jadi sama dengan from date
            }
        });

        let today = new Date();
        let now = `${(today.getMonth()+1).toString().padStart(2, '0')}_${today.getDate().toString().padStart(2, '0')}_${today.getFullYear()}`;
        let fileName = `User_Login_Activity_${now}`;

        var table = $('#tableUserLogin').DataTable({
            processing: true, 
            serverSide: true,
            order: [[1, 'asc']],
            columnDefs: [
                { targets: [3, 4, 5, 6], orderable: false } // kolom 3–6 gak bisa di-sort
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
                    "render":  function(data, type, row) {

                        let total = 0;
                        let jobID = ''; 

                        // Pastikan data itu array
                        if (Array.isArray(data) && data.length > 0) {
                            // return "Yes";

                            data.forEach(element => {
                                total += 1
                                jobID += element.JobID + ', ';
                            });

                            jobID = jobID.replace(/, $/, '');
                        } else {

                            jobID = 0;

                        }

                        

                        return `<button type='button' class='btn btn-sm btn-danger btn_detail_cancel_job' data-job-id='${jobID}' >${total}</button>`;

                    }
                },
                { "data": "TotalJob", className: "text-center" },
                { 
                    "data": "CompleteJob", 
                    "className": "text-center",
                    "render" : function(data, type, row) {
                        return `<button type='button' class='btn btn-sm btn-primary btn_detail_complete_job' data-user-id='${row.UserID}' data-from-date='${row.FromDate}' data-until-date='${row.UntilDate}'>${data}</button>`;

                    }
                },
                { 
                    "data": "OngoingJob", 
                    "className": "text-center" ,
                    "render" : function(data, type, row) {
                        return `<button type='button' class='btn btn-sm btn-primary btn_detail_ongoing_job' data-user-id='${row.UserID}' data-from-date='${row.FromDate}' data-until-date='${row.UntilDate}'>${data}</button>`;
                    }
                }

            ],
            responsive: false, 
            scrollX: true ,
            pageLength: 10, 
            lengthMenu: [10, 25, 50, 100],
            dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    title: `Report Rider (${today.getDate().toString().padStart(2, '0')}/${(today.getMonth()+1).toString().padStart(2, '0')}/${today.getFullYear()})`,
                    filename: fileName,
                    className: 'btn btn-sm btn-primary ',
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
            searching: true,
        });

        // Reload Otomatic
        $('#from_UserLoginActivityReport, #until_UserLoginActivityReport').on('change', function() {
            $('#tableUserLogin').DataTable().ajax.reload();  
        });

        // Submit Filter
        // $('#formFilterUserLoginActivityReport').on('submit', function(e){
        //     e.preventDefault();
        //     table.ajax.reload();
        // });

        // Reset Filter
        $('#resetFilterUserLoginActivityReport').on('click', function() {
            $('#formFilterUserLoginActivityReport')[0].reset(); 
            $('#tableUserLogin').DataTable().ajax.reload();
            const untilInput = document.querySelector('input[name="until_UserLoginActivityReport"]');
            untilInput.removeAttribute("min");
        });
    });

    $(document).on("click", ".btn_detail_complete_job", function() {

        const userID = $(this).data('user-id'); // ambil ID job dari tombol
        const fromDate = $(this).data('from-date'); 
        const untilDate = $(this).data('until-date'); 
        // Tampilkan modal dulu
        $('#modal_detail_job').modal('show');
        $('#modal_detail_job #modal_detail_job_header').text('Detail Complete Job');



        // Load konten dari controller (kirim jobId ke backend)
        $("#modal_detail_job .modal-body").load("<?= base_url('ReportDriver/detail_job/') ?>" + userID + "/2/" + fromDate + "/" + untilDate );
    });

    $(document).on("click", ".btn_detail_ongoing_job", function() {

        const userID = $(this).data('user-id');
        const fromDate = $(this).data('from-date'); 
        const untilDate = $(this).data('until-date'); 
        // Tampilkan modal dulu
        $('#modal_detail_job').modal('show');
        $('#modal_detail_job #modal_detail_job_header').text('Detail Ongoing Job');



        // Load konten dari controller (kirim jobId ke backend)
        $("#modal_detail_job .modal-body").load("<?= base_url('ReportDriver/detail_job/') ?>" + userID + "/1/" + fromDate + "/" + untilDate );
    });

    $(document).on("click", ".btn_detail_cancel_job", function() {

        const jobID = $(this).data('job-id');
        // Tampilkan modal dulu
        $('#modal_detail_job').modal('show');
        $('#modal_detail_job #modal_detail_job_header').text('Detail Cancel Job');

        // Load konten dari controller (kirim jobId ke backend)
        $("#modal_detail_job .modal-body").load("<?= base_url('ReportDriver/detail_job_cancel?job_id=') ?>"  + encodeURIComponent(jobID));
    });

</script>