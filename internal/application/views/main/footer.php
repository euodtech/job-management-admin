<footer id="main-footer" class="lg:ml-64 border-t border-gray-200 bg-white px-6 py-3 text-sm text-gray-500 no-print">
    <strong>Copyright &copy; 2026</strong>
    All rights reserved.
    <div class="hidden sm:inline-block float-right">
        <!-- <b>Made With Pride</b> -->
    </div>
</footer>

</div>
<!-- /.min-h-screen -->

<!-- DataTables screen overlay -->
<div id="dt-screen-overlay" class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/40 backdrop-blur-[2px] opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="inline-flex items-center gap-3 bg-white px-6 py-4 rounded-2xl shadow-2xl border border-gray-100">
        <div class="animate-spin rounded-full h-6 w-6 border-[2.5px] border-gray-200 border-t-primary"></div>
        <span class="text-sm font-medium text-gray-600">Loading data...</span>
    </div>
</div>

<!-- jQuery UI (jQuery already loaded in header.php) -->
<script src="<?php echo base_url('assets/plugins/jquery-ui/jquery-ui.min.js'); ?>"></script>

<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>

<!-- Select2 -->
<script src="<?php echo base_url('assets/plugins/select2/js/select2.full.min.js'); ?>"></script>

<!-- Summernote -->
<script src="<?php echo base_url('assets/plugins/summernote/summernote-bs4.min.js'); ?>"></script>

<!-- DataTables (ONE VERSION ONLY) -->
<script src="<?php echo base_url('assets/plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js'); ?>"></script>

<!-- DataTables Buttons -->
<script src="<?php echo base_url('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-buttons/js/buttons.html5.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-buttons/js/buttons.print.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-buttons/js/buttons.colVis.min.js'); ?>"></script>

<!-- JSZip & PDFMake -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Moment -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>


<script type="text/javascript">
/* === Global DataTables defaults: chevron arrows + auto-hide pagination === */
$.extend(true, $.fn.dataTable.defaults, {
    language: {
        paginate: {
            previous: '\u2039',
            next: '\u203A'
        },
    }
});
/* === Screen overlay for DataTables processing === */
$(document).on('processing.dt', function(e, settings, processing) {
    var overlay = document.getElementById('dt-screen-overlay');
    if (!overlay) return;
    if (processing) {
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        overlay.classList.add('opacity-100', 'pointer-events-auto');
    } else {
        overlay.classList.add('opacity-0', 'pointer-events-none');
        overlay.classList.remove('opacity-100', 'pointer-events-auto');
    }
});
$(document).on('draw.dt', function(e, settings) {
    var api = new $.fn.dataTable.Api(settings);
    var container = $(api.table().container());
    var pageInfo = api.page.info();
    if (pageInfo.pages <= 1) {
        container.find('.dataTables_paginate').hide();
    } else {
        container.find('.dataTables_paginate').show();
    }
});

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
                    $(".count_reschedule").html('<i class="fa-solid fa-bell bell-shake text-red-600" style="font-size: 13px !important;"></i>');
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
                dropdownParent: $(this).closest('[role="dialog"]')
            });
        });

        // Re-initialize Select2 inside Preline overlays when opened
        document.querySelectorAll('[role="dialog"]').forEach(function(overlay) {
            overlay.addEventListener('open.hs.overlay', function() {
                $(this).find('.select2For_modal').each(function() {
                    if (!$(this).data('select2')) {
                        $(this).select2({
                            theme: 'bootstrap4',
                            dropdownParent: $(this).closest('[role="dialog"]')
                        });
                    }
                });
            });
        });

        $('#memTable').DataTable({
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
                        return '<span class="text-green-600">Lifetime</span>';
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

<!-- Sidebar Toggle -->
<style>
    #sidebar.sidebar-mobile-open {
        transform: translateX(0) !important;
    }
    #sidebar.sidebar-desktop-collapsed {
        transform: translateX(-100%) !important;
    }
</style>
<script>
function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    var topbar = document.getElementById('topbar');
    var mainContent = document.querySelector('[data-sidebar-content]');
    var mainFooter = document.getElementById('main-footer');
    var backdrop = document.getElementById('sidebar-backdrop');
    var isLg = window.innerWidth >= 1024;

    if (isLg) {
        var collapsed = sidebar.classList.toggle('sidebar-desktop-collapsed');
        if (topbar) topbar.classList.toggle('lg:ml-64');
        if (mainContent) mainContent.classList.toggle('lg:ml-64');
        if (mainFooter) mainFooter.classList.toggle('lg:ml-64');
    } else {
        var opened = sidebar.classList.toggle('sidebar-mobile-open');
        if (backdrop) backdrop.classList.toggle('hidden');
    }
}

// Clean up sidebar state on window resize across breakpoints
(function() {
    var wasLg = window.innerWidth >= 1024;
    window.addEventListener('resize', function() {
        var isLg = window.innerWidth >= 1024;
        if (wasLg !== isLg) {
            var sidebar = document.getElementById('sidebar');
            var backdrop = document.getElementById('sidebar-backdrop');
            // Reset mobile state when crossing to desktop
            sidebar.classList.remove('sidebar-mobile-open');
            sidebar.classList.remove('sidebar-desktop-collapsed');
            if (backdrop) backdrop.classList.add('hidden');
            // Restore margins
            var topbar = document.getElementById('topbar');
            var mainContent = document.querySelector('[data-sidebar-content]');
            var mainFooter = document.getElementById('main-footer');
            if (isLg) {
                if (topbar && !topbar.classList.contains('lg:ml-64')) topbar.classList.add('lg:ml-64');
                if (mainContent && !mainContent.classList.contains('lg:ml-64')) mainContent.classList.add('lg:ml-64');
                if (mainFooter && !mainFooter.classList.contains('lg:ml-64')) mainFooter.classList.add('lg:ml-64');
            }
            wasLg = isLg;
        }
    });
})();
</script>

<!-- Paginated Table IIFE -->
<script>
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        var tables = document.querySelectorAll('[data-paginated-table]');
        tables.forEach(function(table) {
            initPaginatedTable(table);
        });
    });

    function initPaginatedTable(table) {
        var perPage = parseInt(table.getAttribute('data-per-page')) || 5;
        var searchable = table.hasAttribute('data-searchable');
        var tbody = table.querySelector('tbody');
        if (!tbody) return;

        var allRows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
        var filteredRows = allRows.slice();
        var currentPage = 1;
        var searchInput = null;

        // Find the pagination controls container (sibling of overflow-x-auto wrapper)
        var wrapper = table.closest('.overflow-x-auto');
        var controlsContainer = wrapper
            ? wrapper.parentElement.querySelector('[data-pagination-controls]')
            : null;
        if (!controlsContainer) return;

        // Search input (opt-in)
        if (searchable) {
            searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'Search...';
            searchInput.className = 'mb-3 block w-full max-w-xs rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors';
            // Insert search before the overflow wrapper
            if (wrapper && wrapper.parentElement) {
                wrapper.parentElement.insertBefore(searchInput, wrapper);
            }
            searchInput.addEventListener('input', function() {
                var term = this.value.toLowerCase();
                filteredRows = allRows.filter(function(row) {
                    return row.textContent.toLowerCase().indexOf(term) !== -1;
                });
                currentPage = 1;
                render();
            });
        }

        function render() {
            var totalRows = filteredRows.length;
            var totalPages = Math.ceil(totalRows / perPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;

            var start = (currentPage - 1) * perPage;
            var end = start + perPage;

            // Show/hide rows
            allRows.forEach(function(row) { row.style.display = 'none'; });
            filteredRows.slice(start, end).forEach(function(row) { row.style.display = ''; });

            // Build controls
            controlsContainer.innerHTML = '';

            if (totalRows === 0) {
                controlsContainer.innerHTML = '<p class="text-sm text-gray-500 py-2 text-center">No matching records</p>';
                return;
            }

            if (totalPages <= 1) {
                // Show info text only, no pagination buttons
                controlsContainer.innerHTML = '<p class="text-sm text-gray-600">Showing 1\u2013' + totalRows + ' of ' + totalRows + '</p>';
                return;
            }

            var showStart = start + 1;
            var showEnd = Math.min(end, totalRows);

            var nav = document.createElement('nav');
            nav.className = 'flex flex-wrap items-center justify-between gap-2';

            // Info text
            var info = document.createElement('p');
            info.className = 'text-sm text-gray-600';
            info.textContent = 'Showing ' + showStart + '\u2013' + showEnd + ' of ' + totalRows;
            nav.appendChild(info);

            // Pagination buttons
            var btnWrap = document.createElement('div');
            btnWrap.className = 'flex items-center gap-1';

            // Previous button
            var prevBtn = createNavBtn('\u2039', currentPage <= 1);
            prevBtn.addEventListener('click', function() {
                if (currentPage > 1) { currentPage--; render(); }
            });
            btnWrap.appendChild(prevBtn);

            // Page buttons with ellipsis
            var pages = getPageNumbers(currentPage, totalPages);
            pages.forEach(function(p) {
                if (p === '...') {
                    var dots = document.createElement('span');
                    dots.className = 'px-1 text-sm text-gray-400';
                    dots.textContent = '...';
                    btnWrap.appendChild(dots);
                } else {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = p;
                    if (p === currentPage) {
                        btn.className = 'inline-flex items-center justify-center min-w-[2rem] h-8 px-2.5 text-sm font-medium rounded-lg bg-primary text-white';
                    } else {
                        btn.className = 'inline-flex items-center justify-center min-w-[2rem] h-8 px-2.5 text-sm font-medium rounded-lg text-gray-800 hover:bg-gray-100';
                    }
                    btn.addEventListener('click', (function(page) {
                        return function() { currentPage = page; render(); };
                    })(p));
                    btnWrap.appendChild(btn);
                }
            });

            // Next button
            var nextBtn = createNavBtn('\u203A', currentPage >= totalPages);
            nextBtn.addEventListener('click', function() {
                if (currentPage < totalPages) { currentPage++; render(); }
            });
            btnWrap.appendChild(nextBtn);

            nav.appendChild(btnWrap);
            controlsContainer.appendChild(nav);
        }

        function createNavBtn(text, disabled) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = text;
            btn.className = 'inline-flex items-center justify-center min-w-[2rem] h-8 px-2 text-lg font-medium rounded-lg text-gray-800 hover:bg-gray-100';
            if (disabled) {
                btn.className += ' opacity-50 cursor-not-allowed';
                btn.disabled = true;
            }
            return btn;
        }

        function getPageNumbers(current, total) {
            if (total <= 7) {
                var arr = [];
                for (var i = 1; i <= total; i++) arr.push(i);
                return arr;
            }
            var pages = [];
            pages.push(1);
            if (current > 3) pages.push('...');
            var rangeStart = Math.max(2, current - 1);
            var rangeEnd = Math.min(total - 1, current + 1);
            for (var j = rangeStart; j <= rangeEnd; j++) pages.push(j);
            if (current < total - 2) pages.push('...');
            pages.push(total);
            return pages;
        }

        render();
    }
})();
</script>

<!-- Preline UI v2 -->
<script src="https://cdn.jsdelivr.net/npm/preline@2/dist/preline.min.js"></script>

<!-- Re-init Preline + overlay close-button polyfill -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }

    // Polyfill: ensure [data-hs-overlay] close buttons work even if Preline
    // didn't fully initialize the overlay instances.
    document.addEventListener('click', function(e) {
        var trigger = e.target.closest('[data-hs-overlay]');
        if (!trigger) return;
        var target = document.querySelector(trigger.getAttribute('data-hs-overlay'));
        if (!target) return;
        // If the overlay is currently open, close it
        if (target.classList.contains('open')) {
            target.classList.add('hidden');
            target.classList.remove('open');
            document.body.style.overflow = '';
        }
    });
});
</script>

</body>

</html>
