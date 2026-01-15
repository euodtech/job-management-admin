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
                    <h1>Job Summary</h1>
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

                            <?php 
                            $colors = ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6']; 
                            $i = 0;
                            ?>
                            
                            <!-- <div class="row">
                                <?php foreach($customer as $val): ?>
                                    <?php 
                                        // Ambil warna berdasarkan index (muter ulang kalau warna habis)
                                        $color = $colors[$i % count($colors)];
                                        $i++;
                                    ?>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <div class="info-box" style="background-color: <?= $color ?>; color: white; border-radius: 10px;">
                                            <div class="info-box-content">
                                                <span style="font-size: 18px;"><i class="fas fa-user"></i> <?= $val['CustomerName'] ?? "Customer" ?></span>
                                                    <span class="mt-2">Total Complaint : <strong id="complaint_job<?= $val['CustomerID'] ?>"></strong></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div> -->

                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label for="">Date From</label>
                                    <input type="date" value="<?= date('Y-m-d') ?>" class="form-control" name="from_date_job" id="from_date_job">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="">Date Until</label>
                                    <input type="date" value="<?= date('Y-m-d') ?>" class="form-control" name="until_date_job" id="until_date_job">
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Customer</label>
                                        <select name="customer_name_form" class="form-control select2bs4" id="customer_name_form">
                                            <option value="all">--- All Customer ---</option>
                                            <?php foreach($customer as $val): ?>
                                                <option value="<?= $val['CustomerID'] ?>"><?= $val['CustomerName'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">

                                <input type="hidden" id="type_for_job" value="<?= $type_job ?>">
                                <!--  -->
                                <table class="table table-bordered table-striped" id="tableJobRider">
                                    <thead class="">
                                        <tr style="white-space: nowrap;">
                                            <th style="width: 10%; text-align: center;">No</th>
                                            <th>Create Job</th>
                                            <th>Job Name</th>
                                            <th>To Customer</th>
                                            <th>Customer Address</th>
                                            <th>User Get The Job</th>
                                            <th>Type Job</th>
                                            <th>Assign Date</th>
                                            <th>History Cancel</th>
                                            <th>History Reschedule</th>
                                            <th>Finish Date</th>
                                            <th >Status Job</th>
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

<div class="modal fade" id="modal_history_cancel_job" tabindex="-1" role="dialog" aria-labelledby="modalCameraLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <!-- bisa ganti modal-lg ke modal-sm/modal-xl -->
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalHeader"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body body_detail_history_cancel_job">
                
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery"></script>

<script>

var type_job = $('#type_for_job').val();

function refreshCard() {


    $.ajax({
        url: '<?= base_url('Job/getDataJobForCardJobSummary') ?>',
        method: "post",
        data: {
            dateFrom : $('#from_date_job').val(),
            dateUntil : $('#until_date_job').val()
        },
        dataType: 'json',
        success: function(resp) {

            resp.forEach(function(item ,index) {
                $('#complaint_job' + item.CustomerID).text(item.TotalJob);
            });
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });
}


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


    refreshCard();

    let buttonAdd = $('#addButton');
    let buttonEdit = $('.buttonEdit');
    let buttonDetail = $('.buttonDetail');
    let buttonCamera = $('.buttonCamera');
    let buttonDelete = $('.buttonDelete');
    let modal = $('#modal');
    let modalCamera = $('#modal_camera');
    let modalDetail = $('#modal_detail');
    let modalDelete = $('#modal_delete');
    let textHeaderModal = $("#modalAddLabel");
    let textHeaderModalDelete = $("#modalDeleteLabel");
    let formUser = $("#formAddUser");
    let formUserDelete = $("#formDelete");

    // handle button add
    buttonAdd.on('click', function(e) {
        e.preventDefault();
        modal.modal('show');

        textHeaderModal.text('Add Job');
        formUser.attr("action", '<?= base_url('create-job') ?>')

        modal.find('#job_id').val('');
        modal.find('#job_name').val('');
        modal.find('#job_date').val('');
        modal.find('#customer_id').val('').trigger('change');

        let type_job_label = '<?= $this->uri->segment(1) ?>';
        let value_selected;

        switch (type_job_label) {
            case 'line-interruption-job':
                value_selected = "1";
                break;
            case 'short-circuit-job':
                value_selected = "3";
                break;
            case 'reconnection-job':
                value_selected = "2";
                break;
            case 'disconnection-job':
                value_selected = "4";
                break;
        
            default:
                value_selected = "1";
                break;
        }

        modal.find('#type_job').val(value_selected).trigger('change');
    });

    var table = $('#tableJobRider').DataTable({
        processing: false, 
        serverSide: true,
        searching: true,
        ajax: { 
            url: "<?= base_url('Job/getDataAllJobCustomer') ?>", 
            type: "GET",
            data : function(d) {
                d.customerID = $('#customer_name_form').val();
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
            { data: "JobName" },
            { 
                data: "CustomerName",
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('white-space', 'nowrap');
                }
            },
            { 
                data: "Address"
                
             },
            { 
                data: "Fullname",
                render: function(data, type, row) {

                    let userDriver;

                    if (data === null) {

                        userDriver = `<span class="text-danger">Not Assign User</span>`;
                    } else {
                        
                        userDriver = ` <span style="white-space: nowrap;">Driver Name : <strong>${data}</strong></span>`;
                    }

                    return userDriver;

                }
            },
            { 
                data: "TypeJob",
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('white-space', 'nowrap');
                },
                render : function(data , type, row) {

                    let labelTypeJob;
                    if(data == 1) {
                        labelTypeJob = "Line Interrupt";
                    } else if(data ==2) {
                        labelTypeJob = "Reconnection";
                    } else if(data  == 3) {
                        labelTypeJob = "Short Circuit";
                    } else if(data  == 4) {
                        labelTypeJob = "Disconnection";
                    }

                    return labelTypeJob;

                }
            },
            { 
                data: "AssignWhen",
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('white-space', 'nowrap');
                },
                render : function(data , type, row) {

                    

                    return data;

                }
            },
            {
                data : 'StatusCancelJob',
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('text-align', 'center');
                },
                render : function(data,type, row) {

                    let htmlReturn;

                    if(data.length > 0) {
                        htmlReturn = `<button class="btn btn-sm  btn-primary btn_cancel_job" data-job-id="${row.JobID}" ><i class="fas fa-eye" ></i></button>`;
                    } else {

                        htmlReturn = `-`;

                    }
                    return htmlReturn;
                }
            },
            {
                data : 'StatusReschedule',
                 createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('text-align', 'center');
                },
                render : function(data,type, row) {
                    let htmlReturn;

                    if(data.length > 0) {
                        htmlReturn = `<button class="btn btn-sm  btn-primary btn_reschedule"  data-job-id="${row.JobID}" ><i class="fas fa-eye" ></i></button>`;
                    } else {

                        htmlReturn = `-`;

                    }
                    return htmlReturn;
                }
            },
            { 
                data: "FinishWhen",
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('white-space', 'nowrap');
                },
                render : function(data , type, row) {

                    

                    return data;

                }
            },
            { 
                data: "Status",
                render: function(data, type, row) {

                    let labelStatusJob;
                    if(data == 1) {

                        labelStatusJob = `<span class='ongoing_job'>Ongoing Job</span>`;
                        
                    } else if(data ==2) {
                        
                        labelStatusJob = `<span class='finished_job'>Finished Job</span>`;
                    } else {
                        labelStatusJob = `<span class='awaiting_job'>Awaiting Driver</span>`;

                    }

                    return labelStatusJob;
                        
                }
            }
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
        refreshCard();
    }, 5000);

});

$('#customer_name_form, #from_date_job, #until_date_job').on('change', function() {
    $('#tableJobRider').DataTable().ajax.reload();  
    refreshCard();
});

// HANDLE BUTTON HISTORY CANCEL JOB
$(document).on('click', '.btn_cancel_job', function() {

    let jobID = $(this).data('job-id');

    $('#modal_history_cancel_job').modal('show');
    $('#modal_history_cancel_job #modalHeader').text('Detail Cancel Job');
    $('#modal_history_cancel_job .body_detail_history_cancel_job').load('<?= base_url('Job/historyCancelJob?jobID=') ?>' + jobID);
});

$(document).on('click', '.btn_reschedule', function() {

    let jobID = $(this).data('job-id');

    $('#modal_history_cancel_job').modal('show');
    $('#modal_history_cancel_job #modalHeader').text('Detail Reschedule Job');
    $('#modal_history_cancel_job .body_detail_history_cancel_job').load('<?= base_url('Job/historyReschedule?jobID=') ?>' + jobID);
});

</script>