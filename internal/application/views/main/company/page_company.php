<style>
    table tbody tr td { vertical-align: middle !important; }
</style>

<!-- Content Header -->
<div class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-xl font-bold text-gray-800">Company</h1>
        <nav class="flex">
            <ol class="flex items-center gap-1.5 text-sm">
                <li><a href="<?= base_url('home') ?>" class="text-primary hover:underline">Home</a></li>
            </ol>
        </nav>
    </div>
</div>

<!-- Content -->
<div class="px-4 sm:px-6 lg:px-8 pb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Card Header -->
        <div class="px-5 py-4 border-b border-gray-200">
            <div class="flex items-center gap-2.5">
                <button class="inline-flex items-center gap-1.5 rounded-lg btn-gradient-primary px-3 py-1.5 text-sm font-medium" id="addButton" type="button">
                    Add Company
                </button>

                <a class="inline-flex items-center gap-1.5 rounded-lg bg-gray-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-600 transition-colors" href="<?= base_url('company/synchronize-traxroot') ?>" role="button">
                    <i class="fa-solid fa-repeat"></i> Data Synchronisation
                </a>

                <?php if($this->session->flashdata('message')): ?>
                <?= $this->session->flashdata('message'); ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card Body -->
        <div class="px-5 py-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm" data-paginated-table data-per-page="10" data-searchable>
                    <thead class="bg-gray-50">
                        <tr class="text-center whitespace-nowrap">
                            <th class="w-[10%] text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Company Logo</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Company Code</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Company Name</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Company Email</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Package</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Update Profile</th>
                            <th class="w-[15%] text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($company as $val) : ?>
                        <tr class="text-center border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3"><?= $no++ ?></td>
                            <td class="px-4 py-3">
                                <img src="<?= base_url('assets/dist/img/company_logo/' . $val['CompanyLogo']) ?>" width="50" alt="" class="mx-auto">
                            </td>
                            <td class="px-4 py-3"><?= $val['CompanyCode'] ?></td>
                            <td class="px-4 py-3"><?= $val['CompanyName'] ?></td>
                            <td class="px-4 py-3"><?= $val['CompanyEmail'] ?></td>
                            <td class="px-4 py-3"><?= ($val['CompanySubscribe'] == 1) ? "Basic" : "Pro" ?></td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?php if ($val['CompanySubscribe'] == 2): ?>
                                    <button data-company-id="<?= $val['ListCompanyID'] ?>" type="button" class="btn-tw-info buttonEditProfile">
                                        <i class="fas fa-user-cog"></i> Update Profile
                                    </button>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">No Access</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <button data-company-id="<?= $val['ListCompanyID'] ?>" type="button" class="btn-tw-warning buttonEdit">
                                    <i class="fas fa-edit"></i>
                                </button> |
                                <button type="button" data-company-id="<?= $val['ListCompanyID'] ?>"
                                    data-company-name="<?= $val['CompanyName'] ?>" data-user-login-id="<?= $val['UserLoginID'] ?>"
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

<!-- Add/Edit Company Modal (Preline) -->
<div id="modal" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto [--overlay-backdrop:static]" role="dialog">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800" id="modalAddLabel"></h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" data-hs-overlay="#modal">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-5 py-4 overflow-y-auto max-h-[70vh]">
                <form id="formAddUser" method="post" enctype="multipart/form-data">
                    <input type="hidden" id="company_id_add" name="company_id">
                    <input type="hidden" id="user_login_id_add" name="user_login_id">

                    <div class="form-group mb-4">
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                        <input type="text" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="company_name" name="company_name" placeholder="Enter Company Name">
                        <span class="inline-error" id="error-company_name"></span>
                    </div>

                    <div class="form-group mb-4">
                        <label for="company_phone" class="block text-sm font-medium text-gray-700 mb-1">Company Phone</label>
                        <div class="flex">
                            <span class="input-group-text-tw inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">+63</span>
                            <input type="text" class="tw-input block w-full rounded-r-lg rounded-l-none border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="company_phone" name="company_phone" placeholder="Enter Phone Number">
                        </div>
                        <span class="inline-error" id="error-company_phone"></span>
                    </div>

                    <div class="form-group mb-4">
                        <label for="company_email" class="block text-sm font-medium text-gray-700 mb-1">Company Email</label>
                        <input type="email" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="company_email" name="company_email" placeholder="Enter Company Email">
                        <span class="inline-error" id="error-company_email"></span>
                    </div>

                    <div class="form-group mb-4">
                        <label for="pass" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="pass" name="pass" placeholder="Enter Password">
                    </div>

                    <div class="form-group mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Package</label>
                        <div class="flex gap-4">
                            <div class="inline-flex items-center gap-2">
                                <input type="radio" name="package" id="basic" value="1" class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
                                <label for="basic" class="text-sm text-gray-700">Basic</label>
                            </div>
                            <div class="inline-flex items-center gap-2">
                                <input type="radio" name="package" id="pro" value="2" class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
                                <label for="pro" class="text-sm text-gray-700">Pro</label>
                            </div>
                        </div>
                        <span class="inline-error" id="error-package"></span>
                    </div>

                    <div class="form-group mb-4">
                        <label for="company_logo" class="block text-sm font-medium text-gray-700 mb-1">Company Logo</label>
                        <div class="mb-2">
                            <img id="preview_logo" src="https://tse2.mm.bing.net/th/id/OIP.IZWJ479vW3ZlLf2HS18k6wHaEa?pid=Api&P=0&h=180" alt="Preview Logo" class="max-w-[150px] rounded-lg border border-gray-300 p-1">
                        </div>
                        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-light" id="company_logo" name="company_logo" accept="image/*">
                        <span class="inline-error" id="error-company_logo"></span>
                    </div>
                </form>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-200">
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-600 transition-colors" data-hs-overlay="#modal">Close</button>
                <button type="submit" form="formAddUser" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Company Modal (Preline) -->
<div id="modal_delete" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto [--overlay-backdrop:static]" role="dialog">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800" id="modalDeleteLabel"></h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" data-hs-overlay="#modal_delete">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-5 py-4">
                <form id="formDelete" method="post" action="<?= base_url('company/delete') ?>">
                    <input type="hidden" id="company_id_delete" name="company_id">
                    <input type="hidden" id="user_login_id_delete" name="user_login_id">
                    <span class="text-sm">Do You Sure To delete Company Name : <strong id="company_name"></strong></span>
                </form>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-200">
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-600 transition-colors" data-hs-overlay="#modal_delete">Close</button>
                <button type="submit" form="formDelete" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Profile Modal (Preline) -->
<div id="modal_update_profile" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto [--overlay-backdrop:static]" role="dialog">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Update Profile</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" data-hs-overlay="#modal_update_profile">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-5 py-4">
                <form id="formUpdateProfile" method="post">
                    <input type="hidden" id="company_id_profile" name="company_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="username_traxroot" name="username_traxroot">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="flex">
                            <input type="password" class="tw-input block w-full rounded-l-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="password_traxroot" name="password_traxroot">
                            <button class="toggle-password inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 hover:bg-gray-100 transition-colors" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-200">
                <button class="inline-flex items-center gap-1.5 rounded-lg bg-gray-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-600 transition-colors" data-hs-overlay="#modal_update_profile">Close</button>
                <button type="submit" form="formUpdateProfile" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors">Save</button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/jquery"></script>

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

    // Fix map display when Preline overlay opens
    document.querySelector('#modal').addEventListener('open.hs.overlay', function () {
        setTimeout(function() {
            map.invalidateSize();
        }, 400);
    });
}

$(document).ready(function() {

    // handle image preview
    $('#company_logo').on('change', function () {
        let file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#preview_logo').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });


    let buttonAdd = $('#addButton');
    let buttonEdit = $('.buttonEdit');
    let buttonDelete = $('.buttonDelete');
    let modal = $('#modal');
    let modalDelete = $('#modal_delete');
    let textHeaderModal = $("#modalAddLabel");
    let textHeaderModalDelete = $("#modalDeleteLabel");
    let formUser = $("#formAddUser");
    let formUserDelete = $("#formDelete");

    $('#company_phone').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // handle button add
    buttonAdd.on('click', function(e) {
        e.preventDefault();
        showModal('#modal');

        textHeaderModal.text('Add Company');
        modal.find('#company_id_add').val('');
        modal.find('#user_login_id').val('');
        modal.find('#company_name').val('');
        modal.find('#company_phone').val('');
        modal.find('#company_email').val('');
        modal.find('#pass').val('');
        modal.find('input[name="package"]').prop('checked', false);
        modal.find('#preview_logo').attr('src', "https://tse2.mm.bing.net/th/id/OIP.IZWJ479vW3ZlLf2HS18k6wHaEa?pid=Api&P=0&h=180");

        // Clear validation errors
        modal.find('.form-group').removeClass('field-error');
        modal.find('.inline-error').text('');

        formUser.attr("action", '<?= base_url('create-company') ?>')
    });


    buttonEdit.on('click', function(e) {
        e.preventDefault();
        showModal('#modal');

        // Clear validation errors
        modal.find('.form-group').removeClass('field-error');
        modal.find('.inline-error').text('');

        let companyID = $(this).data('company-id');
        textHeaderModal.text('Edit Company');
        formUser.attr("action", '<?= base_url('edit-company') ?>');

        $.ajax({
            url: '<?= base_url('Company/getCompanyDetail') ?>',
            method: 'post',
            data: { companyID: companyID },
            dataType: 'json',
            success: function(response) {
                let phoneNumber = (response.CompanyPhone || '').replace(/^\+63/, '');

                modal.find('#company_id_add').val(response.ListCompanyID);
                modal.find('#user_login_id_add').val(response.UserLoginID);
                modal.find('#company_name').val(response.CompanyName);
                modal.find('#company_phone').val(phoneNumber);
                modal.find('#company_email').val(response.CompanyEmail);

                modal.find('#pass').val('');
                modal.find('#pass').attr('placeholder', 'Leave blank to keep current password');

                modal.find('input[name="package"]').prop('checked', false);
                if (response.CompanySubscribe == 1) modal.find('#basic').prop('checked', true);
                else if (response.CompanySubscribe == 2) modal.find('#pro').prop('checked', true);

                modal.find('#preview_logo').attr('src', response.CompanyLogo ? '<?= base_url('assets/dist/img/company_logo/') ?>' + response.CompanyLogo : 'assets/dist/img/default-logo.png');
            }
        });
    });


    // Helper: set inline error on a field
    function setFieldError(fieldId, msg) {
        var $field = $('#' + fieldId);
        $field.closest('.form-group').addClass('field-error');
        $('#error-' + fieldId).text(msg);
    }

    // Helper: clear inline error on a field
    function clearFieldError(fieldId) {
        var $field = $('#' + fieldId);
        $field.closest('.form-group').removeClass('field-error');
        $('#error-' + fieldId).text('');
    }

    // Auto-clear errors on input/change
    $('#company_name, #company_email').on('input', function() {
        clearFieldError(this.id);
    });
    $('#company_phone').on('input', function() {
        clearFieldError('company_phone');
    });
    $('input[name="package"]').on('change', function() {
        clearFieldError('package');
    });
    $('#company_logo').on('change', function() {
        clearFieldError('company_logo');
    });

    formUser.on('submit', function(e) {
        e.preventDefault();

        var hasError = false;
        var companyName = $('#company_name').val().trim();
        var companyPhone = $('#company_phone').val().trim();
        var companyEmail = $('#company_email').val().trim();
        var packageSelected = $('input[name="package"]:checked').val();

        clearFieldError('company_name');
        clearFieldError('company_phone');
        clearFieldError('company_email');
        clearFieldError('package');
        clearFieldError('company_logo');

        if (companyName === '') {
            setFieldError('company_name', 'Please enter Company Name.');
            hasError = true;
        }
        if (companyPhone === '') {
            setFieldError('company_phone', 'Please enter Phone Number.');
            hasError = true;
        } else if (!/^\d{10}$/.test(companyPhone)) {
            setFieldError('company_phone', 'Please enter a valid 10-digit phone number.');
            hasError = true;
        }
        if (companyEmail === '') {
            setFieldError('company_email', 'Please enter Company Email.');
            hasError = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(companyEmail)) {
            setFieldError('company_email', 'Please enter a valid email address.');
            hasError = true;
        }
        if (!packageSelected) {
            setFieldError('package', 'Please select a package (Basic or Pro).');
            hasError = true;
        }
        var logoFile = $('#company_logo')[0].files[0];
        if (logoFile) {
            var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp'];
            if (!allowedTypes.includes(logoFile.type)) {
                setFieldError('company_logo', 'Please upload a valid image file (JPEG, JPG, PNG, GIF, BMP).');
                hasError = true;
            } else if (logoFile.size > 2 * 1024 * 1024) {
                setFieldError('company_logo', 'Logo file size must be less than 2MB.');
                hasError = true;
            }
        }

        if (hasError) return;
        this.submit();
    });


    // handle button delete
    buttonDelete.on('click', function(e) {
        e.preventDefault();

        showModal('#modal_delete');
        textHeaderModalDelete.text('Delete Company');
        formUserDelete.attr("action", '<?= base_url('delete-company') ?>');

        let companyID   = $(this).data('company-id');
        let companyName = $(this).data('company-name');
        let userLogin   = $(this).data('user-login-id');

        modalDelete.find('#company_id_delete').val(companyID);
        modalDelete.find('#company_name').text(companyName);
        modalDelete.find('#user_login_id_delete').val(userLogin);
    });


});
</script>


<!-- Update Profile -->
<script>
    $('.buttonEditProfile').on('click', function() {
        let companyID = $(this).data('company-id');

        showModal('#modal_update_profile');
        $("#formUpdateProfile").attr("action", "<?= base_url('company/update-traxroot-profile') ?>");

        $('#username_traxroot').val('');
        $('#password_traxroot').val('');

        $.ajax({
            url: "<?= base_url('Company/getCompanyDetail') ?>",
            type: "POST",
            data: { companyID: companyID },
            dataType: "json",
            success: function(res) {
                $('#company_id_profile').val(res.ListCompanyID);
                $('#username_traxroot').val(res.username_traxroot);
                $('#password_traxroot').val(res.password_traxroot);
            }
        });
    });
</script>

<!-- Hide and Show Password -->
<script>
document.addEventListener("click", function (e) {
    if (e.target.closest(".toggle-password")) {
        const button = e.target.closest(".toggle-password");
        const icon = button.querySelector("i");
        const input = document.getElementById("password_traxroot");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
});
</script>

<!-- Data Syncronisation -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($this->session->flashdata('swal')) :
$swal = $this->session->flashdata('swal');
?>
<script>
Swal.fire({
    title: "<?= $swal['title']; ?>",
    html: "<?= $swal['text']; ?>",
    icon: "<?= $swal['icon']; ?>",
});
</script>
<?php endif; ?>
