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
        <h1 class="text-xl font-bold text-gray-800">Reschedule Job</h1>
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

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="form-group mb-0">
                    <label for="from_date_job" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" value="<?= date('Y-m-d') ?>" class="form-control w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" name="from_date_job" id="from_date_job">
                </div>
                <div class="form-group mb-0">
                    <label for="until_date_job" class="block text-sm font-medium text-gray-700 mb-1">Date Until</label>
                    <input type="date" value="<?= date('Y-m-d') ?>" class="form-control w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" name="until_date_job" id="until_date_job">
                </div>
            </div>

        </div>

        <!-- Card Body -->
        <div class="px-5 py-4">
            <div class="overflow-x-auto">

                <input type="hidden" id="type_for_job" value="<?= $type_job ?>">
                <table class="table table-bordered table-striped w-100" id="tableJobRider">
                    <thead>
                        <tr class="whitespace-nowrap">
                            <th style="width: 10%; text-align: center;">No</th>
                            <th>Create Job</th>
                            <th>Reschedule Job</th>
                            <th>Rider</th>
                            <th>Job Name</th>
                            <th>Reason</th>
                            <th>Status Request</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Reject Reschedule Modal -->
<div id="modal_reject" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modalRejectLabel">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">

            <!-- Header -->
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800" id="modalRejectLabel">Reject Request Reschedule Job</h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal_reject">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-4 overflow-y-auto">
                <form id="form_reject" action="<?= base_url('Job/actionRescheduleJob/reject') ?>" method="post">
                    <div class="form-group mb-4">
                        <input type="hidden" id="reschedule_id" name="reschedule_id">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <textarea name="reason" class="form-control w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" rows="5" placeholder="Input The Reason" required></textarea>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="flex justify-end items-center gap-2 py-3 px-4 border-t border-gray-200">
                <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" data-hs-overlay="#modal_reject">Close</button>
                <button type="submit" form="form_reject" class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Save</button>
            </div>
        </div>
    </div>
</div>

<script>


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
                        <a href="${url_update_status_request}" class="btn-tw-success">Approve</a> | <button type="button" class="btn-tw-danger btn_reject_reschedule" data-reschedule-id="${data}">Reject</button>
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

// handle button reject reschedule
$(document).on('click', '.btn_reject_reschedule', function(e) {

    e.preventDefault();
    let rescheduleID = $(this).data('reschedule-id');
    showModal('#modal_reject');
    $('#modal_reject #reschedule_id').val(rescheduleID);
})

</script>
