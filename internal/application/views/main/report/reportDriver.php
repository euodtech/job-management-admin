<style>
    /* Child row styles for DataTables responsive mode */
    #tableUserPerformance tr.child td {
        text-align: left !important;
        background-color: #ffffffff;
        white-space: nowrap;
    }

    table.dataTable>tbody>tr.child span.dtr-title {
        display: inline-block;
        font-weight: bold;
    }

    /* Mobile & Tablet (< 992px) */
    @media (max-width: 991.98px) {
        table.dataTable>tbody>tr.child ul.dtr-details {
            display: flex;
            list-style-type: none;
            margin: 0;
            padding: 0;
            align-content: flex-start;
            flex-wrap: nowrap;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
        }

        table.dataTable>tbody>tr.child ul.dtr-details>li {
            border-bottom: 1px solid #efefef;
            padding: 0.5em 0;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: flex-start;
            align-items: flex-start;
        }

        table.dataTable>tbody>tr.child span.dtr-data {
            text-align: center !important;
            width: 100px !important;
            margin-left: 100px;
            flex: 3;
        }
    }
</style>

<div class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-xl font-bold text-gray-800"><?= $title ?></h1>
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

<div class="px-4 sm:px-6 lg:px-8 pb-6">
    <div class="bg-white rounded-xl shadow-sm border border-cyan-200">
        <div class="px-5 py-4 border-b border-cyan-200">
            <h5 class="text-base font-semibold text-gray-800">📌 Rider Report</h5>
        </div>
        <div class="px-5 py-4">
            <form id="formFilterUserLoginActivityReport" method="GET" action="">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" name="from_UserLoginActivityReport" id="from_UserLoginActivityReport" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" value="<?= (isset($_GET['from_UserLoginActivityReport'])) ? $_GET['from_UserLoginActivityReport'] : date('Y-m-01') ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Until Date</label>
                        <input type="date" name="until_UserLoginActivityReport" id="until_UserLoginActivityReport" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" value="<?= (isset($_GET['until_UserLoginActivityReport'])) ? $_GET['until_UserLoginActivityReport'] : date('Y-m-d')?>" >
                    </div>
                    <div class="self-end mt-3 flex items-end gap-2">
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors"><svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg> Filter</button>
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" id="resetFilterUserLoginActivityReport">Reset</button>
                    </div>
                </div>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full text-sm dt-responsive nowrap" width="100%" id="tableUserLogin">
                    <thead class="bg-gray-50">
                        <tr class="text-center whitespace-nowrap">
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Fullname</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Email</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Cancel Job</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Total Job</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Complete Job</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Ongoing Job</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>
</div>


<div id="modal_detail_job" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modal_detail_job_header">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-4xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl pointer-events-auto">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800" id="modal_detail_job_header"></h3>
                <button type="button" class="inline-flex items-center justify-center size-8 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition" data-hs-overlay="#modal_detail_job">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 overflow-y-auto">
            </div>
        </div>
    </div>
</div>

<script>

    // User Login Activity
    $(document).ready(function() {

        let form_date_until = $('#from_UserLoginActivityReport').val();
        $('#until_UserLoginActivityReport').attr('min', form_date_until);

        $('#from_UserLoginActivityReport').on('change', function() {
            let fromDate = $(this).val();
            $('#until_UserLoginActivityReport').attr('min', fromDate); // batas bawah until date
        });

        $('#until_UserLoginActivityReport').on('change', function() {
            let untilDate = $(this).val();
            let fromDate = $('#from_UserLoginActivityReport').val();

            if (untilDate < fromDate) {
                alert('Until Date cannot be earlier than From Date.');
                $(this).val(fromDate); // reset jadi sama dengan from date
            }
        });

        let today = new Date();
        let now = `${(today.getMonth()+1).toString().padStart(2, '0')}_${today.getDate().toString().padStart(2, '0')}_${today.getFullYear()}`;
        let fileName = `User_Login_Activity_${now}`;

        var table = $('#tableUserLogin').DataTable({
            processing: true,
            serverSide: true,
            order: [[1, 'asc']],
            columnDefs: [
                { targets: [3, 4, 5, 6], orderable: false } // kolom 3–6 gak bisa di-sort
            ],
            ajax: {
                url: "<?= base_url('ReportDriver/UserLoginActivityReport') ?>",
                type: "GET",
                data: function(d) {
                    d.from_UserLoginActivityReport = $('input[name=from_UserLoginActivityReport]').val();
                    d.until_UserLoginActivityReport = $('input[name=until_UserLoginActivityReport]').val();
                },
            },
            columns: [
                { data: "no", className: "text-center" },
                { data: "Fullname" },
                { data: "Email" },
                {
                    "data": "CancelJob",
                    "className": "text-center",
                    "render":  function(data, type, row) {

                        let total = 0;
                        let jobID = '';

                        // Pastikan data itu array
                        if (Array.isArray(data) && data.length > 0) {
                            // return "Yes";

                            data.forEach(element => {
                                total += 1
                                jobID += element.JobID + ', ';
                            });

                            jobID = jobID.replace(/, $/, '');
                        } else {

                            jobID = 0;

                        }



                        return `<button type='button' class='btn-tw-danger btn_detail_cancel_job' data-job-id='${jobID}' >${total}</button>`;

                    }
                },
                { "data": "TotalJob", className: "text-center" },
                {
                    "data": "CompleteJob",
                    "className": "text-center",
                    "render" : function(data, type, row) {
                        return `<button type='button' class='btn-tw-primary btn_detail_complete_job' data-user-id='${row.UserID}' data-from-date='${row.FromDate}' data-until-date='${row.UntilDate}'>${data}</button>`;

                    }
                },
                {
                    "data": "OngoingJob",
                    "className": "text-center" ,
                    "render" : function(data, type, row) {
                        return `<button type='button' class='btn-tw-primary btn_detail_ongoing_job' data-user-id='${row.UserID}' data-from-date='${row.FromDate}' data-until-date='${row.UntilDate}'>${data}</button>`;
                    }
                }

            ],
            responsive: false,
            scrollX: true ,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    title: `Rider Report (${today.getDate().toString().padStart(2, '0')}/${(today.getMonth()+1).toString().padStart(2, '0')}/${today.getFullYear()})`,
                    filename: fileName,
                    className: 'btn-tw-primary',
                    customize: function (xlsx) {
                        try {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];

                            // Tambahkan padding via spasi agar tidak dempet
                            $('row c t', sheet).each(function () {
                                var text = $(this).text();
                                $(this).text('  ' + text + '  ');
                            });

                            // Lebarkan kolom (default 25 karakter)
                            $('col', sheet).attr('width', 25);
                        } catch (e) {
                            console.warn('⚠️ Failed to modify Excel XML:', e.message);
                        }
                    },
                },
            ],
            searching: true,
        });

        // Reload Otomatic
        $('#from_UserLoginActivityReport, #until_UserLoginActivityReport').on('change', function() {
            $('#tableUserLogin').DataTable().ajax.reload();
        });

        // Submit Filter
        $('#formFilterUserLoginActivityReport').on('submit', function(e){
            e.preventDefault();
            table.ajax.reload();
        });

        // Reset Filter
        $('#resetFilterUserLoginActivityReport').on('click', function() {
            $('#formFilterUserLoginActivityReport')[0].reset();
            $('#tableUserLogin').DataTable().ajax.reload();
            const untilInput = document.querySelector('input[name="until_UserLoginActivityReport"]');
            untilInput.removeAttribute("min");
        });
    });

    $(document).on("click", ".btn_detail_complete_job", function() {

        const userID = $(this).data('user-id'); // ambil ID job dari tombol
        const fromDate = $(this).data('from-date');
        const untilDate = $(this).data('until-date');
        // Tampilkan modal dulu
        showModal('#modal_detail_job');
        $('#modal_detail_job #modal_detail_job_header').text('Detail Complete Job');



        // Load konten dari controller (kirim jobId ke backend)
        $("#modal_detail_job .p-4.overflow-y-auto").load("<?= base_url('ReportDriver/detail_job/') ?>" + userID + "/2/" + fromDate + "/" + untilDate );
    });

    $(document).on("click", ".btn_detail_ongoing_job", function() {

        const userID = $(this).data('user-id');
        const fromDate = $(this).data('from-date');
        const untilDate = $(this).data('until-date');
        // Tampilkan modal dulu
        showModal('#modal_detail_job');
        $('#modal_detail_job #modal_detail_job_header').text('Detail Ongoing Job');



        // Load konten dari controller (kirim jobId ke backend)
        $("#modal_detail_job .p-4.overflow-y-auto").load("<?= base_url('ReportDriver/detail_job/') ?>" + userID + "/1/" + fromDate + "/" + untilDate );
    });

    $(document).on("click", ".btn_detail_cancel_job", function() {

        const jobID = $(this).data('job-id');
        // Tampilkan modal dulu
        showModal('#modal_detail_job');
        $('#modal_detail_job #modal_detail_job_header').text('Detail Cancel Job');

        // Load konten dari controller (kirim jobId ke backend)
        $("#modal_detail_job .p-4.overflow-y-auto").load("<?= base_url('ReportDriver/detail_job_cancel?job_id=') ?>"  + encodeURIComponent(jobID));
    });

</script>