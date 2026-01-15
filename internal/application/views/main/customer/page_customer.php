

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Customer</h1>
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
                            <div class="d-flex justify-content-start align-items-center mb-3" style="gap: 10px;">
                                <form id="import_excel_form" enctype="multipart/form-data" class="mb-0">
                                    <label for="import_excel" class="btn btn-sm btn-success mb-0" style="font-weight: 500 !important;">
                                        <i class="fa fa-file-excel"></i> Import Excel
                                    </label>
                                    <input type="file" id="import_excel" name="import_excel" accept=".xls,.xlsx" hidden>
                                </form>

                                <!-- Tombol Download Contoh Excel -->
                                <a href="<?= base_url('assets/dist/Example Excel Upload Customer.xlsx') ?>" 
                                class="btn btn-sm btn-primary" 
                                download>
                                    <i class="fa fa-download"></i> Download Example Excel
                                </a>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-sm btn-primary" id="addButton" type="button">
                                    Add Customer
                                </button>
                                <?php if($this->session->flashdata('message')): ?>
                                <?= $this->session->flashdata('message'); ?>
                                <?php endif; ?>

                            </div>


                        </div>

                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped w-100" id="example3">
                                    <thead class="">
                                        <tr style="white-space: nowrap;">
                                            <th style="width: 10%; text-align: center;">No</th>
                                            <th>Company</th>
                                            <th>Customer Name</th>
                                            <th>Customer Email</th>
                                            <th>Account Number</th>
                                            <th>Phone Number</th>
                                            <th>Address</th>
                                            <th>Maps</th>
                                            <th style="width: 15%; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                    $no = 1;
                                    foreach($customer as $val) : ?>
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;"><?= $no++ ?></td>
                                            <td style="vertical-align: middle; white-space: nowrap;"><?= $val['CompanyName'] ?></td>
                                            <td style="vertical-align: middle; white-space: nowrap;"><?= $val['CustomerName'] ?></td>
                                            <td style="vertical-align: middle; white-space: nowrap;"><?= $val['CustomerEmail'] ?></td>
                                            <td style="vertical-align: middle; white-space: nowrap;"><?= $val['AccountNumber'] ?></td>
                                            <td style="vertical-align: middle; white-space: nowrap;"><?= $val['PhoneNumber'] ?></td>
                                            <td style="vertical-align: middle;white-space: nowrap;"><?= $val['Address'] ?></td>
                                            <td style="white-space: nowrap;">
                                                <a href="https://www.google.com/maps?q=<?= $val['Latitude'] ?>,<?= $val['Longitude'] ?>" 
                                                target="_blank" 
                                                class="btn btn-sm btn-info mt-1">
                                                    Open In Maps
                                                </a>
                                            </td>
                                            <td style="vertical-align: middle; text-align: center; white-space: nowrap;">
                                                <button data-customer-id="<?= $val['CustomerID'] ?>" type="button"
                                                    class="btn btn-sm btn-warning buttonEdit">
                                                    <i class="fas fa-edit"></i>
                                                </button> |
                                                <button type="button" data-customer-id="<?= $val['CustomerID'] ?>"
                                                    data-customer-name="<?= $val['CustomerName'] ?>"
                                                    class="btn btn-sm btn-danger buttonDelete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>

                                    </tbody>
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

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalAddLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <!-- bisa ganti modal-lg ke modal-sm/modal-xl -->
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <form id="formAddUser" method="post">
                    <div class="form-group <?= ($this->session->userdata('Role') != 1) ? "d-none" : "" ?> " >
                        <label for="email">Select Company</label>
                         <select name="company_selected" class="form-control select2For_modal" id="company_selected" required>
                            <option value="">---- Select Company ----</option>
                            <?php foreach($list_company as $val): ?>
                                <option value="<?= $val['ListCompanyID'] ?>" <?= ($this->session->userdata('CompanyID') == $val['ListCompanyID']) ? "selected" : "" ?> <?= ($this->session->userdata('Role') != 1) ?  "disabled" : "" ?> ><?= $val['CompanyName'] ?></option>
                            <?php endforeach; ?>
                         </select>
                    </div>
                    <div class="form-group">
                        <input type="hidden" id="customer_id" name="customer_id" class="form-control">
                        <label for="fullname">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                            placeholder="Enter Customer Name" required>
                    </div>

                    <div class="form-group">
                        
                        <label for="fullname">Customer Email</label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email"
                            placeholder="Enter Customer Email" required>
                    </div>

                    <div class="form-group">
                        
                        <label for="fullname">Account Number</label>
                        <input type="text" class="form-control" id="account_number" name="account_number"
                            placeholder="Enter Account Number" required>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">+63</span>
                            </div>
                            <input type="text" class="form-control" id="phone_number" name="phone_number"
                                placeholder="Enter Phone Number" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type_job">Address</label>
                        <textarea name="address" id="address" rows="5" class="form-control" required></textarea>
                    </div>
                    <div class="form-group mt-3">
                        <label for="map">Select Location on Map</label>
                        <div id="map" style="width: 100%; height: 300px; border-radius: 8px; border: 1px solid #ccc;"></div>
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="submit" form="formAddUser" class="btn btn-sm btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="modalAddLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <!-- bisa ganti modal-lg ke modal-sm/modal-xl -->
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <form id="formDelete" method="post">
                    <input type="hidden" id="customer_id" name="customer_id">
                    <span>Do You Sure To delete Customer Name : <strong id="customer_name"></strong></span>
                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="submit" form="formDelete" class="btn btn-sm btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/jquery"></script> -->

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>

function showMaps(defaultLat, defaultLng) {
    // ✅ Cek kalau sebelumnya sudah ada map, hapus dulu biar gak dobel instance
    if (window.currentMap) {
        window.currentMap.remove();
        window.currentMap = null;
    }

    // ✅ Buat map baru
    var map = L.map('map').setView([defaultLat, defaultLng], 13);
    window.currentMap = map; // simpan instance biar bisa dihapus nanti

    var marker = L.marker([defaultLat, defaultLng]).addTo(map);

    $('#latitude').val(defaultLat);
    $('#longitude').val(defaultLng);

    // Tile layer OSM
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    // Klik di peta untuk ubah posisi marker
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

    // ✅ Tambahkan Search Geocoder (fokus Filipina)
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

    // 🔥 Fix tampilan map setengah saat modal muncul
    $('#modal').on('shown.bs.modal', function () {
        setTimeout(function() {
            map.invalidateSize();
        }, 300);
    });
}



$(document).ready(function() {
    // Inisialisasi Map
    


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
        // hapus semua karakter yang bukan angka
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // handle button add
    buttonAdd.on('click', function(e) {
        e.preventDefault();
        modal.modal('show');

        textHeaderModal.text('Add Customer');
        formUser.attr("action", '<?= base_url('create-customer') ?>')

        var defaultLat = 14.5995;
        var defaultLng = 120.9842;

        showMaps(defaultLat, defaultLng)
    });



    // handle button edit
    buttonEdit.on('click', function(e) {
        e.preventDefault();
        modal.modal('show');

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
                
                // modal.find('#email').val(response.Email);
                // modal.find('#phone').val(response.PhoneNumber);
            },
        })

    })


    // handle button delete
    buttonDelete.on('click', function(e) {
        e.preventDefault();
        modalDelete.modal('show');
        textHeaderModalDelete.text('Delete Customer')
        formUserDelete.attr("action", '<?= base_url('delete-customer') ?>');

        let customerID = $(this).data('customer-id');
        let customerName = $(this).data('customer-name');
        modalDelete.find('#customer_id').val(customerID);
        modalDelete.find('#customer_name').text(customerName);
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
            url: '<?= base_url("Customer/import_excel"); ?>', // ganti sesuai endpoint
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
                                confirmButton: 'btn btn-sm btn-primary'
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
                                confirmButton: 'btn btn-sm btn-primary'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                }

                // console.log(response);
                $('#import_excel').val(''); // reset input
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