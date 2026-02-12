

<!-- Content Header -->
<div class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-xl font-bold text-gray-800">Customer</h1>
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
            <div class="flex flex-wrap items-center justify-between gap-2.5">
                <div class="flex items-center gap-2.5">
                    <form id="import_excel_form" enctype="multipart/form-data" class="flex">
                        <label for="import_excel" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer mb-0!">
                            <i class="fa fa-file-excel text-green-600"></i> Import
                        </label>
                        <input type="file" id="import_excel" name="import_excel" accept=".xls,.xlsx" hidden>
                    </form>

                    <a href="<?= base_url('assets/dist/Example Excel Upload Customer.xlsx') ?>"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" download>
                        <i class="fa fa-download text-primary"></i> Template
                    </a>
                </div>

                <button class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-dark transition-colors" id="addButton" type="button">
                    <i class="fa fa-plus"></i> Add Customer
                </button>
            </div>
            <div class="flex items-center justify-between mt-3">
                <?php if($this->session->flashdata('message')): ?>
                <?= $this->session->flashdata('message'); ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Table -->
        <div class="px-5 py-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm" data-paginated-table data-per-page="10" data-searchable>
                    <thead class="bg-gray-50">
                        <tr class="text-center whitespace-nowrap">
                            <th class="w-[10%] px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Company</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Customer Name</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Customer Email</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Account Number</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Phone Number</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Address</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Maps</th>
                            <th class="w-[15%] px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($customer as $val) : ?>
                        <tr class="text-center border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 align-middle"><?= $no++ ?></td>
                            <td class="px-4 py-3 align-middle whitespace-nowrap"><?= $val['CompanyName'] ?></td>
                            <td class="px-4 py-3 align-middle whitespace-nowrap"><?= $val['CustomerName'] ?></td>
                            <td class="px-4 py-3 align-middle whitespace-nowrap"><?= $val['CustomerEmail'] ?></td>
                            <td class="px-4 py-3 align-middle whitespace-nowrap"><?= $val['AccountNumber'] ?></td>
                            <td class="px-4 py-3 align-middle whitespace-nowrap"><?= $val['PhoneNumber'] ?></td>
                            <td class="px-4 py-3 align-middle whitespace-nowrap"><?= $val['Address'] ?></td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="https://www.google.com/maps?q=<?= $val['Latitude'] ?>,<?= $val['Longitude'] ?>"
                                target="_blank"
                                class="btn-tw-info">
                                    Open In Maps
                                </a>
                            </td>
                            <td class="px-4 py-3 align-middle whitespace-nowrap">
                                <button data-customer-id="<?= $val['CustomerID'] ?>" type="button"
                                    class="btn-tw-warning buttonEdit">
                                    <i class="fas fa-edit"></i>
                                </button> |
                                <button type="button" data-customer-id="<?= $val['CustomerID'] ?>"
                                    data-customer-name="<?= $val['CustomerName'] ?>"
                                    class="btn-tw-danger buttonDelete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3" data-pagination-controls></div>
        </div>
    </div>
</div>

<!-- Add/Edit Customer Modal -->
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
                    <div class="form-group mb-4 <?= ($this->session->userdata('Role') != 1) ? 'hidden' : '' ?>">
                        <label for="company_selected" class="block text-sm font-medium text-gray-700 mb-1">Select Company</label>
                         <select name="company_selected" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm select2For_modal" id="company_selected" required>
                            <option value="">---- Select Company ----</option>
                            <?php foreach($list_company as $val): ?>
                                <option value="<?= $val['ListCompanyID'] ?>" <?= ($this->session->userdata('CompanyID') == $val['ListCompanyID']) ? "selected" : "" ?> <?= ($this->session->userdata('Role') != 1) ?  "disabled" : "" ?> ><?= $val['CompanyName'] ?></option>
                            <?php endforeach; ?>
                         </select>
                        <span class="inline-error" id="error-company_selected"></span>
                    </div>
                    <div class="form-group mb-4">
                        <input type="hidden" id="customer_id" name="customer_id" class="form-control">
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                        <input type="text" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="customer_name" name="customer_name"
                            placeholder="Enter Customer Name" required>
                        <span class="inline-error" id="error-customer_name"></span>
                    </div>

                    <div class="form-group mb-4">
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Customer Email</label>
                        <input type="email" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="customer_email" name="customer_email"
                            placeholder="Enter Customer Email" required>
                        <span class="inline-error" id="error-customer_email"></span>
                    </div>

                    <div class="form-group mb-4">
                        <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                        <input type="text" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="account_number" name="account_number"
                            placeholder="Enter Account Number" required>
                        <span class="inline-error" id="error-account_number"></span>
                    </div>

                    <div class="form-group mb-4">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <div class="flex">
                            <span class="input-group-text-tw inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-sm text-gray-600">+63</span>
                            <input type="text" class="tw-input block w-full rounded-r-lg rounded-l-none border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="phone_number" name="phone_number"
                                placeholder="Enter Phone Number" required>
                        </div>
                        <span class="inline-error" id="error-phone_number"></span>
                    </div>
                    <div class="form-group mb-4">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="address" id="address" rows="5" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" required></textarea>
                        <span class="inline-error" id="error-address"></span>
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

<!-- Delete Customer Modal -->
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
                    <input type="hidden" id="customer_id" name="customer_id">
                    <span>Do You Sure To delete Customer Name : <strong id="customer_name"></strong></span>
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



$(document).ready(function() {

    let buttonAdd = $('#addButton');
    let buttonEdit = $('.buttonEdit');
    let buttonDelete = $('.buttonDelete');
    let modal = $('#modal');
    let modalDelete = $('#modal_delete');
    let textHeaderModal = $("#modalAddLabel");
    let textHeaderModalDelete = $("#modalDeleteLabel");
    let formUser = $("#formAddUser");
    let formUserDelete = $("#formDelete");

    $('#phone_number').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // handle button add
    buttonAdd.on('click', function(e) {
        e.preventDefault();
        showModal('#modal');
        clearAllFieldErrors('#modal');

        textHeaderModal.text('Add Customer');
        modal.find('#customer_id').val('');
        modal.find('#customer_name').val('');
        modal.find('#customer_email').val('');
        modal.find('#account_number').val('');
        modal.find('#phone_number').val('');
        modal.find('#address').val('');
        formUser.attr("action", '<?= base_url('create-customer') ?>')

        var defaultLat = 14.5995;
        var defaultLng = 120.9842;

        showMaps(defaultLat, defaultLng)
    });



    // handle button edit
    buttonEdit.on('click', function(e) {
        e.preventDefault();
        showModal('#modal');
        clearAllFieldErrors('#modal');

        let customerID = $(this).data('customer-id');
        textHeaderModal.text('Edit Customer');
        formUser.attr("action", '<?= base_url('edit-customer') ?>');

        $.ajax({
            url: '<?= base_url('Customer/getCustomerDetail') ?>',
            method: 'post',
            data: {
                customerID: customerID
            },
            dataType: 'json',
            success: function(response) {
                let phoneNumber = response.PhoneNumber.replace(/^\+63/, '');
                phoneNumber = parseInt(phoneNumber);

                modal.find('#customer_id').val(response.CustomerID);
                modal.find('#customer_name').val(response.CustomerName);
                modal.find('#customer_email').val(response.CustomerEmail);
                modal.find('#account_number').val(response.AccountNumber);
                modal.find('#phone_number').val(phoneNumber);
                modal.find('#address').val(response.Address);
                modal.find('#latitude').val(response.Latitude);
                modal.find('#longitude').val(response.Longitude);
                modal.find('#company_selected').val(response.ListCompanyID).trigger('change');
                showMaps(response.Latitude, response.Longitude)
            },
        })

    })


    // handle button delete
    buttonDelete.on('click', function(e) {
        e.preventDefault();
        showModal('#modal_delete');
        textHeaderModalDelete.text('Delete Customer')
        formUserDelete.attr("action", '<?= base_url('delete-customer') ?>');

        let customerID = $(this).data('customer-id');
        let customerName = $(this).data('customer-name');
        modalDelete.find('#customer_id').val(customerID);
        modalDelete.find('#customer_name').text(customerName);
    });

    // Form validation
    formUser.on('submit', function(e) {
        e.preventDefault();

        var hasError = false;
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        var company      = $('#company_selected').val();
        var customerName  = $('#customer_name').val().trim();
        var customerEmail = $('#customer_email').val().trim();
        var accountNumber = $('#account_number').val().trim();
        var phoneRaw      = $('#phone_number').val().trim();
        var address       = $('#address').val().trim();

        clearAllFieldErrors('#modal');

        if ($('#company_selected').is(':visible') && !company) {
            setFieldError('company_selected', 'Please select a company.');
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

        if (!accountNumber) {
            setFieldError('account_number', 'Account number is required.');
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

    $('#import_excel').on('change', function() {
        var file = this.files[0];

        if (!file) return;

        var allowedExtensions = /(\.xls|\.xlsx)$/i;
        if (!allowedExtensions.exec(file.name)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid file!',
                text: 'Please upload an Excel file (.xls or .xlsx)',
            });
            $(this).val('');
            return;
        }

        var formData = new FormData($('#import_excel_form')[0]);

        $.ajax({
            url: '<?= base_url("Customer/import_excel"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                Swal.fire({
                    title: 'Uploading...',
                    text: 'Please wait a moment',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                Swal.close();

                if(response.status) {

                    if(response.label == "success") {

                        Swal.fire({
                            icon: 'success',
                            title: 'Upload Successful!',
                            html: response.message,
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            location.reload();
                        });

                    } else {
                        Swal.fire({
                            icon: response.label,
                            title: 'Upload Successful!',
                            html: response.message,
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                }

                $('#import_excel').val('');
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed!',
                    text: 'Something went wrong: ' + error
                });
                $('#import_excel').val('');
            }
        });
    });


});
</script>
