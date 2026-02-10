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
        flex: 0 0 120px;
        font-weight: 600;
        color: #212529;
    }

    .content_header .value {
        flex: 1;
        color: #495057;
    }

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
        background-color: #ffc107;
        box-shadow: 2px 2px 8px rgba(255, 193, 7, 0.4);
        color: white;
    }

    .completed_job {
        background-color: #28a745;
        box-shadow: 2px 2px 8px rgba(40, 167, 69, 0.4);
        color: white;
    }

</style>

<!-- Content Header -->
<div class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-xl font-bold text-gray-800">Job Summary</h1>
        <nav class="flex">
            <ol class="flex items-center gap-1.5 text-sm">
                <li><a href="<?= base_url('home') ?>" class="text-primary hover:underline">Dashboard</a></li>
            </ol>
        </nav>
    </div>
</div>

<!-- Content -->
<div class="px-4 sm:px-6 lg:px-8 pb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200">

            <?php
            $colors = ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6'];
            $i = 0;
            ?>

            <!-- <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <?php foreach($customer as $val): ?>
                    <?php
                        $color = $colors[$i % count($colors)];
                        $i++;
                    ?>
                    <div class="flex items-center gap-4 rounded-xl p-4 shadow-sm" style="background-color: <?= $color ?>; color: white;">
                        <div>
                            <span class="text-lg"><i class="fas fa-user"></i> <?= $val['CustomerName'] ?? "Customer" ?></span>
                            <span class="block mt-2">Total Complaint : <strong id="complaint_job<?= $val['CustomerID'] ?>"></strong></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div> -->

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="form-group mb-0">
                    <label for="from_date_job" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" value="<?= date('Y-m-d') ?>" class="form-control w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" name="from_date_job" id="from_date_job">
                </div>
                <div class="form-group mb-0">
                    <label for="until_date_job" class="block text-sm font-medium text-gray-700 mb-1">Date Until</label>
                    <input type="date" value="<?= date('Y-m-d') ?>" class="form-control w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" name="until_date_job" id="until_date_job">
                </div>
                <div class="form-group mb-0 md:col-span-2">
                    <label for="customer_name_form" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <select name="customer_name_form" class="form-control select2bs4 w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" id="customer_name_form">
                        <option value="all">--- All Customer ---</option>
                        <?php foreach($customer as $val): ?>
                            <option value="<?= $val['CustomerID'] ?>"><?= $val['CustomerName'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

        </div>

        <!-- Card Body -->
        <div class="px-5 py-4">
            <div class="overflow-x-auto">

                <input type="hidden" id="type_for_job" value="<?= $type_job ?>">
                <table class="table table-bordered table-striped" id="tableJobRider">
                    <thead>
                        <tr class="whitespace-nowrap">
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
                            <th>Status Job</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- History Modal (shared for Cancel and Reschedule) -->
<div id="modal_history_cancel_job" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modalHeader">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">

            <!-- Header -->
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800" id="modalHeader"></h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal_history_cancel_job">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-4 overflow-y-auto body_detail_history_cancel_job">

            </div>

            <!-- Footer -->
            <div class="flex justify-end items-center gap-2 py-3 px-4 border-t border-gray-200">
                <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" data-hs-overlay="#modal_history_cancel_job">Close</button>
            </div>
        </div>
    </div>
</div>

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

    $('#from_date_job').on('change', function() {
        let fromDate = $(this).val();
        $('#until_date_job').attr('min', fromDate);
    });

    $('#until_date_job').on('change', function() {
        let untilDate = $(this).val();
        let fromDate = $('#from_date_job').val();

        if (untilDate < fromDate) {
            alert('Until Date cannot be earlier than From Date.');
            $(this).val(fromDate);
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
        showModal('#modal');

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

                        userDriver = `<span class="text-red-600">Not Assign User</span>`;
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
                        htmlReturn = `<button class="btn-tw-primary btn_cancel_job" data-job-id="${row.JobID}"><i class="fas fa-eye"></i></button>`;
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
                        htmlReturn = `<button class="btn-tw-primary btn_reschedule" data-job-id="${row.JobID}"><i class="fas fa-eye"></i></button>`;
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

    showModal('#modal_history_cancel_job');
    $('#modal_history_cancel_job #modalHeader').text('Detail Cancel Job');
    $('#modal_history_cancel_job .body_detail_history_cancel_job').load('<?= base_url('Job/historyCancelJob?jobID=') ?>' + jobID);
});

$(document).on('click', '.btn_reschedule', function() {

    let jobID = $(this).data('job-id');

    showModal('#modal_history_cancel_job');
    $('#modal_history_cancel_job #modalHeader').text('Detail Reschedule Job');
    $('#modal_history_cancel_job .body_detail_history_cancel_job').load('<?= base_url('Job/historyReschedule?jobID=') ?>' + jobID);
});

</script>
