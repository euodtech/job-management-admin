<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>

    <!-- Tailwind CSS v3 CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['Inter', 'sans-serif'] },
                colors: {
                    primary: { DEFAULT: '#070f26', dark: '#0a1431' },
                },
            },
        },
    }
    </script>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">

    <style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }
    input:checked+.slider {
        background: #f00 !important;
    }
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
    }
    input:checked+.slider {
        background-color: #2196F3;
    }
    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }
    input:checked+.slider:before {
        transform: translateX(26px);
    }
    .slider.round {
        border-radius: 34px;
    }
    .slider.round:before {
        border-radius: 50%;
    }
    </style>
</head>

<body class="bg-gray-100 font-sans min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-center mb-8">Choose Outlet</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <form method="post" action="<?php echo base_url('ChooseOutlet/choose_redirect'); ?>">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-800 hidden" id="alert-<?=$key['OutletID']?>" role="alert">
                            <div id=""></div>
                        </div>
                        <div class="text-center">
                            <div class="inline-block border-[3px] border-gray-300 rounded-[30%] h-[150px] w-[150px] overflow-hidden mx-auto">
                                <img src="https://www.nicepng.com/png/detail/204-2046737_barber-shop-comments-barbershop-icon-png.png"
                                    alt="Outlet Logo" class="w-full h-full object-cover">
                            </div>
                            <h5 class="text-base font-semibold text-center mt-3">ALL OUTLET</h5>
                            <input type="hidden" value="all" name="storeid">
                            <input type="hidden" value="ALL OUTLET" name="storename">
                            <button type="submit"
                                class="w-full mt-4 rounded-lg bg-[#deba44] px-4 py-2 text-sm font-medium text-white hover:bg-[#c9a93a] transition-colors">
                                Choose
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <?php foreach ($store->result_array() as $key) { ?>
            <div>
                <form method="post" action="<?php echo base_url('ChooseOutlet/choose_redirect'); ?>">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-800 hidden" id="alert-<?=$key['OutletID']?>" role="alert">
                            <div id="message-<?=$key['OutletID']?>"></div>
                        </div>
                        <div class="text-center">
                            <div class="inline-block border-[3px] border-gray-300 rounded-[30%] h-[150px] w-[150px] overflow-hidden mx-auto">
                                <img src="https://www.nicepng.com/png/detail/204-2046737_barber-shop-comments-barbershop-icon-png.png"
                                    alt="Outlet Logo" class="w-full h-full object-cover">
                            </div>
                            <h5 class="text-base font-semibold text-center mt-3"><?php echo $key['MasterOutletName']?></h5>
                            <input type="hidden" value="<?php echo $key['MasterOutletID']; ?>" name="storeid">
                            <input type="hidden" value="<?php echo $key['MasterOutletName']; ?>" name="storename">
                            <button type="submit"
                                class="w-full mt-4 rounded-lg bg-[#deba44] px-4 py-2 text-sm font-medium text-white hover:bg-[#c9a93a] transition-colors">
                                Choose
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <?php }?>
        </div>

        <script src="<?php echo base_url('assets/admin/plugins/jquery/jquery.min.js'); ?>"></script>
        <script>
        function changeStatus(id) {
            if ($('#toko-' + id).prop("checked") == true) {
                var status = 0;
            } else {
                var status = 1;
            }
            $.ajax({
                url: "<?php echo base_url('outlet/statusoutlet'); ?>",
                method: "post",
                data: {
                    status: status,
                    outletid: id
                },
                dataType: "json",
                success: function(data) {
                    $("#message-" + id).text("Outlet status updated successfully");
                    $('#alert-' + id).removeClass('hidden');
                    $("#alert-" + id).fadeTo(2000, 500).slideUp(500, function() {
                        $("#alert-" + id).slideUp(500);
                    });
                }
            })
        }
        </script>
    </div>
</body>
</html>
