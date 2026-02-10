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
        <h1 class="text-xl font-bold text-gray-800">Job <?= $label_job ?></h1>
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
                            <th>Status Job</th>
                            <th style="width: 15%; text-align: center;">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Add/Edit Job Modal -->
<div id="modal" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modalAddLabel">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
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
            <div class="p-4 overflow-y-auto">
                <form id="formAddUser" method="post">

                    <h5 class="text-base font-semibold text-gray-800">Job Information: </h5>
                    <hr class="my-3 border-gray-200">
                    <div class="form-group mb-4">
                        <input type="hidden" id="job_id" name="job_id" class="form-control">
                        <label for="job_name" class="block text-sm font-medium text-gray-700 mb-1">Job Name</label>
                        <input type="text" class="form-control w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" id="job_name" name="job_name"
                            placeholder="Enter Job Name">
                    </div>
                    <div class="form-group mb-4">
                        <label for="type_job" class="block text-sm font-medium text-gray-700 mb-1">Type Job</label>
                        <select class="form-control select2bs4 w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" name="type_job" id="type_job" disabled required>
                            <option value="">--- Select Type Job ---</option>
                            <option value="1">Line Interrupt</option>
                            <option value="2">Reconnection</option>
                            <option value="3">Short Circuit</option>
                            <option value="4">Disconnection</option>
                        </select>

                        <input type="hidden" name="type_job_input" id="type_job_input">
                    </div>
                    <div class="form-group mb-4">
                        <label for="job_date" class="block text-sm font-medium text-gray-700 mb-1">Date Job</label>
                        <input type="date" class="form-control w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" id="job_date" name="job_date" value="<?= date('Y-m-d') ?>"
                            placeholder="Enter Job Date">
                    </div>

                    <hr class="my-3 border-gray-200">
                    <h5 class="text-base font-semibold text-gray-800">Customer Information: </h5>
                    <hr class="my-3 border-gray-200">
                    <div class="form-group mb-4">
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                        <input type="hidden" class="form-control" id="customer_id" name="customer_id">
                        <input type="text" class="form-control w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" id="customer_name" name="customer_name"
                            placeholder="Enter Customer Name">
                    </div>
                    <div class="form-group mb-4">
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Customer Email</label>
                        <input type="email" class="form-control w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" id="customer_email" name="customer_email"
                            placeholder="Enter Customer Email">
                    </div>
                    <div class="form-group mb-4">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-sm text-gray-600">+63</span>
                            <input type="text" class="form-control w-full rounded-r-lg rounded-l-none border-gray-300 text-sm focus:border-primary focus:ring-primary" id="phone_number" name="phone_number"
                                placeholder="Enter Phone Number">
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="address" id="address" rows="5" class="form-control w-full rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary"></textarea>
                    </div>
                    <div class="form-group mb-4 mt-3">
                        <label for="map" class="block text-sm font-medium text-gray-700 mb-1">Select Location on Map</label>
                        <div id="map" style="width: 100%; height: 300px; border-radius: 8px; border: 1px solid #ccc;"></div>
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
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

<!-- Detail Job Modal -->
<div id="modal_detail" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modalDetailLabel">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">

            <!-- Header -->
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800" id="modalDetailLabel"></h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal_detail">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-4 overflow-y-auto">
                <div class="container_status_job ">
                    <p class="mb-0" id="to_status_detail"></p>
                </div>

               <div class="content_header mt-3">
                    <div class="row-item">
                        <div class="label">Customer:</div>
                        <div class="value" id="to_customer_detail"></div>
                    </div>
                    <div class="row-item">
                        <div class="label">Driver:</div>
                        <div class="value" id="to_driver_detail"></div>
                    </div>
                    <div class="row-item">
                        <div class="label">Type Job:</div>
                        <div class="value" id="to_type_detail"></div>
                    </div>

                    <div class="row-item job_reschedule">
                        <div class="label">Create Job :</div>
                        <div class="value" id="to_assign_date"></div>
                    </div>
                    <div class="row-item job_reschedule">
                        <div class="label">Reschedule Job :</div>
                        <div class="value" id="to_reschedule_date"></div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end items-center gap-2 py-3 px-4 border-t border-gray-200">
                <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" data-hs-overlay="#modal_detail">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Photo Job Modal -->
<div id="modal_camera" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modalCameraLabel">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-4xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">

            <!-- Header -->
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800" id="modalCameraLabel">Detail Photo Job</h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal_camera">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-4 overflow-y-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 job-gallery">

                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end items-center gap-2 py-3 px-4 border-t border-gray-200">
                <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" data-hs-overlay="#modal_camera">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- History Cancel Job Modal -->
<div id="modal_history_cancel_job" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modalHistoryCancelLabel">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">

            <!-- Header -->
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800" id="modalHistoryCancelLabel">Detail Cancel Job</h3>
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
            $('#today_job_count').text(resp.todalJob);
            $('#ongoing_job_count').text(resp.ongoingJob);
            $('#upcoming_job_count').text(resp.onComingJob);
            $('#reschedule_job_count').text(resp.rescheduleJob);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });
}


$(document).ready(function() {

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
        const today = new Date().toISOString().slice(0, 10);
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

    var table = $('#tableJobRider').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: "<?= base_url('Job/getDataAllJob/') ?>" + type_job,
            type: "GET",
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
                data: "Address",
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('white-space', 'nowrap');
                }

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
                data: "Status",
                render: function(data, type, row) {

                    let labelStatusJob;
                    if(data == 1) {

                        labelStatusJob = `<span class='ongoing_job'>Ongoing Job</span>`;

                    } else if(data ==2) {

                        labelStatusJob = `<span class='finished_job'>Finished Job</span>`;
                    }  else if(data ==3) {

                        labelStatusJob = `<span class='reschedule_job'>Reschedule Job</span>`;
                    } else {
                        labelStatusJob = `<span class='awaiting_job'>Awaiting Driver</span>`;

                    }

                    return labelStatusJob;

                }
            },
            {
                data: "JobID",
                className: "text-center",
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).css('white-space', 'nowrap');
                },
                render: function(data, type, row) {

                    let labelButtonAction;

                    if(row.Status == null) {

                        labelButtonAction = `
                        <button data-jobid="${data}" type="button"
                            class="btn-tw-warning buttonEdit" title="Edit Job">
                            <i class="fas fa-edit"></i>
                        </button> |
                        <button type="button" data-jobid="${data}"
                            data-job-name="${row.JobName}"
                            class="btn-tw-danger buttonDelete" title="Delete Job">
                            <i class="fas fa-trash"></i>
                        </button>
                        `;


                    } else if(row.Status == 1 || row.Status == 2 || row.Status == 3) {

                        if(row.StatusCancelJob.length > 0) {

                            labelButtonAction = `
                            <button class="btn-tw-danger button_history_cancel_job" data-job-id="${data}" title="History cancel Job" ><i class="fa-solid fa-clock-rotate-left"></i></button> |

                            <button data-jobid="${data}" type="button"
                                class="btn-tw-success buttonDetail" title="Detail Job">
                                <i class="fas fa-eye" ></i>
                            </button>
                            `;
                        } else {
                            labelButtonAction = `
                            <button data-jobid="${data}" type="button"
                                class="btn-tw-success buttonDetail" title="Detail Job">
                                <i class="fas fa-eye" ></i>
                            </button>
                            `
                        }

                        if(row.Status == 2) {

                            labelButtonAction += `
                             |
                            <button data-jobid="${data}" type="button"
                                class="btn-tw-info buttonCamera" title="Detail Photo">
                                <i class="fas fa-camera" ></i>
                            </button>
                            `;

                        }
                    }

                    return labelButtonAction;

                }
            },
        ],
        responsive: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        paging: true,
        autoWidth: true,
        scrollX: true,
        order: [[1, 'desc']],
    });

    setInterval(function() {
        table.ajax.reload(null, false);
        refreshCard();
    }, 5000);

});

function returnDateFormatDetailJS(value) {
    const date = new Date(value);

    const days = [
        "Sunday", "Monday", "Tuesday", "Wednesday",
        "Thursday", "Friday", "Saturday"
    ];

    const months = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    const dayName = days[date.getDay()];
    const day = String(date.getDate()).padStart(2, '0');
    const monthName = months[date.getMonth()];
    const year = date.getFullYear();

    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');

    return `${dayName}, ${day} ${monthName} ${year}`;
}

// handle button edit job
$(document).on('click', '.buttonEdit', function(e) {

    e.preventDefault();
    showModal('#modal');

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

// handle button detail
$(document).on('click', ".buttonDetail", function(e) {

    e.preventDefault();
    let jobID = $(this).data('jobid');

    showModal('#modal_detail');
    $('#modal_detail').find('#modalDetailLabel').text('Detail Job');

    $.ajax({
        url: '<?= base_url('Job/getJobDetail') ?>',
        method: 'post',
        data: {
            jobID: jobID
        },
        dataType: 'json',
        success: function(response) {

            let statusJob = response.Status;
            let typeJob = response.TypeJob;

            let labelJob, labelTypeJob, classLabelJob;

            if(statusJob == 1) {

                labelJob = 'Ongoing';
                classLabelJob = "ongoing_job";
                $('#modal_detail').find('.job_reschedule').addClass('hidden');
            } else if(statusJob == 2) {
                labelJob = 'Complete Job';
                classLabelJob = "completed_job";
                $('#modal_detail').find('.job_reschedule').addClass('hidden');
            } else if(statusJob == 3) {
                labelJob = 'Reschedule Job';
                classLabelJob = "reschedule_job";
                $('#modal_detail').find('.job_reschedule').removeClass('hidden');
            }

            if(typeJob == 1) {
                labelTypeJob = "Line Interrupt";
            } else if(typeJob == 2) {
                labelTypeJob = "Reconnection";
            } else if(typeJob == 3){
                labelTypeJob = "Short Circuit";
            }

            $('#modal_detail').find('#to_customer_detail').text(response.CustomerName);
            $('#modal_detail').find('#to_driver_detail').text(response.Fullname);
            $('#modal_detail').find('#to_status_detail').text(labelJob);
            $('#modal_detail').find('#to_type_detail').text(labelTypeJob);
            $('#modal_detail').find('#to_assign_date').text(returnDateFormatDetailJS(response.AssignWhen));
            $('#modal_detail').find('#to_reschedule_date').text(returnDateFormatDetailJS(response.RescheduledDateJob));
            $('#modal_detail').find('.container_status_job').removeClass("ongoing_job");
            $('#modal_detail').find('.container_status_job').removeClass("completed_job");
            $('#modal_detail').find('.container_status_job').addClass(classLabelJob);

        },
    })

});

// HANDLE BUTTON HISTORY CANCEL JOB
$(document).on('click', '.button_history_cancel_job', function() {

    let jobID = $(this).data('job-id');

    showModal('#modal_history_cancel_job');
    $('#modal_history_cancel_job .body_detail_history_cancel_job').load('<?= base_url('Job/historyCancelJob?jobID=') ?>' + jobID);
});

// HANDLE BUTTON DETAIL PHOTO JOB

$(document).on('click', '.buttonCamera', function(e) {

    e.preventDefault();
    let jobID = $(this).data('jobid');

    showModal('#modal_camera');

    $.ajax({
        url: '<?= base_url('Job/getDetailPhoto') ?>',
        method: 'POST',
        data: { jobID: jobID },
        dataType: 'json',
        success: function(response) {

            let galleryContainer = $('.job-gallery');
            galleryContainer.empty();

            if (response.length > 0) {
                response.forEach((item, index) => {
                    let html = `
                        <div>
                            <img src="${item.Photo}" alt="Job Photo ${index + 1}" class="rounded-lg shadow-sm">
                        </div>
                    `;
                    galleryContainer.append(html);
                });
            } else {
                galleryContainer.html(`
                    <div class="col-span-full text-center text-gray-500 py-4">
                        <i class="fa-solid fa-image-slash fa-2x mb-2"></i><br>
                        No photos available
                    </div>
                `);
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Failed to load photos.'
            });
        }
    });

});
</script>
