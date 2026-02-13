<style>
    table#tableJobRider.dataTable thead th {
        text-align: center !important;
    }
    #tableJobRider tbody tr td {
        vertical-align: middle;
        text-align: center;
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

    /* Tab styles */
    .tab-btn {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
        border-bottom: 2px solid transparent;
        transition: all 0.15s;
        cursor: pointer;
        background: none;
        border-top: none;
        border-left: none;
        border-right: none;
    }
    .tab-btn:hover {
        color: #374151;
    }
    .tab-btn.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
    }
    .tab-panel {
        display: none;
    }
    .tab-panel.active {
        display: block;
    }

    /* Type badge */
    .type-badge {
        display: inline-flex;
        align-items: center;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        margin-left: 6px;
        vertical-align: middle;
    }
    .type-badge-li { background-color: #dbeafe; color: #1d4ed8; }
    .type-badge-rc { background-color: #dcfce7; color: #15803d; }
    .type-badge-sc { background-color: #fef3c7; color: #b45309; }
    .type-badge-dc { background-color: #fee2e2; color: #b91c1c; }
</style>

<!-- Content Header -->
<div class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-xl font-bold text-gray-800">Job Summary</h1>
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

<!-- Content -->
<div class="px-4 sm:px-6 lg:px-8 pb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200">

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="form-group mb-0">
                    <label for="from_date_job" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" value="<?= date('Y-m-d') ?>" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" name="from_date_job" id="from_date_job">
                </div>
                <div class="form-group mb-0">
                    <label for="until_date_job" class="block text-sm font-medium text-gray-700 mb-1">Date Until</label>
                    <input type="date" value="<?= date('Y-m-d') ?>" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" name="until_date_job" id="until_date_job">
                </div>
                <div class="form-group mb-0 md:col-span-2">
                    <label for="customer_name_form" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <select name="customer_name_form" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm select2bs4" id="customer_name_form">
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
                <table class="w-full text-sm" id="tableJobRider">
                    <thead class="bg-gray-50">
                        <tr class="text-center whitespace-nowrap">
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">#</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Date</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Job</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Customer</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Status</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Job Modal (Unified Tabbed) -->
<div id="modal_view" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modalViewLabel">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-3xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">

            <!-- Header -->
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800" id="modalViewLabel">Job Details</h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal_view">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-200 px-4">
                <button class="tab-btn active" data-tab-target="tab-overview">Overview</button>
                <button class="tab-btn" data-tab-target="tab-photos">Photos</button>
                <button class="tab-btn" data-tab-target="tab-history">History</button>
            </div>

            <!-- Body -->
            <div class="p-4 overflow-y-auto" style="max-height: 70vh;">

                <!-- Overview Tab -->
                <div id="tab-overview" class="tab-panel active">
                    <div class="content_header">
                        <div class="row-item">
                            <div class="label">Date:</div>
                            <div class="value" id="view_date"></div>
                        </div>
                        <div class="row-item">
                            <div class="label">Job Name:</div>
                            <div class="value" id="view_job_name"></div>
                        </div>
                        <div class="row-item">
                            <div class="label">Type:</div>
                            <div class="value" id="view_type"></div>
                        </div>
                        <div class="row-item">
                            <div class="label">Customer:</div>
                            <div class="value" id="view_customer"></div>
                        </div>
                        <div class="row-item">
                            <div class="label">Address:</div>
                            <div class="value" id="view_address"></div>
                        </div>
                        <div class="row-item">
                            <div class="label">Driver:</div>
                            <div class="value" id="view_driver"></div>
                        </div>
                        <div class="row-item">
                            <div class="label">Status:</div>
                            <div class="value" id="view_status"></div>
                        </div>
                        <div class="row-item">
                            <div class="label">Assigned:</div>
                            <div class="value" id="view_assign_date"></div>
                        </div>
                        <div class="row-item">
                            <div class="label">Finished:</div>
                            <div class="value" id="view_finish_date"></div>
                        </div>
                    </div>
                </div>

                <!-- Photos Tab -->
                <div id="tab-photos" class="tab-panel">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 job-gallery">
                        <div class="col-span-full flex flex-col items-center justify-center gap-1 text-gray-400 py-8">
                            <i class="fa-solid fa-image text-sm"></i>
                            <p class="text-sm">Select the Photos tab to load images</p>
                        </div>
                    </div>
                </div>

                <!-- History Tab -->
                <div id="tab-history" class="tab-panel">
                    <div class="view-history-content">
                        <div class="flex flex-col items-center justify-center gap-1 text-gray-400 py-8">
                            <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                            <p class="text-sm">Select the History tab to load records</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="flex justify-end items-center gap-2 py-3 px-4 border-t border-gray-200">
                <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" data-hs-overlay="#modal_view">Close</button>
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

function getTypeBadge(typeJob) {
    var map = {
        '1': { code: 'LI', cls: 'type-badge-li' },
        '2': { code: 'RC', cls: 'type-badge-rc' },
        '3': { code: 'SC', cls: 'type-badge-sc' },
        '4': { code: 'DC', cls: 'type-badge-dc' }
    };
    var t = map[typeJob];
    if (!t) return '';
    return '<span class="type-badge ' + t.cls + '">' + t.code + '</span>';
}

function getTypeLabel(typeJob) {
    var labels = { '1': 'Line Interrupt', '2': 'Reconnection', '3': 'Short Circuit', '4': 'Disconnection' };
    return labels[typeJob] || '-';
}

function getStatusBadge(status) {
    var badge;
    if (status == 1) {
        badge = { dot: 'bg-amber-500', bg: 'bg-amber-50', text: 'text-amber-700', ring: 'ring-amber-600/20', label: 'Ongoing' };
    } else if (status == 2) {
        badge = { dot: 'bg-green-500', bg: 'bg-green-50', text: 'text-green-700', ring: 'ring-green-600/20', label: 'Finished' };
    } else if (status == 3) {
        badge = { dot: 'bg-blue-500', bg: 'bg-blue-50', text: 'text-blue-700', ring: 'ring-blue-600/20', label: 'Reschedule' };
    } else {
        badge = { dot: 'bg-gray-400', bg: 'bg-gray-50', text: 'text-gray-600', ring: 'ring-gray-500/20', label: 'Awaiting' };
    }
    return '<span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium ' + badge.bg + ' ' + badge.text + ' ring-1 ring-inset ' + badge.ring + '"><span class="size-1.5 rounded-full ' + badge.dot + '"></span>' + badge.label + '</span>';
}

function returnDateFormatDetailJS(value) {
    if (!value) return '-';
    const date = new Date(value);
    const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const dayName = days[date.getDay()];
    const day = String(date.getDate()).padStart(2, '0');
    const monthName = months[date.getMonth()];
    const year = date.getFullYear();
    return dayName + ', ' + day + ' ' + monthName + ' ' + year;
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

    // Silent reload flag: suppresses the loading overlay during background auto-refresh
    var silentReload = false;

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
                className: "whitespace-nowrap",
                render: function(data) {
                    return data ? moment(data).format('DD MMM YYYY') : '-';
                }
            },
            {
                data: "JobName",
                render: function(data, type, row) {
                    return '<span>' + data + '</span>' + getTypeBadge(row.TypeJob);
                }
            },
            {
                data: "CustomerName",
                className: "whitespace-nowrap"
            },
            {
                data: "Status",
                className: "text-center",
                render: function(data) {
                    return getStatusBadge(data);
                }
            },
            {
                data: "JobID",
                className: "text-center whitespace-nowrap",
                orderable: false,
                render: function(data, type, row) {
                    return '<div class="inline-flex items-center justify-center gap-1.5">' +
                        '<button data-jobid="' + data + '" type="button" class="btn-tw-success buttonView" title="View Job"><i class="fas fa-eye"></i></button>' +
                        '</div>';
                }
            },
        ],
        responsive: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        paging: true,
        autoWidth: false,
        scrollX: false,
        order: [[1, 'desc']],
    });

    // Intercept the processing event on this specific table.
    // When silentReload is true, stop propagation so the global overlay handler
    // in footer.php never fires. This keeps background refreshes seamless.
    $('#tableJobRider').on('processing.dt', function(e, settings, processing) {
        if (silentReload) {
            e.stopPropagation();
            if (!processing) {
                silentReload = false;
            }
        }
    });

    setInterval(function() {
        silentReload = true;
        table.ajax.reload(null, false);
        refreshCard();
    }, 5000);

});

$('#customer_name_form, #from_date_job, #until_date_job').on('change', function() {
    $('#tableJobRider').DataTable().ajax.reload();
    refreshCard();
});

// ===================== UNIFIED VIEW MODAL =====================

var currentViewJobID = null;
var loadedTabs = {};

// Tab switching
$(document).on('click', '.tab-btn', function() {
    var target = $(this).data('tab-target');

    // Update active tab button
    $('.tab-btn').removeClass('active');
    $(this).addClass('active');

    // Update active panel
    $('.tab-panel').removeClass('active');
    $('#' + target).addClass('active');

    // Lazy-load tab content
    if (target === 'tab-photos' && !loadedTabs['photos']) {
        loadPhotosTab(currentViewJobID);
    }
    if (target === 'tab-history' && !loadedTabs['history']) {
        loadHistoryTab(currentViewJobID);
    }
});

// Handle View button click
$(document).on('click', '.buttonView', function(e) {
    e.preventDefault();

    var jobID = $(this).data('jobid');
    currentViewJobID = jobID;
    loadedTabs = {};

    // Reset tabs to Overview
    $('.tab-btn').removeClass('active');
    $('.tab-btn[data-tab-target="tab-overview"]').addClass('active');
    $('.tab-panel').removeClass('active');
    $('#tab-overview').addClass('active');

    // Reset content
    $('.job-gallery').html('<div class="col-span-full flex flex-col items-center justify-center gap-1 text-gray-400 py-8"><i class="fa-solid fa-spinner fa-spin text-sm"></i><p class="text-sm">Loading...</p></div>');
    $('.view-history-content').html('<div class="flex flex-col items-center justify-center gap-1 text-gray-400 py-8"><i class="fa-solid fa-spinner fa-spin text-sm"></i><p class="text-sm">Loading...</p></div>');

    showModal('#modal_view');

    // Populate Overview from AJAX
    $.ajax({
        url: '<?= base_url('Job/getJobDetail') ?>',
        method: 'post',
        data: { jobID: jobID },
        dataType: 'json',
        success: function(response) {
            if (!response) {
                $('#tab-overview .content_header').html('<p class="text-center text-gray-500 py-4">No details found.</p>');
                return;
            }

            var jobDate = response.JobDate ? returnDateFormatDetailJS(response.JobDate) : '-';
            $('#view_date').text(jobDate);
            $('#view_job_name').text(response.JobName || '-');
            $('#view_type').text(getTypeLabel(response.TypeJob));
            $('#view_customer').text(response.CustomerName || '-');
            $('#view_address').text(response.Address || '-');
            $('#view_driver').text(response.Fullname || 'Unassigned');
            $('#view_status').html(getStatusBadge(response.Status));
            $('#view_assign_date').text(response.AssignWhen ? returnDateFormatDetailJS(response.AssignWhen) : '-');
            $('#view_finish_date').text(response.FinishWhen ? returnDateFormatDetailJS(response.FinishWhen) : '-');

            $('#modalViewLabel').text('Job Details - ' + (response.JobName || ''));
        }
    });
});

function loadPhotosTab(jobID) {
    loadedTabs['photos'] = true;
    var galleryContainer = $('.job-gallery');
    galleryContainer.html('<div class="col-span-full flex flex-col items-center justify-center gap-1 text-gray-400 py-8"><i class="fa-solid fa-spinner fa-spin text-sm"></i><p class="text-sm">Loading photos...</p></div>');

    $.ajax({
        url: '<?= base_url('Job/getDetailPhoto') ?>',
        method: 'POST',
        data: { jobID: jobID },
        dataType: 'json',
        success: function(response) {
            galleryContainer.empty();
            if (response && response.length > 0) {
                response.forEach(function(item, index) {
                    galleryContainer.append('<div><img src="' + item.Photo + '" alt="Job Photo ' + (index + 1) + '" class="rounded-lg shadow-sm"></div>');
                });
            } else {
                galleryContainer.html('<div class="col-span-full flex flex-col items-center justify-center gap-1 text-gray-500 py-8"><i class="fa-solid fa-image text-sm"></i><p class="text-sm">No photos available</p></div>');
            }
        },
        error: function() {
            galleryContainer.html('<div class="col-span-full text-center text-red-500 py-8">Failed to load photos.</div>');
        }
    });
}

function loadHistoryTab(jobID) {
    loadedTabs['history'] = true;
    var container = $('.view-history-content');
    container.html('<div class="flex flex-col items-center justify-center gap-1 text-gray-400 py-8"><i class="fa-solid fa-spinner fa-spin text-sm"></i><p class="text-sm">Loading history...</p></div>');

    var cancelHtml = '';
    var rescheduleHtml = '';
    var completed = 0;

    // Load cancel history
    $.ajax({
        url: '<?= base_url('Job/historyCancelJob?jobID=') ?>' + jobID,
        method: 'GET',
        success: function(response) {
            cancelHtml = response;
        },
        complete: function() {
            completed++;
            if (completed === 2) renderHistory(container, cancelHtml, rescheduleHtml);
        }
    });

    // Load reschedule history
    $.ajax({
        url: '<?= base_url('Job/historyReschedule?jobID=') ?>' + jobID,
        method: 'GET',
        success: function(response) {
            rescheduleHtml = response;
        },
        complete: function() {
            completed++;
            if (completed === 2) renderHistory(container, cancelHtml, rescheduleHtml);
        }
    });
}

function renderHistory(container, cancelHtml, rescheduleHtml) {
    var html = '';

    var hasCancelContent = cancelHtml && $(cancelHtml).find('tbody tr').length > 0;
    var hasRescheduleContent = rescheduleHtml && $(rescheduleHtml).find('tbody tr').length > 0;

    if (hasCancelContent) {
        html += '<h4 class="text-sm font-semibold text-gray-700 mb-2">Cancel History</h4>';
        html += cancelHtml;
    }

    if (hasRescheduleContent) {
        if (hasCancelContent) html += '<div class="my-4 border-t border-gray-200"></div>';
        html += '<h4 class="text-sm font-semibold text-gray-700 mb-2">Reschedule History</h4>';
        html += rescheduleHtml;
    }

    if (!hasCancelContent && !hasRescheduleContent) {
        html = '<div class="flex flex-col items-center justify-center gap-1 text-gray-500 py-8"><i class="fa-solid fa-clock-rotate-left text-sm"></i><p class="text-sm">No history records found</p></div>';
    }

    container.html(html);
}

</script>
