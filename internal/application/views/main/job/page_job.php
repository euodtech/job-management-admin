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
        <h1 class="text-xl font-bold text-gray-800">Job <?= $label_job ?></h1>
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
                <!-- Today's Job -->
                <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-dark">
                        <i class="fa-solid fa-calendar-day text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Today's Job</p>
                        <p class="text-lg font-bold text-gray-800" id="today_job_count"></p>
                    </div>
                </div>

                <!-- Ongoing Job -->
                <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg" style="background-color: #ffc107;">
                        <i class="fa-solid fa-briefcase text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Ongoing Job</p>
                        <p class="text-lg font-bold text-gray-800" id="ongoing_job_count"></p>
                    </div>
                </div>

                <!-- Upcoming Job -->
                <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg" style="background-color: #28a745;">
                        <i class="fa-solid fa-circle-check text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Upcomming Job</p>
                        <p class="text-lg font-bold text-gray-800" id="upcoming_job_count"></p>
                    </div>
                </div>

                <!-- Reschedule Job -->
                <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg" style="background-color: #17a2b8;">
                        <i class="fa-solid fa-calendar-plus text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Reschedule Job</p>
                        <p class="text-lg font-bold text-gray-800" id="reschedule_job_count"></p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Card Body -->
        <div class="px-5 py-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <button class="rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-dark transition-colors" id="addButton" type="button">
                        Add Job
                    </button>
                </div>
                <div>
                    <?php if($this->session->flashdata('message')): ?>
                        <?= $this->session->flashdata('message'); ?>
                    <?php endif; ?>
                </div>
            </div>

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

<!-- Add/Edit Job Modal -->
<div id="modal" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modalAddLabel">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-4xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">

            <!-- Header -->
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800" id="modalAddLabel"></h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-4 overflow-y-auto max-h-[70vh]">
                <form id="formAddUser" method="post">
                    <input type="hidden" id="job_id" name="job_id" class="form-control">
                    <input type="hidden" class="form-control" id="customer_id" name="customer_id">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6">
                        <!-- Left Column: Job Info + Customer Fields -->
                        <div>
                            <h5 class="text-base font-semibold text-gray-800">Job Information</h5>
                            <hr class="my-3 border-gray-200">
                            <div class="form-group mb-4">
                                <label for="job_name" class="block text-sm font-medium text-gray-700 mb-1">Job Name</label>
                                <input type="text" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="job_name" name="job_name"
                                    placeholder="Enter Job Name">
                                <span class="inline-error" id="error-job_name"></span>
                            </div>
                            <div class="form-group mb-4">
                                <label for="type_job" class="block text-sm font-medium text-gray-700 mb-1">Type Job</label>
                                <select class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm select2bs4" name="type_job" id="type_job" disabled required>
                                    <option value="">--- Select Type Job ---</option>
                                    <option value="1">Line Interrupt</option>
                                    <option value="2">Reconnection</option>
                                    <option value="3">Short Circuit</option>
                                    <option value="4">Disconnection</option>
                                </select>
                                <input type="hidden" name="type_job_input" id="type_job_input">
                                <span class="inline-error" id="error-type_job"></span>
                            </div>
                            <div class="form-group mb-4">
                                <label for="job_date" class="block text-sm font-medium text-gray-700 mb-1">Date Job</label>
                                <input type="date" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="job_date" name="job_date" value="<?= date('Y-m-d') ?>"
                                    placeholder="Enter Job Date">
                                <span class="inline-error" id="error-job_date"></span>
                            </div>

                            <h5 class="text-base font-semibold text-gray-800 mt-2">Customer Information</h5>
                            <hr class="my-3 border-gray-200">
                            <div class="form-group mb-4">
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                                <input type="text" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="customer_name" name="customer_name"
                                    placeholder="Enter Customer Name">
                                <span class="inline-error" id="error-customer_name"></span>
                            </div>
                            <div class="form-group mb-4">
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Customer Email</label>
                                <input type="email" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="customer_email" name="customer_email"
                                    placeholder="Enter Customer Email">
                                <span class="inline-error" id="error-customer_email"></span>
                            </div>
                            <div class="form-group mb-4">
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <div class="flex">
                                    <span class="input-group-text-tw inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-sm text-gray-600">+63</span>
                                    <input type="text" class="tw-input block w-full rounded-r-lg rounded-l-none border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="phone_number" name="phone_number"
                                        placeholder="Enter Phone Number">
                                </div>
                                <span class="inline-error" id="error-phone_number"></span>
                            </div>
                        </div>

                        <!-- Right Column: Location -->
                        <div>
                            <h5 class="text-base font-semibold text-gray-800">Location</h5>
                            <hr class="my-3 border-gray-200">
                            <div class="form-group mb-4">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea name="address" id="address" rows="3" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors"></textarea>
                                <span class="inline-error" id="error-address"></span>
                            </div>
                            <div class="form-group mb-4">
                                <label for="map" class="block text-sm font-medium text-gray-700 mb-1">Select Location on Map</label>
                                <div id="map" style="width: 100%; height: 280px; border-radius: 8px; border: 1px solid #ccc;"></div>
                                <input type="hidden" id="latitude" name="latitude">
                                <input type="hidden" id="longitude" name="longitude">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="flex justify-end items-center gap-2 py-3 px-4 border-t border-gray-200">
                <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" data-hs-overlay="#modal">Close</button>
                <button type="submit" form="formAddUser" class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Job Modal -->
<div id="modal_delete" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modalDeleteLabel">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">

            <!-- Header -->
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800" id="modalDeleteLabel"></h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal_delete">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-4 overflow-y-auto">
                <form id="formDelete" method="post">
                    <input type="hidden" id="job_id" name="job_id">
                    <input type="hidden" id="delete_type_job" name="type_job" value="<?= $type_job ?>">
                    <span>Do You Sure To delete Job Name : <strong id="job_name"></strong></span>
                </form>
            </div>

            <!-- Footer -->
            <div class="flex justify-end items-center gap-2 py-3 px-4 border-t border-gray-200">
                <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" data-hs-overlay="#modal_delete">Close</button>
                <button type="submit" form="formDelete" class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Save</button>
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
                        <div class="row-item view_reschedule hidden">
                            <div class="label">Created:</div>
                            <div class="value" id="view_assign_date"></div>
                        </div>
                        <div class="row-item view_reschedule hidden">
                            <div class="label">Rescheduled:</div>
                            <div class="value" id="view_reschedule_date"></div>
                        </div>
                    </div>
                    <!-- Rider Notes -->
                    <div class="mt-3 bg-gray-50 rounded-lg border border-gray-200 p-3">
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Rider Notes</div>
                        <div id="view_notes" class="text-sm text-gray-700 whitespace-pre-line" style="min-height: 24px;"></div>
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

<!-- ===================== ASSIGN JOB MODAL ===================== -->
<div id="modal_assign" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Assign Job</h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal_assign">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 overflow-y-auto">
                <input type="hidden" id="assign_job_id">
                <p class="text-sm text-gray-600 mb-3">Assign <strong id="assign_job_name"></strong> to a driver:</p>
                <div class="form-group mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Driver</label>
                    <select id="assign_driver_select" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" style="width:100%">
                        <option value="">-- Select Driver --</option>
                    </select>
                    <span class="inline-error" id="error-assign_driver_select"></span>
                </div>
            </div>
            <div class="flex justify-end items-center gap-2 py-3 px-4 border-t border-gray-200">
                <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" data-hs-overlay="#modal_assign">Close</button>
                <button type="button" id="btn_confirm_assign" class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Assign</button>
            </div>
        </div>
    </div>
</div>

<!-- ===================== REASSIGN JOB MODAL ===================== -->
<div id="modal_reassign" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Reassign Job</h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal_reassign">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 overflow-y-auto">
                <input type="hidden" id="reassign_job_id">
                <p class="text-sm text-gray-600 mb-3">Reassign <strong id="reassign_job_name"></strong> to a new driver:</p>
                <div class="form-group mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select New Driver</label>
                    <select id="reassign_driver_select" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" style="width:100%">
                        <option value="">-- Select Driver --</option>
                    </select>
                    <span class="inline-error" id="error-reassign_driver_select"></span>
                </div>
            </div>
            <div class="flex justify-end items-center gap-2 py-3 px-4 border-t border-gray-200">
                <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" data-hs-overlay="#modal_reassign">Close</button>
                <button type="button" id="btn_confirm_reassign" class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Reassign</button>
            </div>
        </div>
    </div>
</div>

<!-- ===================== CANCEL JOB MODAL ===================== -->
<div id="modal_cancel" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Cancel Job Assignment</h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal_cancel">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 overflow-y-auto">
                <input type="hidden" id="cancel_job_id">
                <p class="text-sm text-gray-600 mb-3">Cancel assignment for <strong id="cancel_job_name"></strong>? The job will return to the unassigned pool.</p>
                <div class="form-group mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for cancellation</label>
                    <textarea id="cancel_reason" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" rows="4" placeholder="Enter reason..."></textarea>
                    <span class="inline-error" id="error-cancel_reason"></span>
                </div>
            </div>
            <div class="flex justify-end items-center gap-2 py-3 px-4 border-t border-gray-200">
                <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" data-hs-overlay="#modal_cancel">Close</button>
                <button type="button" id="btn_confirm_cancel" class="rounded-lg bg-destructive px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">Cancel Job</button>
            </div>
        </div>
    </div>
</div>

<!-- ===================== ADMIN RESCHEDULE MODAL ===================== -->
<div id="modal_admin_reschedule" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Reschedule Job</h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal_admin_reschedule">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 overflow-y-auto">
                <input type="hidden" id="reschedule_job_id">
                <p class="text-sm text-gray-600 mb-3">Reschedule <strong id="reschedule_job_name"></strong>:</p>
                <div class="form-group mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Date</label>
                    <input type="date" id="reschedule_new_date" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <span class="inline-error" id="error-reschedule_new_date"></span>
                </div>
                <div class="form-group mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                    <textarea id="reschedule_reason" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" rows="4" placeholder="Enter reason..."></textarea>
                    <span class="inline-error" id="error-reschedule_reason"></span>
                </div>
            </div>
            <div class="flex justify-end items-center gap-2 py-3 px-4 border-t border-gray-200">
                <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" data-hs-overlay="#modal_admin_reschedule">Close</button>
                <button type="button" id="btn_confirm_reschedule" class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">Reschedule</button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
function showMaps(defaultLat, defaultLng) {
    if (window.currentMap) {
        window.currentMap.remove();
        window.currentMap = null;
    }

    var map = L.map('map').setView([defaultLat, defaultLng], 13);
    window.currentMap = map;

    var marker = L.marker([defaultLat, defaultLng]).addTo(map);

    $('#latitude').val(defaultLat);
    $('#longitude').val(defaultLng);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        if (marker) {
            map.removeLayer(marker);
        }

        marker = L.marker([lat, lng]).addTo(map);

        $('#latitude').val(lat);
        $('#longitude').val(lng);
    });

    var geocoder = L.Control.geocoder({
        geocoder: new L.Control.Geocoder.Nominatim({
            geocodingQueryParams: {
                countrycodes: 'ph',
                limit: 5,
                addressdetails: 1
            }
        }),
        placeholder: 'Search address',
        defaultMarkGeocode: false
    })
    .on('markgeocode', function(e) {
        var latlng = e.geocode.center;

        if (marker) {
            map.removeLayer(marker);
        }

        marker = L.marker(latlng).addTo(map);
        map.setView(latlng, 16);

        $('#latitude').val(latlng.lat);
        $('#longitude').val(latlng.lng);
    })
    .addTo(map);

    // Fix map rendering when Preline overlay opens
    var modalEl = document.querySelector('#modal');
    if (modalEl) {
        modalEl.addEventListener('open.hs.overlay', function () {
            setTimeout(function() {
                map.invalidateSize();
            }, 400);
        });
    }
}
</script>

<script>

var type_job = $('#type_for_job').val();

function refreshCard() {
    $.ajax({
        url: '<?= base_url('Job/getDataJobForCard/') ?>' + type_job,
        dataType: 'json',
        success: function(resp) {
            $('#today_job_count').text(resp.todayJob);
            $('#ongoing_job_count').text(resp.ongoingJob);
            $('#upcoming_job_count').text(resp.onComingJob);
            $('#reschedule_job_count').text(resp.rescheduleJob);
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
    var result = dayName + ', ' + day + ' ' + monthName + ' ' + year;
    if (date.getHours() !== 0 || date.getMinutes() !== 0) {
        const hours = date.getHours();
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const h12 = hours % 12 || 12;
        const time = String(h12).padStart(2, '0') + ':' + minutes + ' ' + ampm;
        result += ' - ' + time;
    }
    return result;
}

$(document).ready(function() {

    refreshCard();

    let buttonAdd = $('#addButton');
    let modal = $('#modal');
    let textHeaderModal = $("#modalAddLabel");
    let formUser = $("#formAddUser");

    $('#phone_number').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // handle button add
    buttonAdd.on('click', function(e) {
        e.preventDefault();
        showModal('#modal');
        clearAllFieldErrors('#modal');

        textHeaderModal.text('Add Job');
        formUser.attr("action", '<?= base_url('create-job') ?>')

        modal.find('#job_id').val('');
        modal.find('#job_name').val('');
        const now = new Date();
        const today = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
        modal.find('#job_date').val(today);

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
        modal.find('#type_job_input').val(value_selected);

        var defaultLat = 14.5995;
        var defaultLng = 120.9842;

        showMaps(defaultLat, defaultLng)
    });

    // Silent reload flag: suppresses the loading overlay during background auto-refresh
    var silentReload = false;
    // Silent search flag: suppresses the loading overlay during search typing
    var silentSearch = false;

    // Suppress default DataTables alert for JSON errors; handle via ajax.error instead
    $.fn.dataTable.ext.errMode = 'none';

    var table = $('#tableJobRider').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: "<?= base_url('Job/getDataAllJob/') ?>" + type_job,
            type: "GET",
            error: function(xhr, error, thrown) {
                // Session expired → server returned HTML redirect instead of JSON
                if (xhr.status === 200 || xhr.status === 307 || xhr.status === 302) {
                    try { JSON.parse(xhr.responseText); } catch (e) {
                        window.location.href = '<?= base_url('auth') ?>';
                        return;
                    }
                }
                if (!silentReload) {
                    console.error('DataTable AJAX error:', error, thrown);
                }
            },
        },
        columns: [
            { data: "no", className: "text-center" },
            {
                data: "JobDate",
                className: "whitespace-nowrap",
                render: function(data) {
                    if (!data) return '-';
                    var d = moment(data);
                    var result = d.format('DD MMM YYYY');
                    if (d.hours() !== 0 || d.minutes() !== 0) {
                        result += '<br><span class="text-xs text-gray-400">' + d.format('hh:mm A') + '</span>';
                    }
                    return result;
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
                    var btns = '<div class="inline-flex items-center justify-center gap-1.5">';
                    btns += '<button data-jobid="' + data + '" type="button" class="btn-tw-success buttonView" title="View Job"><i class="fas fa-fw fa-eye"></i></button>';

                    // Assign — only for unassigned jobs
                    if (row.Status == null) {
                        btns += '<button data-jobid="' + data + '" data-job-name="' + row.JobName + '" type="button" class="btn-tw-primary buttonAssign" title="Assign Driver"><i class="fas fa-fw fa-user-plus"></i></button>';
                    }

                    // Reassign — only for assigned/ongoing jobs
                    if (row.Status == 1 || row.Status == 3) {
                        btns += '<button data-jobid="' + data + '" data-job-name="' + row.JobName + '" data-current-driver="' + (row.UserID || '') + '" type="button" class="btn-tw-info buttonReassign" title="Reassign Driver"><i class="fas fa-fw fa-user-tag"></i></button>';
                    }

                    // Cancel assignment — only for assigned/ongoing jobs
                    if (row.Status == 1 || row.Status == 3) {
                        btns += '<button data-jobid="' + data + '" data-job-name="' + row.JobName + '" type="button" class="btn-tw-danger buttonCancel" title="Cancel Assignment"><i class="fas fa-fw fa-ban"></i></button>';
                    }

                    // Admin reschedule — any non-finished job
                    if (row.Status == null || row.Status == 1 || row.Status == 3) {
                        btns += '<button data-jobid="' + data + '" data-job-name="' + row.JobName + '" type="button" class="btn-tw-warning buttonAdminReschedule" title="Reschedule Job"><i class="fas fa-fw fa-calendar-alt"></i></button>';
                    }

                    // Edit/Delete — only for unassigned
                    if (row.Status == null) {
                        btns += '<button data-jobid="' + data + '" type="button" class="btn-tw-warning buttonEdit" title="Edit Job"><i class="fas fa-fw fa-edit"></i></button>';
                        btns += '<button type="button" data-jobid="' + data + '" data-job-name="' + row.JobName + '" class="btn-tw-danger buttonDelete" title="Delete Job"><i class="fas fa-fw fa-trash"></i></button>';
                    }

                    btns += '</div>';
                    return btns;
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
    // NOTE: We listen on the actual search input (not search.dt) because
    // DataTables fires search.dt internally during sort/redraw, which would
    // incorrectly set silentSearch=true and prevent the overlay from hiding.
    $('#tableJobRider_wrapper').on('input', '.dataTables_filter input', function() {
        silentSearch = true;
    });

    // Skip the auto-refresh when a user-initiated request (e.g. changing page
    // length) is already in flight, so the silentReload flag won't swallow
    // the processing=false event that hides the overlay.
    setInterval(function() {
        if (isProcessing) return;
        silentReload = true;
        table.ajax.reload(null, false);
        refreshCard();
    }, 5000);

    // ===================== FEATURE 1: ASSIGN JOB =====================

    $(document).on('click', '.buttonAssign', function(e) {
        e.preventDefault();
        var jobID = $(this).data('jobid');
        var jobName = $(this).data('job-name');

        $('#assign_job_id').val(jobID);
        $('#assign_job_name').text(jobName);
        $('#assign_driver_select').empty().append('<option value="">-- Loading... --</option>');
        clearAllFieldErrors('#modal_assign');

        showModal('#modal_assign');

        $.ajax({
            url: '<?= base_url("Job/getDriversByCompany") ?>',
            method: 'GET',
            data: { jobID: jobID },
            dataType: 'json',
            success: function(drivers) {
                var $select = $('#assign_driver_select');
                $select.empty().append('<option value="">-- Select Driver --</option>');
                $.each(drivers, function(i, d) {
                    $select.append('<option value="' + d.UserID + '">' + d.Fullname + '</option>');
                });
            }
        });
    });

    $('#btn_confirm_assign').on('click', function() {
        var jobID = $('#assign_job_id').val();
        var driverID = $('#assign_driver_select').val();

        clearAllFieldErrors('#modal_assign');

        if (!driverID) {
            setFieldError('assign_driver_select', 'Please select a driver.');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('Assigning...');

        $.ajax({
            url: '<?= base_url("Job/assignJob") ?>',
            method: 'POST',
            data: { jobID: jobID, driverID: driverID },
            dataType: 'json',
            success: function(resp) {
                if (resp.success) {
                    hideModal('#modal_assign');
                    Swal.fire({ icon: 'success', title: 'Success', text: resp.message, timer: 2000, showConfirmButton: false });
                    silentReload = true;
                    table.ajax.reload(null, false);
                    refreshCard();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.' });
            },
            complete: function() {
                $btn.prop('disabled', false).text('Assign');
            }
        });
    });

    // ===================== FEATURE 2: REASSIGN JOB =====================

    $(document).on('click', '.buttonReassign', function(e) {
        e.preventDefault();
        var jobID = $(this).data('jobid');
        var jobName = $(this).data('job-name');
        var currentDriver = $(this).data('current-driver');

        $('#reassign_job_id').val(jobID);
        $('#reassign_job_name').text(jobName);
        $('#reassign_driver_select').empty().append('<option value="">-- Loading... --</option>');
        clearAllFieldErrors('#modal_reassign');

        showModal('#modal_reassign');

        $.ajax({
            url: '<?= base_url("Job/getDriversByCompany") ?>',
            method: 'GET',
            data: { jobID: jobID, excludeUserID: currentDriver },
            dataType: 'json',
            success: function(drivers) {
                var $select = $('#reassign_driver_select');
                $select.empty().append('<option value="">-- Select New Driver --</option>');
                $.each(drivers, function(i, d) {
                    $select.append('<option value="' + d.UserID + '">' + d.Fullname + '</option>');
                });
            }
        });
    });

    $('#btn_confirm_reassign').on('click', function() {
        var jobID = $('#reassign_job_id').val();
        var driverID = $('#reassign_driver_select').val();

        clearAllFieldErrors('#modal_reassign');

        if (!driverID) {
            setFieldError('reassign_driver_select', 'Please select a new driver.');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('Reassigning...');

        $.ajax({
            url: '<?= base_url("Job/reassignJob") ?>',
            method: 'POST',
            data: { jobID: jobID, driverID: driverID },
            dataType: 'json',
            success: function(resp) {
                if (resp.success) {
                    hideModal('#modal_reassign');
                    Swal.fire({ icon: 'success', title: 'Success', text: resp.message, timer: 2000, showConfirmButton: false });
                    silentReload = true;
                    table.ajax.reload(null, false);
                    refreshCard();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.' });
            },
            complete: function() {
                $btn.prop('disabled', false).text('Reassign');
            }
        });
    });

    // ===================== FEATURE 3: CANCEL JOB =====================

    $(document).on('click', '.buttonCancel', function(e) {
        e.preventDefault();
        var jobID = $(this).data('jobid');
        var jobName = $(this).data('job-name');

        $('#cancel_job_id').val(jobID);
        $('#cancel_job_name').text(jobName);
        $('#cancel_reason').val('');
        clearAllFieldErrors('#modal_cancel');

        showModal('#modal_cancel');
    });

    $('#btn_confirm_cancel').on('click', function() {
        var jobID = $('#cancel_job_id').val();
        var reason = $('#cancel_reason').val().trim();

        clearAllFieldErrors('#modal_cancel');

        if (!reason) {
            setFieldError('cancel_reason', 'Please provide a reason for cancellation.');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('Cancelling...');

        $.ajax({
            url: '<?= base_url("Job/cancelJob") ?>',
            method: 'POST',
            data: { jobID: jobID, reason: reason },
            dataType: 'json',
            success: function(resp) {
                if (resp.success) {
                    hideModal('#modal_cancel');
                    Swal.fire({ icon: 'success', title: 'Success', text: resp.message, timer: 2000, showConfirmButton: false });
                    silentReload = true;
                    table.ajax.reload(null, false);
                    refreshCard();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.' });
            },
            complete: function() {
                $btn.prop('disabled', false).text('Cancel Job');
            }
        });
    });

    // ===================== FEATURE 4: ADMIN RESCHEDULE =====================

    $(document).on('click', '.buttonAdminReschedule', function(e) {
        e.preventDefault();
        var jobID = $(this).data('jobid');
        var jobName = $(this).data('job-name');

        $('#reschedule_job_id').val(jobID);
        $('#reschedule_job_name').text(jobName);
        $('#reschedule_new_date').val('');
        $('#reschedule_reason').val('');
        clearAllFieldErrors('#modal_admin_reschedule');

        var now = new Date();
        var today = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
        $('#reschedule_new_date').attr('min', today);

        showModal('#modal_admin_reschedule');
    });

    $('#btn_confirm_reschedule').on('click', function() {
        var jobID = $('#reschedule_job_id').val();
        var newDate = $('#reschedule_new_date').val();
        var reason = $('#reschedule_reason').val().trim();

        clearAllFieldErrors('#modal_admin_reschedule');
        var hasError = false;

        if (!newDate) {
            setFieldError('reschedule_new_date', 'Please select a new date.');
            hasError = true;
        }
        if (!reason) {
            setFieldError('reschedule_reason', 'Please provide a reason.');
            hasError = true;
        }
        if (hasError) return;

        var $btn = $(this);
        $btn.prop('disabled', true).text('Rescheduling...');

        $.ajax({
            url: '<?= base_url("Job/adminRescheduleJob") ?>',
            method: 'POST',
            data: { jobID: jobID, newDate: newDate, reason: reason },
            dataType: 'json',
            success: function(resp) {
                if (resp.success) {
                    hideModal('#modal_admin_reschedule');
                    Swal.fire({ icon: 'success', title: 'Success', text: resp.message, timer: 2000, showConfirmButton: false });
                    silentReload = true;
                    table.ajax.reload(null, false);
                    refreshCard();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.' });
            },
            complete: function() {
                $btn.prop('disabled', false).text('Reschedule');
            }
        });
    });

});

// handle button edit job
$(document).on('click', '.buttonEdit', function(e) {

    e.preventDefault();
    showModal('#modal');
    clearAllFieldErrors('#modal');

    let jobID = $(this).data('jobid');
    $('#modalAddLabel').text('Edit Job');
    $("#formAddUser").attr("action", '<?= base_url('edit-job') ?>');

    $.ajax({
        url: '<?= base_url('Job/getJobDetail') ?>',
        method: 'post',
        data: {
            jobID: jobID
        },
        dataType: 'json',
        success: function(response) {
            $('#modal').find('#job_id').val(response.JobID);
            $('#modal').find('#job_name').val(response.JobName);

            let rawPhone = response.CustPhoneNumber || "";
            let phoneNumber = rawPhone.replace(/^\+63/, '');
            let formatPhoneNumber = parseInt(phoneNumber) || "";

            let jobDate = response.JobDate;
            let formattedDate = jobDate.split(' ')[0];
            $('#modal').find('#job_date').val(formattedDate);

            $('#modal').find('#customer_id').val(response.CustomerID);
            $('#modal').find('#customer_name').val(response.CustomerName);
            $('#modal').find('#customer_email').val(response.CustomerEmail);
            $('#modal').find('#phone_number').val(formatPhoneNumber);
            $('#modal').find('#address').val(response.Address);
            $('#modal').find('#latitude').val(response.Latitude);
            $('#modal').find('#longitude').val(response.Longitude);
            $('#modal').find('#type_job').val(response.TypeJob).trigger('change');
            $('#modal').find('#type_job_input').val(response.TypeJob);
            showMaps(response.Latitude, response.Longitude)
        },
    })

})

// handle button delete job
$(document).on('click', '.buttonDelete', function(e) {

    e.preventDefault();
    showModal('#modal_delete');
    $("#modalDeleteLabel").text('Delete Job')
    $("#formDelete").attr("action", '<?= base_url('delete-job') ?>');

    let jobID = $(this).data('jobid');
    let jobName = $(this).data('job-name');
    $('#modal_delete').find('#job_id').val(jobID);
    $('#modal_delete').find('#job_name').text(jobName);
})

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

            // Reschedule info
            if (response.Status == 3) {
                $('.view_reschedule').removeClass('hidden');
                $('#view_assign_date').text(returnDateFormatDetailJS(response.AssignWhen));
                $('#view_reschedule_date').text(returnDateFormatDetailJS(response.RescheduledDateJob));
            } else {
                $('.view_reschedule').addClass('hidden');
            }

            // Rider Notes
            $('#view_notes').text(response.Notes || '-');
            if (response.Notes) {
                $('#view_notes').removeClass('italic text-gray-400').addClass('text-gray-700');
            } else {
                $('#view_notes').removeClass('text-gray-700').addClass('italic text-gray-400');
            }

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

// Form validation for Add/Edit Job
$('#formAddUser').on('submit', function(e) {
    e.preventDefault();

    var hasError = false;
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    var jobName       = $('#job_name').val().trim();
    var typeJob       = $('#type_job_input').val();
    var jobDate       = $('#job_date').val().trim();
    var customerName  = $('#customer_name').val().trim();
    var customerEmail = $('#customer_email').val().trim();
    var phoneRaw      = $('#phone_number').val().trim();
    var address       = $('#address').val().trim();

    clearAllFieldErrors('#modal');

    if (!jobName) {
        setFieldError('job_name', 'Job name is required.');
        hasError = true;
    }

    if (!typeJob) {
        setFieldError('type_job', 'Job type is required.');
        hasError = true;
    }

    if (!jobDate) {
        setFieldError('job_date', 'Job date is required.');
        hasError = true;
    }

    if (!customerName) {
        setFieldError('customer_name', 'Customer name is required.');
        hasError = true;
    }

    if (!customerEmail) {
        setFieldError('customer_email', 'Email is required.');
        hasError = true;
    } else if (!emailRegex.test(customerEmail)) {
        setFieldError('customer_email', 'Please enter a valid email address.');
        hasError = true;
    }

    var digits = phoneRaw.replace(/\D/g, '');

    if (!phoneRaw) {
        setFieldError('phone_number', 'Phone number is required.');
        hasError = true;
    } else if (
        !(
            (digits.length === 11 && digits.startsWith('09')) ||
            (digits.length === 10 && digits.startsWith('9')) ||
            (digits.length === 12 && digits.startsWith('63'))
        )
    ) {
        setFieldError('phone_number', 'Please enter a valid mobile number.');
        hasError = true;
    }

    if (!address) {
        setFieldError('address', 'Address is required.');
        hasError = true;
    }

    if (hasError) return;
    this.submit();
});

</script>
