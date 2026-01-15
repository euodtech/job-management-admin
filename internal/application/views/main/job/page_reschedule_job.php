<style>

    table.table-striped tbody tr td {
        vertical-align: middle;
    }

   .content_header {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .content_header .row-item {
        display: flex;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px dashed #ddd;
    }

    .content_header .row-item:last-child {
        border-bottom: none;
    }

    .content_header .label {
        flex: 0 0 120px; /* lebar tetap untuk label */
        font-weight: 600;
        color: #212529;
    }

    .content_header .value {
        flex: 1;
        color: #495057;
    }

    /* .job-gallery {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    } */

    .job-gallery img {
        width: 100%;
        border-radius: 10px;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .job-gallery img:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .container_status_job {
        width: fit-content;
        padding: 2px 1rem;
        border-radius: 6px;
    }
    .ongoing_job {
        background-color: #ffc107; /* kuning */
        box-shadow: 2px 2px 8px rgba(255, 193, 7, 0.4); /* bayangan kuning transparan */
        color: white;
    }

    .completed_job {
        background-color: #28a745; /* hijau */
        box-shadow: 2px 2px 8px rgba(40, 167, 69, 0.4); /* bayangan hijau transparan */
        color: white;
    }

</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reschedule Job</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url("home") ?>">Dashboard</a></li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header ">

                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label for="">Date From</label>
                                    <input type="date" value="<?= date('Y-m-d') ?>" class="form-control" name="from_date_job" id="from_date_job">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="">Date Until</label>
                                    <input type="date" value="<?= date('Y-m-d') ?>" class="form-control" name="until_date_job" id="until_date_job">
                                </div>
                            </div>


                        </div>

                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">

                                <input type="hidden" id="type_for_job" value="<?= $type_job ?>">
                                <!--  -->
                                <table class="table table-bordered table-striped w-100" id="tableJobRider">
                                    <thead class="">
                                        <tr style="white-space: nowrap;">
                                            <th style="width: 10%; text-align: center;">No</th>
                                            <th>Create Job</th>
                                            <th>Reschedule Job</th>
                                            <th>Rider</th>
                                            <th>Job Name</th>
                                            <th>Reason</th>
                                            <th>Status Request</th>
                                            <th >Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>



                            <!-- /.row -->
                        </div>
                        <!-- ./card-body -->

                        <!-- /.card-footer -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!--/. container-fluid -->
    </section>
</div>



<div class="modal fade" id="modal_reject" tabindex="-1" role="dialog" aria-labelledby="modalAddLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <!-- bisa ganti modal-lg ke modal-sm/modal-xl -->
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel">Reject Request Reschedule Job</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <form id="form_reject" action="<?= base_url('Job/actionRescheduleJob/reject') ?>" method="post">
                    <div class="form-group">

                        <input type="hidden" id="reschedule_id" name="reschedule_id">
                        <label for="">Reason</label>
                        <textarea name="reason" class="form-control" rows="5" placeholder="Input The Reason" required></textarea>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="submit" form="form_reject" class="btn btn-sm btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/jquery"></script>

<script>


$(document).ready(function() {

    let form_date_until = $('#from_date_job').val();
    $('#until_date_job').attr('min', form_date_until);

    // bayunovandrie
    $('#from_date_job').on('change', function() {
        let fromDate = $(this).val();
        $('#until_date_job').attr('min', fromDate); // batas bawah until date
    });

    $('#until_date_job').on('change', function() {
        let untilDate = $(this).val();
        let fromDate = $('#from_date_job').val();

        if (untilDate < fromDate) {
            alert('Until Date cannot be earlier than From Date.');
            $(this).val(fromDate); // reset jadi sama dengan from date
        }
    });

    var table = $('#tableJobRider').DataTable({
        processing: false, 
        serverSide: true,
        searching: true,
        ajax: { 
            url: "<?= base_url('Job/getJobReschedule') ?>", 
            type: "GET",
            data : function(d) {
                d.dateFrom = $('#from_date_job').val();
                d.dateUntil = $('#until_date_job').val();
            }
        },
        columns: [
            { data: "no", className: "text-center" },
            { 
                data: "JobDate",
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('white-space', 'nowrap');
                }
            },
            { 
                data: "RequestDateJob",
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('white-space', 'nowrap');
                }
             },
            { 
                data: "Fullname",
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('white-space', 'nowrap');
                },
                
             },
            { 
                data: "JobName"
            },
            { 
                data: "Reason",
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('white-space', 'nowrap');
                },
            },
            { 
                data: "StatusApproved",
                className: "text-center", 
                render: function(data, type, row) {

                    let labelStatusJob;
                    if(data == 1) {
                        labelStatusJob = `<span class='ongoing_job'>Pending</span>`;
                        
                    } else if(data ==2) {
                        
                        labelStatusJob = `<span class='finished_job'>Approve</span>`;
                    } else {
                        labelStatusJob = `<span class='awaiting_job'>Reject</span>`;

                    }

                    return labelStatusJob;
                        
                }
            },
            { 
                data: "RescheduledID",
                className: "text-center", 
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('white-space', 'nowrap');
                },
                render: function(data, type, row) {

                    let htmlButton;

                    let url_update_status_request = '<?= base_url('Job/actionRescheduleJob/approve/') ?>' + data; 

                    if(row['StatusApproved'] == 1) {
                        htmlButton = `
                        <a href="${url_update_status_request}" class="btn btn-sm btn-success">Approve</a> | <button type="button" class="btn btn-sm btn-danger btn_reject_reschedule " data-reschedule-id="${data}" >Reject</button>
                        `;
                        
                    }  else {
                        htmlButton = ` - `;

                    }

                    return htmlButton;
                        
                }
            },
        ],
        responsive: false, 
        pageLength: 10, 
        lengthMenu: [10, 25, 50, 100],
        order: [[0, 'desc']],
        paging: true,
        autoWidth: true,
        scrollX: true,
    });

    setInterval(function() {
        table.ajax.reload(null, false);
    }, 10000);

});

$('#from_date_job, #until_date_job').on('change', function() {
    $('#tableJobRider').DataTable().ajax.reload();  
});

// handle button edit job
$(document).on('click', '.btn_reject_reschedule', function(e) {

    e.preventDefault();
    let rescheduleID = $(this).data('reschedule-id');
    $('#modal_reject').modal('show');
    $('#modal_reject #reschedule_id').val(rescheduleID);
})

</script>