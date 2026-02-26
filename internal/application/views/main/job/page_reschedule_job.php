<style>

    #tableJobRider_wrapper .dataTables_scrollHead table.dataTable thead th,
    table#tableJobRider.dataTable thead th {
        text-align: center !important;
    }

    #tableJobRider tbody tr td {
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

</style>

<!-- Content Header -->
<div class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-xl font-bold text-gray-800">Reschedule Job</h1>
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

            <!-- Tab Navigation -->
            <div class="flex items-center gap-0 border-b border-gray-200 mb-4">
                <button type="button" class="tab-btn active" data-tab="pending">
                    Pending Approval
                    <span id="pending-count-badge" class="ml-1.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-amber-500 rounded-full hidden">0</span>
                </button>
                <button type="button" class="tab-btn" data-tab="approved">
                    Approved
                </button>
                <button type="button" class="tab-btn" data-tab="rejected">
                    Rejected
                </button>
            </div>

            <!-- Date Filters (hidden when Pending tab is active) -->
            <div id="date-filters" style="display: none;">
                <!-- Preset date filter buttons -->
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="text-sm font-medium text-gray-600 mr-1">Period:</span>
                    <button type="button" class="date-preset-btn-reschedule rounded-full px-4 py-1.5 text-sm font-medium border transition-all duration-200 cursor-pointer" data-preset="1day">Today</button>
                    <button type="button" class="date-preset-btn-reschedule rounded-full px-4 py-1.5 text-sm font-medium border transition-all duration-200 cursor-pointer" data-preset="7days">7 Days</button>
                    <button type="button" class="date-preset-btn-reschedule rounded-full px-4 py-1.5 text-sm font-medium border transition-all duration-200 cursor-pointer" data-preset="1month">1 Month</button>
                    <button type="button" class="date-preset-btn-reschedule rounded-full px-4 py-1.5 text-sm font-medium border transition-all duration-200 cursor-pointer" data-preset="custom">Custom</button>
                </div>
                <!-- Custom date range (hidden by default) -->
                <div id="customDateRangeReschedule" class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-3 mb-3 overflow-hidden transition-all duration-300 ease-in-out" style="max-height: 0; opacity: 0; margin-bottom: 0;">
                    <div>
                        <label for="from_date_job" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" value="<?= date('Y-m-d', strtotime('-6 days')) ?>" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" name="from_date_job" id="from_date_job">
                    </div>
                    <div>
                        <label for="until_date_job" class="block text-sm font-medium text-gray-700 mb-1">Until Date</label>
                        <input type="date" value="<?= date('Y-m-d') ?>" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" name="until_date_job" id="until_date_job">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="button" id="applyCustomDateReschedule" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors cursor-pointer"><svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg> Apply</button>
                        <button type="button" id="resetDateReschedule" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">Reset</button>
                    </div>
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
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Created</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Reschedule</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Driver</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Job</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Reason</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Status</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Action</th>
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
                        <textarea name="reason" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" rows="5" placeholder="Input The Reason" required></textarea>
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

    // === Date preset helpers ===
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
        $('.date-preset-btn-reschedule').each(function() {
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
        var el = $('#customDateRangeReschedule');
        if (show) {
            el.css({ 'max-height': '200px', 'opacity': '1', 'margin-bottom': '0.75rem' });
        } else {
            el.css({ 'max-height': '0', 'opacity': '0', 'margin-bottom': '0' });
        }
    }

    function applyPreset(preset) {
        var range = getPresetRange(preset);
        $('#from_date_job').val(range.from);
        $('#until_date_job').val(range.until);
        $('#until_date_job').attr('min', range.from);
        setActivePreset(preset);
        showCustomDateRange(false);
        if (currentTab !== 'pending') {
            table.ajax.reload();
        }
    }

    // --- Initialize with 7 Days active ---
    setActivePreset('7days');

    // --- Preset button clicks ---
    $('.date-preset-btn-reschedule').on('click', function() {
        var preset = $(this).data('preset');
        if (preset === 'custom') {
            setActivePreset('custom');
            showCustomDateRange(true);
        } else {
            applyPreset(preset);
        }
    });

    // --- Apply custom date range ---
    $('#applyCustomDateReschedule').on('click', function() {
        setActivePreset('custom');
        if (currentTab !== 'pending') {
            table.ajax.reload();
        }
    });

    // --- Reset to 7 Days default ---
    $('#resetDateReschedule').on('click', function() {
        applyPreset('7days');
    });

    // === Current active tab state ===
    var currentTab = 'pending';

    // === Tab click handler ===
    $('.tab-btn').on('click', function() {
        var tab = $(this).data('tab');
        if (tab === currentTab) return;

        // Update tab visual state
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        currentTab = tab;

        // Show/hide date filters
        if (tab === 'pending') {
            $('#date-filters').slideUp(200);
        } else {
            $('#date-filters').slideDown(200);
        }

        // Adjust sort order: oldest first for pending, newest first for processed
        if (tab === 'pending') {
            table.order([1, 'asc']);
        } else {
            table.order([1, 'desc']);
        }

        // Reload the DataTable with new filter (reset to page 1)
        table.ajax.reload(null, true);
    });

    // === Date filter validation ===
    $('#from_date_job').on('change', function() {
        var fromDate = $(this).val();
        $('#until_date_job').attr('min', fromDate);
    });

    $('#until_date_job').on('change', function() {
        var untilDate = $(this).val();
        var fromDate = $('#from_date_job').val();
        if (untilDate < fromDate) {
            alert('Until Date cannot be earlier than From Date.');
            $(this).val(fromDate);
        }
    });

    // Silent reload flag: suppresses the loading overlay during background auto-refresh
    var silentReload = false;
    // Silent search flag: suppresses the loading overlay during search typing
    var silentSearch = false;

    var table = $('#tableJobRider').DataTable({
        processing: false,
        serverSide: true,
        searching: true,
        ajax: {
            url: "<?= base_url('Job/getJobReschedule') ?>",
            type: "GET",
            data: function(d) {
                d.statusFilter = currentTab;
                if (currentTab !== 'pending') {
                    d.dateFrom = $('#from_date_job').val();
                    d.dateUntil = $('#until_date_job').val();
                }
            }
        },
        columns: [
            { data: "no", className: "text-center" },
            {
                data: "JobDate",
                className: "text-center whitespace-nowrap",
                render: function(data) {
                    return data ? moment(data).format('DD MMM YYYY') : '-';
                }
            },
            {
                data: "RequestDateJob",
                className: "text-center whitespace-nowrap",
                render: function(data) {
                    return data ? moment(data).format('DD MMM YYYY') : '-';
                }
             },
            {
                data: "Fullname",
                className: "text-center whitespace-nowrap",
             },
            {
                data: "JobName",
                className: "text-center"
            },
            {
                data: "Reason",
                className: "text-center whitespace-nowrap",
                render: function(data, type, row) {
                    var html = data || '-';
                    if (row.ReasonReject) {
                        html += '<br><span class="text-xs text-red-500 italic">Reject: ' + $('<span>').text(row.ReasonReject).html() + '</span>';
                    }
                    return html;
                }
            },
            {
                data: "StatusApproved",
                className: "text-center",
                render: function(data) {
                    var badge = { dot: '', bg: '', text: '', ring: '', label: '' };
                    if (data == 1) {
                        badge = { dot: 'bg-amber-500', bg: 'bg-amber-50', text: 'text-amber-700', ring: 'ring-amber-600/20', label: 'Pending' };
                    } else if (data == 2) {
                        badge = { dot: 'bg-green-500', bg: 'bg-green-50', text: 'text-green-700', ring: 'ring-green-600/20', label: 'Approved' };
                    } else {
                        badge = { dot: 'bg-red-500', bg: 'bg-red-50', text: 'text-red-700', ring: 'ring-red-600/20', label: 'Rejected' };
                    }
                    return `<span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium ${badge.bg} ${badge.text} ring-1 ring-inset ${badge.ring}"><span class="size-1.5 rounded-full ${badge.dot}"></span>${badge.label}</span>`;
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
        order: [[1, 'asc']],
        paging: true,
        autoWidth: true,
        scrollX: true,
        searchDelay: 500,
    });

    // Track whether the DataTable is currently processing a request.
    var isProcessing = false;

    // Intercept the processing event on this specific table.
    // When silentReload or silentSearch is true, stop propagation so the global
    // overlay handler in footer.php never fires. This keeps background refreshes
    // and search-while-typing seamless.
    $('#tableJobRider').on('processing.dt', function(e, settings, processing) {
        isProcessing = processing;
        if (silentReload || silentSearch) {
            e.stopPropagation();
            if (!processing) {
                silentReload = false;
                silentSearch = false;
            }
        }
    });

    // Suppress loading overlay when the user types in the search box
    $('#tableJobRider').on('search.dt', function() {
        silentSearch = true;
    });

    setInterval(function() {
        if (isProcessing) return;
        silentReload = true;
        table.ajax.reload(null, false);
        refreshPendingCount();
    }, 10000);

    // Date change in custom mode triggers reload
    $('#from_date_job, #until_date_job').on('change', function() {
        if (currentTab !== 'pending') {
            setActivePreset('custom');
            showCustomDateRange(true);
        }
    });

    // === Pending count badge ===
    function refreshPendingCount() {
        $.ajax({
            url: "<?= base_url('Job/getPendingRescheduleCount') ?>",
            type: "GET",
            dataType: "json",
            success: function(response) {
                var count = response.count || 0;
                var $badge = $('#pending-count-badge');
                $badge.text(count);
                if (count > 0) {
                    $badge.removeClass('hidden');
                } else {
                    $badge.addClass('hidden');
                }
            }
        });
    }

    // Initial load of pending count
    refreshPendingCount();

});

// handle button reject reschedule
$(document).on('click', '.btn_reject_reschedule', function(e) {

    e.preventDefault();
    let rescheduleID = $(this).data('reschedule-id');
    showModal('#modal_reject');
    $('#modal_reject #reschedule_id').val(rescheduleID);
})

</script>
