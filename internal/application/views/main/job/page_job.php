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
        flex: 0 0 120px; /* lebar tetap untuk label */
        font-weight: 600;
        color: #212529;
    }

    .content_header .value {
        flex: 1;
        color: #495057;
    }

    /* .job-gallery {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    } */

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
        background-color: #ffc107; /* kuning */
        box-shadow: 2px 2px 8px rgba(255, 193, 7, 0.4); /* bayangan kuning transparan */
        color: white;
    }

    .completed_job {
        background-color: #28a745; /* hijau */
        box-shadow: 2px 2px 8px rgba(40, 167, 69, 0.4); /* bayangan hijau transparan */
        color: white;
    }

</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Job <?= $label_job ?></h1>
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
                            
                            <div class="row">
                                <!-- Today's Job -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon elevation-1" style="background-color: #0a1431ff;">
                                            <i class="fa-solid fa-calendar-day text-white"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                Today's Job <br>
                                                <strong id="today_job_count"></strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ongoing Job -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon elevation-1" style="background-color: #ffc107;">
                                            <i class="fa-solid fa-briefcase text-white"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                Ongoing Job <br>
                                                <strong id="ongoing_job_count"></strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Completed Job -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon elevation-1" style="background-color: #28a745;">
                                            <i class="fa-solid fa-circle-check text-white"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                Upcomming Job <br>
                                                <strong id="upcoming_job_count"></strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upcoming Job -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon elevation-1" style="background-color: #17a2b8;">
                                            <i class="fa-solid fa-calendar-plus text-white"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                Reschedule Job <br>
                                                <strong id="reschedule_job_count"></strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <button class="btn btn-sm btn-primary" id="addButton" type="button">
                                        Add Job
                                    </button>
                                </div>
                                <div>
                                    <?php if($this->session->flashdata('message')): ?>
                                        <?= $this->session->flashdata('message'); ?>
                                    <?php endif; ?>
                                </div>
                                

                            </div>
                            

                            <div class="table-responsive">

                                <input type="hidden" id="type_for_job" value="<?= $type_job ?>">
                                <!--  -->
                                <table class="table table-bordered table-striped" id="tableJobRider">
                                    <thead class="">
                                        <tr style="white-space: nowrap;">
                                            <th style="width: 10%; text-align: center;">No</th>
                                            <th>Create Job</th>
                                            <th>Job Name</th>
                                            <th>To Customer</th>
                                            <th>Customer Address</th>
                                            <th>User Get The Job</th>
                                            <th>Type Job</th>
                                            <th >Status Job</th>
                                            <th style="width: 15%; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <!-- <tbody>
                                        <?php 
                                    $no = 1;
                                    foreach($job as $val) : ?>
                                        <tr>
                                            <td style="text-align: center;"><?= $no++ ?></td>
                                            <td style="white-space: nowrap;"><?= return_date_format($val['JobDate']) ?>
                                            </td>
                                            <td><?= $val['JobName'] ?></td>
                                            <td><?= $val['CustomerName'] ?></td>
                                            <td style="white-space: nowrap;"><?= $val['Address'] ?></td>
                                            <td>
                                                <?php $hasCancel = ($val['StatusCancelJob'] !== null && count($val['StatusCancelJob']) > 0);?>

                                                <?php if ($val['Fullname'] === null): ?>
                                                    <span class="text-danger">Not Assign User</span>
                                                <?php else: ?>
                                                    <span style="white-space: nowrap;">Driver Name : <strong><?= $val['Fullname'] ?></strong></span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if($val['TypeJob'] == 1) {
                                                echo "Line Interrupt";
                                            } elseif($val['TypeJob'] ==2) {
                                                echo "Reconnection";
                                            } elseif($val['TypeJob'] == 3) {
                                                echo "Short Circuit";
                                            } ?>
                                            </td>
                                            <td style="width: 20%;">
                                                <?php if($val['Status'] == 1) : ?>
                                                    <span class='ongoing_job'>Ongoing Job</span>
                                                <?php elseif($val['Status'] ==2): ?>
                                                    <span class='finished_job'>Finished Job</span>
                                                <?php  else:?>
                                                    <span class='awaiting_job'>Awaiting Driver</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">

                                                <?php if($val['Status'] == null) : ?>
                                                    <button data-jobid="<?= $val['JobID'] ?>" type="button"
                                                        class="btn btn-sm btn-warning buttonEdit" title="Edit Job">
                                                        <i class="fas fa-edit"></i>
                                                    </button> |
                                                    <button type="button" data-jobid="<?= $val['JobID'] ?>"
                                                        data-job-name="<?= $val['JobName'] ?>"
                                                        class="btn btn-sm btn-danger buttonDelete" title="Delete Job">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php elseif($val['Status'] == 1 OR $val['Status'] == 2): ?>

                                                    <?php if($hasCancel) : ?>
                                                        <button class="btn btn-sm  btn-danger button_history_cancel_job" data-job-id="<?= $val['JobID'] ?>" title="History cancel Job" ><i class="fa-solid fa-clock-rotate-left"></i></button> |
                                                    <?php endif ?>
                                                    
                                                    <button data-jobid="<?= $val['JobID'] ?>" type="button"
                                                        class="btn btn-sm btn-success buttonDetail" title="Detail Job">
                                                        <i class="fas fa-eye" ></i>
                                                    </button>

                                                    <?php if($val['Status'] == 2) : ?>
                                                        |
                                                        <button data-jobid="<?= $val['JobID'] ?>" type="button"
                                                            class="btn btn-sm btn-info buttonCamera" title="Detail Photo">
                                                            <i class="fas fa-camera" ></i>
                                                        </button>
                                                    <?php endif; ?>

                                                <?php endif ?>
                                                
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>

                                    </tbody> -->
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

                    <h5>Job Information: </h5>
                    <hr>
                    <div class="form-group">
                        <input type="hidden" id="job_id" name="job_id" class="form-control">
                        <label for="fullname">Job Name</label>
                        <input type="text" class="form-control" id="job_name" name="job_name"
                            placeholder="Enter Job Name">
                    </div>
                    <!-- <div class="form-group">
                        <label for="customer_id">Customer</label>
                        <select class="form-control select2For_modal" name="customer_id" id="customer_id" required>
                            <option value="">--- Select Customer ---</option>
                            <?php foreach($customer as $val): ?>
                            <option value="<?= $val['CustomerID'] ?>"><?= $val['CustomerName'] ?> -
                                <?= $val['Address'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div> -->
                    <div class="form-group">
                        <label for="type_job">Type Job</label>
                        <select class="form-control select2bs4" name="type_job" id="type_job" disabled required>
                            <option value="">--- Select Type Job ---</option>
                            <option value="1">Line Interrupt</option>
                            <option value="2">Reconnection</option>
                            <option value="3">Short Circuit</option>
                            <option value="4">Disconnection</option>
                        </select>

                        <input type="hidden" name="type_job_input" id="type_job_input">
                    </div>
                    <div class="form-group">
                        <label for="type_job">Date Job</label>
                        <input type="date" class="form-control" id="job_date" name="job_date" value="<?= date('Y-m-d') ?>"
                            placeholder="Enter Job Date">
                    </div>

                    <hr>
                    <h5>Customer Information: </h5>
                    <hr>
                    <div class="form-group">
                        <label for="fullname">Customer Name</label>
                        <input type="hidden" class="form-control" id="customer_id" name="customer_id">
                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                            placeholder="Enter Customer Name">
                    </div>
                    <div class="form-group">
                        <label for="fullname">Customer Email</label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email"
                            placeholder="Enter Customer Email">
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">+63</span>
                            </div>
                            <input type="text" class="form-control" id="phone_number" name="phone_number"
                                placeholder="Enter Phone Number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type_job">Address</label>
                        <textarea name="address" id="address" rows="5" class="form-control"></textarea>
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
                    <input type="hidden" id="job_id" name="job_id">
                    <span>Do You Sure To delete Job Name : <strong id="job_name"></strong></span>
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

<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <!-- bisa ganti modal-lg ke modal-sm/modal-xl -->
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">
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

                    <!-- <div class="row-item">
                        <div class="label">Assign To Job:</div>
                        <div class="value" id="to_assign_detail"></div>
                    </div> -->
                </div>

                <!-- <div class="row job-gallery">
                    <div class="col-12 col-md-6">
                        <img src="http://quetraverse.pro/efms/api/storage/app/finished_jobs/job_4_1759466668_0.png" alt="Job Photo 1">
                    </div>
                    <div class="col-12 col-md-6">
                        <img src="http://quetraverse.pro/efms/api/storage/app/finished_jobs/job_4_1759466668_0.png" alt="Job Photo 2">
                    </div>
                </div> -->
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <!-- <button type="submit" form="formDelete" class="btn btn-sm btn-primary">Save</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_camera" tabindex="-1" role="dialog" aria-labelledby="modalCameraLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <!-- bisa ganti modal-lg ke modal-sm/modal-xl -->
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalCameraLabel">Detail Photo Job</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="row job-gallery">
                    
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <!-- <button type="submit" form="formDelete" class="btn btn-sm btn-primary">Save</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_history_cancel_job" tabindex="-1" role="dialog" aria-labelledby="modalCameraLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <!-- bisa ganti modal-lg ke modal-sm/modal-xl -->
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalCameraLabel">Detail Cancel Job</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body body_detail_history_cancel_job">
                
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
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
        modal.modal('show');

        textHeaderModal.text('Add Job');
        formUser.attr("action", '<?= base_url('create-job') ?>')

        modal.find('#job_id').val('');
        modal.find('#job_name').val('');
        const today = new Date().toISOString().slice(0, 10);
        modal.find('#job_date').val(today);
        // modal.find('#customer_id').val('').trigger('change');

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

                        userDriver = `<span class="text-danger">Not Assign User</span>`;
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
                            class="btn btn-sm btn-warning buttonEdit" title="Edit Job">
                            <i class="fas fa-edit"></i>
                        </button> |
                        <button type="button" data-jobid="${data}"
                            data-job-name="${row.JobName}"
                            class="btn btn-sm btn-danger buttonDelete" title="Delete Job">
                            <i class="fas fa-trash"></i>
                        </button>
                        `;


                    } else if(row.Status == 1 || row.Status == 2 || row.Status == 3) {

                        if(row.StatusCancelJob.length > 0) {

                            labelButtonAction = `
                            <button class="btn btn-sm  btn-danger button_history_cancel_job" data-job-id="${data}" title="History cancel Job" ><i class="fa-solid fa-clock-rotate-left"></i></button> | 

                            <button data-jobid="${data}" type="button"
                                class="btn btn-sm btn-success buttonDetail" title="Detail Job">
                                <i class="fas fa-eye" ></i>
                            </button>
                            `;
                        } else {
                            labelButtonAction = `
                            <button data-jobid="${data}" type="button"
                                class="btn btn-sm btn-success buttonDetail" title="Detail Job">
                                <i class="fas fa-eye" ></i>
                            </button>
                            `
                        }

                        if(row.Status == 2) {

                            labelButtonAction += `
                             |
                            <button data-jobid="${data}" type="button"
                                class="btn btn-sm btn-info buttonCamera" title="Detail Photo">
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
    $('#modal').modal('show');

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
    $('#modal_delete').modal('show');
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

    $('#modal_detail').modal('show');
    $('#modal_detail').find('#modalDetailLabel').text('Detail Job');

    $.ajax({
        url: '<?= base_url('Job/getJobDetail') ?>',
        method: 'post',
        data: {
            jobID: jobID
        },
        dataType: 'json',
        success: function(response) {

            // console.log(response);

            let statusJob = response.Status;
            let typeJob = response.TypeJob;

            let labelJob, labelTypeJob, classLabelJob;

            if(statusJob == 1) {

                labelJob = 'Ongoing';
                classLabelJob = "ongoing_job";
                $('#modal_detail').find('.job_reschedule').addClass('d-none');
            } else if(statusJob == 2) {
                labelJob = 'Complete Job';
                classLabelJob = "completed_job";
                $('#modal_detail').find('.job_reschedule').addClass('d-none');
            } else if(statusJob == 3) {
                labelJob = 'Reschedule Job';
                classLabelJob = "reschedule_job";
                $('#modal_detail').find('.job_reschedule').removeClass('d-none');
            }

            if(typeJob == 1) {
                labelTypeJob = "Line Interrupt";
            } else if(typeJob == 2) {
                labelTypeJob = "Reconnection";
            } else if(typeJob == 3){
                labelTypeJob = "Short Circuit";
            }

            // console.log(response.RescheduledDateJob);

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

    $('#modal_history_cancel_job').modal('show');
    $('#modal_history_cancel_job .body_detail_history_cancel_job').load('<?= base_url('Job/historyCancelJob?jobID=') ?>' + jobID);
});

// HANDLE BUTTON DETAIL PHOTO JOB

$(document).on('click', '.buttonCamera', function(e) {

    e.preventDefault();
    let jobID = $(this).data('jobid');

    $('#modal_camera').modal('show');

    $.ajax({
        url: '<?= base_url('Job/getDetailPhoto') ?>',
        method: 'POST',
        data: { jobID: jobID },
        dataType: 'json',
        success: function(response) {

            // Kosongkan isi sebelumnya dulu
            let galleryContainer = $('.job-gallery');
            galleryContainer.empty();

            // Kalau data ada
            if (response.length > 0) {
                response.forEach((item, index) => {
                    let html = `
                        <div class="col-12 col-md-3 mb-3">
                            <img src="${item.Photo}" alt="Job Photo ${index + 1}" class="img-fluid rounded shadow-sm">
                        </div>
                    `;
                    galleryContainer.append(html);
                });
            } else {
                // Kalau gak ada foto
                galleryContainer.html(`
                    <div class="col-12 text-center text-muted py-4">
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