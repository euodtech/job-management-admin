<footer class="main-footer text-sm no-print">
    <strong>Copyright &copy; 2025</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Made With Pride</b>
    </div>
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<script src="https://cdn.jsdelivr.net/npm/jquery"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url('assets/plugins/jquery-ui/jquery-ui.min.js'); ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<!-- ChartJS -->
<script src="<?php echo base_url('assets/plugins/chart.js/Chart.min.js'); ?>"></script>
<!-- Select2 -->
<script src="<?php echo base_url('assets/plugins/select2/js/select2.full.min.js'); ?>"></script>

<!-- Summernote -->
<script src="<?php echo base_url('assets/plugins/summernote/summernote-bs4.min.js'); ?>"></script>

<!-- AdminLTE App -->
<script src="<?php echo base_url('assets/dist/js/adminlte.js'); ?>"></script>

<!-- AdminLTE for demo purposes -->

<script src="<?php echo base_url('assets/dist/js/demo.js'); ?>"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="<?php echo base_url('assets/dist/js/pages/dashboard.js'); ?>"></script> -->

<!-- DataTables -->
<script src="<?php echo base_url('assets/plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap4.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
<script src="<?php echo base_url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<!-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script> -->
<!-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.pageLength.min.js"></script> -->

<!-- JSZip & pdfmake (dibutuhkan untuk Excel/PDF export) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script> -->

<!-- SweetAlert2 -->
<script src="https://cdn.rawgit.com/jeromeetienne/jquery-qrcode/master/jquery.qrcode.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->


<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js" integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/fontawesome.min.js" integrity="sha512-obFNtQ1JKCrxPBPLmYDUevlriATl5EhvwU3CFtdW/HKOkeAe0bbsyZfHO44/f1QyndrZJ464TQvrRP9ZjyXSSA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/regular.min.js" integrity="sha512-Zq4D1wxoa4GRA5ejM+34rZkeuuKX8Xq9rIsfsX2yH3NKG4SyJT8BjLFIIQSgN7F8oe2IIHlGbVsDzdTgjB1lgA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>




<script type="text/javascript">
$(document).ready(function() {

    countJobInSidebar()

    $('#example1 tbody tr').each(function() {
        $(this).find('td').each(function() {
            var id = $(this).attr('id');
            var cellText = $(this).text().trim();

            if (id === 'tengah') {
                $(this).css('text-align', 'center');
            } else if (id === 'kiri') {
                $(this).css('text-align', 'left');
            } else if (id === 'kanan') {
                $(this).css('text-align', 'right');
            }
        });
    });
});
</script>
<script>
$('.summernote').summernote({
    height: 300
});

$(function() {
$('#category').change(function() {

    var idcat = $('#category').val();

    $.ajax({
        type: "POST",
        url: '<?php echo base_url('Product/get_subcategory') ?>',
        data: {
            idcat: idcat
        },
        dataType: 'json',
        cache: false,
        success: function(response) {
            var len = response.length;

            $("#subcategory").empty();

            for (var i = 0; i < len; i++) {
                var id = response[i]['SubCategoryID'];
                var name = response[i]['SubCategoryName'];

                
                $("#subcategory").append("<option value='" + id + "'>" + name +
                    "</option>");

            }
        }
    })


})

$("#example1").DataTable({
    "responsive": false,
    "length" : true,
    "scrollX": true 

});

$("#example3").DataTable({
    "stateSave": true,
    "paging": true,
    "autoWidth": true,
    "responsive": false,
    "scrollX": true
});

$(".table_dashboard1").DataTable({
    "responsive": false,
    "length" : true,
    "info" : false,
    "scrollX": true 
});

$(document).on('click', '.has-treeview > a', function (e) {
    e.preventDefault();

    // ambil icon di dalam link yang diklik
    let icon = $(this).find('.right');
    
    // toggle icon (kiri ↔ bawah)
    if (icon.hasClass('fa-angle-left')) {
        icon.removeClass('fa-angle-left').addClass('fa-angle-down');
    } else {
        icon.removeClass('fa-angle-down').addClass('fa-angle-left');
    }
});


$('#example2').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": true,
    "responsive": true,
});

$("#example1excel").DataTable({
    dom: 'Bfrtip',
    buttons: [{
        extend: 'excelHtml5',
        text: 'Export Excel',


        className: 'btn-info',
        title: 'Ortuseight-Report'
    }],
    "autoWidth": true,
    "stateSave": true,
    "paging": true,
    "scrollX": true

});

$("#excelProduct").DataTable({
    dom: 'Bfrtip',
    buttons: [{
        extend: 'excelHtml5',
        text: 'Export Excel',


        className: 'btn btn-sm btn-primary"',
        title: 'Ortuseight-Product Stock'
    }],
    "autoWidth": true,
    "stateSave": true,
    "paging": true,
    "scrollX": true

});

$("#example2excel").DataTable({
    dom: 'Bfrtip',
    buttons: [{
        extend: 'excelHtml5',
        title: '',
        text: 'Export Data',
        exportOptions: {
            columns: [1, 4, 6, 9, 10, 11]
        },


        className: 'btn-primary',
    }],
    "responsive": true,
    "autoWidth": true,
    "stateSave": true,
    "paging": true

});

});

$('.custom-file-input').on('change', function() {
    let fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
});

</script>

<script>
    function previewImage(input, imageId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#' + imageId).attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<script>

    function playNotificationSound() {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

        function beep(timeOffset) {
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();

            oscillator.type = "square";      // suara beep lebih tegas
            oscillator.frequency.value = 750; // nada beep

            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            // volume pelan -> turun cepat
            gainNode.gain.setValueAtTime(0.4, audioCtx.currentTime + timeOffset);
            gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + timeOffset + 0.15);

            oscillator.start(audioCtx.currentTime + timeOffset);
            oscillator.stop(audioCtx.currentTime + timeOffset + 0.15);
        }

        // Beep pertama
        beep(0);

        // Beep kedua (setelah 200ms)
        beep(0.2);
    }

    
    function countJobInSidebar()
    {
        $.ajax({
            url: "<?= base_url('Home/get_total_job_ajax') ?>",
            method: "GET",
            dataType : 'json',
            success: function(response) {

                $(".count_notif_sidebar").text(response.CountAllType);
                $(".count_line").text(response.CountLine);
                $(".count_short").text(response.CountShort);
                $(".count_disconnection").text(response.CountDc);
                $(".count_reconnect").text(response.CountReconnect);

                let count_reschedule = response.CountReschedule;
                
                if(count_reschedule > 0 ) {

                    playNotificationSound();
                    $(".count_reschedule").html('<i class="fa-solid fa-bell bell-shake text-danger" style="font-size: 13px !important;"></i>');
                }
            }
        });
    }

    $(function() {

        //Initialize Select2 Elements
        //$('.select2').select2()
        $("#areas").select2({
            theme: 'bootstrap4',
            ajax: {
                url: "<?php echo base_url('SendNotif/get_area') ?>",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchTerm: params.term // search term
                    };
                },
                processResults: function(response) {
                    return {
                        results: response
                    };
                },

            }
        });

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        

        setInterval(function () {
            countJobInSidebar();
        }, 5000);

        $('.select2For_modal').each(function() {
            $(this).select2({
                theme: 'bootstrap4',
                dropdownParent: $(this).closest('.modal')
            });
        });

        $('#memTable').DataTable({
            // dom: 'Bfrtip',
            // buttons: [{
            //     extend: 'excelHtml5',
            //     title: 'Export Data Member',
            //     text: 'Export Data',
            //     exportOptions: {
            //         columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
            //     },


            //     className: 'btn-primary'

            // }],
            "stateSave": true,
            'processing': true,
            'serverSide': true,
            "scrollX": true,
            'serverMethod': 'post',
            'ajax': {
                'url': '<?php echo base_url() ?>Member/get'
            },
            'columns': [

                {
                    data: 'CustomerID',
                    render: function(data, type, row) {
                        return '<button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal_show" onclick="look(' +
                            data + ')" >History</button';
                    },
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        return moment(data).format('D MMMM YYYY', 'Do MMM YYY', 'fr');
                    }
                },
                {
                    data: 'CustomerName'
                },
                {
                    data: 'Gender'
                },
                {
                    data: 'Email'
                },
                {
                    data: 'Phone'
                },
                {
                    data: 'ProvinceName'
                },
                {
                    data: 'CityName'
                },
                {
                    data: 'BirthDate',
                    render: function(data) {
                        return moment(data).format('D MMMM YYYY', 'Do MMM YYY', 'fr');
                    }

                },
                {
                    data: 'TotalPoin',
                    render: $.fn.dataTable.render.number(',')
                },
                {
                    data: 'CustomerID',
                    render: function(data, type, row) {
                        return '<button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal_history" onclick="history(' +
                            data + ')" >History Point</button>';

                    }
                },
                {
                    data: 'TotalTransaction',
                    render: $.fn.dataTable.render.number(',')
                },
                {
                    data: 'QtyTrans',
                    render: $.fn.dataTable.render.number(',')
                }
            ]
        });

        $('#reportAllTrans').DataTable({
            "stateSave": true,
            'processing': true,
            'serverSide': true,
            "scrollX": true,
            "paging": true,
            'serverMethod': 'post',
            'ajax': {
                'url': '<?php echo base_url() ?>Transaction/get?ex_country=<?= $_GET["country"] ?> '
            },
            'columns': [

                {
                    data: 'StatusTransID',
                    render: function(data, type, row) {
                        if (data == 2) {
                            return '<button class="btn btn-sm btn-info" disabled>Success Payment</button>';
                        } else if (data == 8) {
                            return '<button class="btn btn-sm btn-danger" disabled>Reject By MidTrans</button>';
                        } else if (data == 1) {
                            return '<button class="btn btn-sm btn-primary" disabled>Pending Payment</button>';
                        } else if (data == 4) {
                            return '<button class="btn btn-sm btn-success" disabled>Complete</button>';
                        } else if (data == 7) {
                            return '<button class="btn btn-sm btn-danger" disabled>Cancel / Reject By Admin</button>';
                        } else if (data == 3) {
                            return '<button class="btn btn-sm btn-warning" disabled>In Shipping</button>';
                        }
                    },
                },
                {
                    data: 'TransactionNumber',
                    render: function(data, type, row) {
                        return '<button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal_show" onclick="detail(`' +
                            data + '`, ' + row.CodeID + ')">See Detail</button>';
                    }
                },

                {
                    data: 'CodeLabel',

                },
                {
                    data: 'TransactionNumber',

                },
                {
                    data: 'TransactionDatetime',
                },
                {
                    data: 'csname',
                },
                {
                    data: 'ReceiverName',
                },
                {
                    data: 'Phone',
                },
                {
                    data: 'SubTotalTransaction',
                    render: $.fn.dataTable.render.number(','),
                },
                {
                    data: 'Discount',
                    render: $.fn.dataTable.render.number(','),

                },
                {
                    data: 'PointRedeem',
                    render: $.fn.dataTable.render.number(','),
                },
                {
                    data: 'Fare',
                    render: $.fn.dataTable.render.number(','),
                },
                {
                    data: 'Insurance',
                    render: $.fn.dataTable.render.number(','),
                },
                {
                    data: 'GrandTotalTransaction',
                    render: $.fn.dataTable.render.number(','),
                },
                {
                    data: 'PointObtain',
                    render: $.fn.dataTable.render.number(','),
                },
                {
                    data: 'AddressName',
                    render: function(data, type, row) {
                        return data +
                            '<br>' + row.kecamatan + '<br>' + row.Kota + '<br>' + row.Provinsi;
                    }

                }
            ]
        });

        $('#tableVoucher').DataTable({
            "stateSave": true,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '<?= $url_datatable ?>'
            },
            'columns': [

                {

                    data: 'CustomerName'

                },
                {

                    data: 'Email'

                },
                {

                    data: 'Phone'

                },
                {

                    data: 'VoucherName'

                },
                {

                    data: 'TransactionNumber'

                },
                {

                    data: 'UsedStatus'

                }

            ]
        });

        $('#voucherCustomer').DataTable({
          "stateSave": true,
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
             'url':'<?php echo base_url()?>VoucherMaster/customer'
          },
          'columns': [

           {
            
            data : 'VoucherName'
                
           },
           {
            
            data : 'CustomerName'
                
           },
           {
            
            data : 'QtyVo',render: $.fn.dataTable.render.number( ',' )
                
           },
           {
            
            data : 'ValidFrom'
                
           },
           { 
                data: "ValidUntil",
                render: function(data, type, row) {
                    if (data === null) {
                        return '<span style="color:green;">Lifetime</span>';
                    }
                    return data;
                }
            },
           {
            data : 'VoucherCustomerID',
            render: function(data, type, row) {
                    return '<a href="<?php echo base_url('VoucherMaster/deleteVoucherCustomer/')?>'+data+'" class="btn btn-sm btn-danger" onclick="return confirmDelete()" ><span class="fas fa-trash-alt" aria-hidden="true"></span></a>';
                }
           }

            ]
        });

    })
</script>

<script>
function alertError(title, text) {
    Swal.fire({
        title,
        text,
        icon: 'error',
    })
}
$(function() {
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    const flashData = $('.flash-data').data('flashdata');

    if (flashData) {
        Toast.fire({
            title: 'Congratulation! ',
            text: flashData,
            icon: 'success'
        });
    }

    const flashError = $('.flash-error').data('flashdata');

    if (flashError) {
        Toast.fire({
            title: 'Sorry!',
            text: flashError,
            icon: 'error'
        });
    }

    $('.swt').on('click', function(e) {

        e.preventDefault();

        const href = $(this).attr('href');

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to end session!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#da1e26',
            cancelButtonColor: '#003d79',
            confirmButtonText: 'Yes, sign out!'
        }).then((result) => {
            if (result.value) {
                document.location.href = href;
            }
        })
    })

    $('.swalDefaultInfo').click(function() {
        Toast.fire({
            icon: 'info',
            title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
        })
    });


    $('.swalDefaultWarning').click(function() {
        Toast.fire({
            icon: 'warning',
            title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
        })
    });
    $('.swalDefaultQuestion').click(function() {
        Toast.fire({
            icon: 'question',
            title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
        })
    });
})
</script>

<script type="text/javascript">
    function readURL(image) {
        if (image.files && image.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('.img-thumbnail').attr('src', e.target.result);
            }
            reader.readAsDataURL(image.files[0]);
        }
    }

    $("input[name='image']").change(function() {
        readURL(this);
    });

    function goBack() {
        window.history.back();
    }
</script>

<script>
    $("#func-export-excel-member").click(function() {
        $('#func-export-excel-member .preloader').show();
        $('#func-export-excel-member .label').hide();
        $.ajax({
            url: "Member/export",
            responseType: "blob",
            success: function(data) {
                const url = URL.createObjectURL(
                    new Blob([data], {
                        type: "application/vnd.ms-excel"
                    })
                );
                const link = document.createElement("a");
                link.href = url;
                link.setAttribute("download", "file-member.xls");
                document.body.appendChild(link);
                link.click();
                $('#func-export-excel-member .preloader').hide();
                $('#func-export-excel-member .label').show();
            }
        })
    });

    $(`input[name="select_link"]`).click(function() {
        const data = $(this).val()
        if (data == "product") {
            $(".select-link-magazine select").val("").trigger("change")

            $(".select-link-product").show();
            $(".select-link-magazine").hide();
        } else {
            $(".select-link-product select").val("").trigger("change")

            $(".select-link-product").hide();
            $(".select-link-magazine").show();
        }
    })

    $('#qrformat').DataTable({
            "stateSave": true,
            'processing': true,
            'serverSide': true,
            "paging": true,
            'serverMethod': 'POST',
            'ajax': {
                'url': '<?php echo base_url('Qr/get_format') ?>',
                'error': function(xhr, error, thrown) {
                    // Display a detailed error message
                    console.log("Error: ", error);
                    console.log("Thrown: ", thrown);
                    console.log("Response: ", xhr.responseText);

                }
            },
            'columns': [
                { data: 'ItemCode' },
                { data: 'ItemDesc' },
                { data: 'QrCode' }
            ]
        });
</script>
</body>

</html>