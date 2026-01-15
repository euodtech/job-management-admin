<style>
    table tbody tr td {
        vertical-align: middle !important;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Company</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
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
                            <div class="d-flex justify-content-start align-items-center" style="gap: 10px;">
                                <button class="btn btn-sm btn-primary-gradient" id="addButton" type="button">
                                    Add Company
                                </button>

                                <a class="btn btn-sm btn-secondary" href="<?= base_url('company/synchronize-traxroot') ?>" role="button">
                                    <i class="fa-solid fa-repeat"></i> Data Synchronisation
                                </a>

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
                                        <tr style="text-align: center !important;">
                                            <th style="width: 10%; text-align: center;">No</th>
                                            <th>Company Logo</th>
                                            <th>Company Code</th>
                                            <th>Company Name</th>
                                            <th>Company Email</th>
                                            <th>Package</th>
                                            <th>Update Profile</th>
                                            <th style="width: 15%; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                    $no = 1;
                                    foreach($company as $val) : ?>
                                        <tr>
                                            <td style="text-align: center !important;"><?= $no++ ?></td>
                                            <td style="text-align: center !important;">
                                                <img src="<?= $val['CompanyLogo'] ?>" width="50" alt="">
                                            </td>
                                            <td style="text-align: center !important;"><?= $val['CompanyCode'] ?></td>
                                            <td style="text-align: center !important;"><?= $val['CompanyName'] ?></td>
                                            <td style="text-align: center !important;"><?= $val['CompanyEmail'] ?></td>
                                            <td style="text-align: center !important;">
                                                <?= ($val['CompanySubscribe'] == 1) ? "Basic" : "Pro" ?>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <?php if ($val['CompanySubscribe'] == 2): ?>
                                                    <button data-company-id="<?= $val['ListCompanyID'] ?>" 
                                                            type="button"
                                                            class="btn btn-sm btn-info buttonEditProfile">
                                                        <i class="fas fa-user-cog"></i> Update Profile
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">No Access</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button data-company-id="<?= $val['ListCompanyID'] ?>" type="button"
                                                    class="btn btn-sm btn-warning buttonEdit">
                                                    <i class="fas fa-edit"></i>
                                                </button> |
                                                <button type="button" data-company-id="<?= $val['ListCompanyID'] ?>"
                                                    data-company-name="<?= $val['CompanyName'] ?>" data-user-login-id="<?= $val['UserLoginID'] ?>"
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
                <form id="formAddUser" method="post" enctype="multipart/form-data">
                    <input type="hidden" id="company_id" name="company_id" class="form-control">
                    <input type="hidden" id="user_login_id" name="user_login_id" class="form-control">
                    
                    <div class="form-group">
                        <label for="fullname">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name"
                            placeholder="Enter Company Name">
                    </div>

                    <!-- Company Phone -->
                    <div class="form-group">
                        <label for="phone_number">Company Phone</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">+63</span>
                            </div>
                            <input type="text" class="form-control" id="company_phone" name="company_phone"
                                placeholder="Enter Phone Number">
                        </div>
                    </div>

                    <!-- Company Email -->
                    <div class="form-group">
                        <label for="type_job">Company Email</label>
                        <input type="email" class="form-control" id="company_email" name="company_email"
                            placeholder="Enter Company Name">
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="type_job">Password</label>
                        <input type="password" class="form-control" id="pass" name="pass"
                            placeholder="Enter Password">
                    </div>

                    <div class="form-group">
                        <label for="package">Select Package</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="package" id="basic" value="1">
                                <label class="form-check-label" for="basic">Basic</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="package" id="pro" value="2">
                                <label class="form-check-label" for="pro">Pro</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="company_logo">Company Logo</label>
                        <div class="">
                            <img id="preview_logo" src="https://tse2.mm.bing.net/th/id/OIP.IZWJ479vW3ZlLf2HS18k6wHaEa?pid=Api&P=0&h=180" alt="Preview Logo" style="max-width: 150px; border-radius: 8px; border: 1px solid #ccc; padding: 4px;">
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="company_logo" name="company_logo" accept="image/*">
                            <label class="custom-file-label" for="company_logo">Choose image</label>
                        </div>
                        
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
                    <input type="hidden" id="company_id" name="company_id">
                    <input type="hidden" id="user_login_id" name="user_login_id">
                    <span>Do You Sure To delete Company Name : <strong id="company_name"></strong></span>
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

<div class="modal fade" id="modal_update_profile" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Update Profile</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="formUpdateProfile" method="post">
                    <input type="hidden" id="company_id_profile" name="company_id">

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" id="username_traxroot" name="username_traxroot">
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_traxroot" name="password_traxroot">

                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="submit" form="formUpdateProfile" class="btn btn-primary btn-sm">Save</button>
            </div>

        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/jquery"></script>

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

    // handle image preview
    $('#company_logo').on('change', function () {
        let file = this.files[0];
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);

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
        // hapus semua karakter yang bukan angka
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // handle button add
    buttonAdd.on('click', function(e) {
        e.preventDefault();
        modal.modal('show');

        textHeaderModal.text('Add Company');
        modal.find('#company_id').val('');
        modal.find('#user_login_id').val('');
        modal.find('#company_name').val('');
        modal.find('#company_phone').val('');
        modal.find('#company_email').val('');
        modal.find('#pass').val('');
        modal.find('input[name="package"]').prop('checked', false); 
        // Misal ini di dalam modal
        modal.find('#preview_logo').attr('src', "https://tse2.mm.bing.net/th/id/OIP.IZWJ479vW3ZlLf2HS18k6wHaEa?pid=Api&P=0&h=180");

        formUser.attr("action", '<?= base_url('create-company') ?>')
    });



    // handle button edit
    buttonEdit.on('click', function(e) {
        e.preventDefault();
        modal.modal('show');

        let companyID = $(this).data('company-id');
        textHeaderModal.text('Edit Company');
        formUser.attr("action", '<?= base_url('edit-company') ?>');

        $.ajax({
            url: '<?= base_url('Company/getCompanyDetail') ?>',
            method: 'post',
            data: {
                companyID: companyID
            },
            dataType: 'json',
            success: function(response) {
                let phoneNumber = response.CompanyPhone.replace(/^\+63/, '');
                phoneNumber = parseInt(phoneNumber);

                // console.log(response)

                // name="package"
                modal.find('#company_id').val(response.ListCompanyID);
                modal.find('#user_login_id').val(response.UserLoginID);
                modal.find('#company_name').val(response.CompanyName);
                modal.find('#company_phone').val(phoneNumber);
                modal.find('#company_email').val(response.CompanyEmail);
                modal.find('#pass').val(response.Password);
                modal.find('input[name="package"]').prop('checked', false); // reset dulu semua

                if (response.CompanySubscribe == 1) {
                    modal.find('#basic').prop('checked', true);
                } else if (response.CompanySubscribe == 2) {
                    modal.find('#pro').prop('checked', true);
                }
               // Misal ini di dalam modal
                modal.find('#preview_logo').attr('src', response.CompanyLogo);

                // modal.find('#phone').val(response.PhoneNumber);
            },
        })

    })


    // handle button delete
    buttonDelete.on('click', function(e) {
        e.preventDefault();
        modalDelete.modal('show');
        textHeaderModalDelete.text('Delete Company')
        formUserDelete.attr("action", '<?= base_url('delete-company') ?>');

        let companyID = $(this).data('company-id');
        let companyName = $(this).data('company-name');
        let userLogin = $(this).data('user-login-id');
        modalDelete.find('#company_id').val(companyID);
        modalDelete.find('#company_name').text(companyName);
        modalDelete.find('#user_login_id').val(userLogin);
    });


});
</script>


<!-- Update Profile -->
<script>
    // === Update Profile Traxroot ===
    $('.buttonEditProfile').on('click', function() {
    let companyID = $(this).data('company-id');
    
    $('#modal_update_profile').modal('show');
    $("#formUpdateProfile").attr("action", "<?= base_url('company/update-traxroot-profile') ?>");
    
    // kosongkan dulu
    $('#username_traxroot').val('');
    $('#password_traxroot').val('');
    
    // ambil data detail
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
    // Jika yang diklik adalah tombol toggle password
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
