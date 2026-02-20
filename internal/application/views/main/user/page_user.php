<!-- Content Header -->
<div class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-xl font-bold text-gray-800">Rider</h1>
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
    <?php if ($this->session->flashdata('message')): ?>
        <div class="mt-2">
            <?= $this->session->flashdata('message'); ?>
        </div>
    <?php endif; ?>
</div>

<!-- Content -->
<div class="px-4 sm:px-6 lg:px-8 pb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Card Header -->
        <div class="px-5 py-4 border-b border-gray-200">

            <?php if($this->session->userdata('Role') ==1): ?>
            <form action="<?= base_url('user-list') ?>" method="post" class="mb-3">
                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                        <select name="company_select" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm select2bs4">
                            <option value="all">----- All Company -----</option>
                            <?php foreach($list_company as $val): ?>
                                <option value="<?= $val['ListCompanyID'] ?>"
                                    <?= (isset($_POST['company_select']) && $_POST['company_select'] == $val['ListCompanyID']) ? "selected" : "" ?>>
                                    <?= $val['CompanyName'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors">Submit</button>
                    </div>
                </div>
            </form>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center justify-between gap-2.5">
                <div class="flex items-center gap-2.5">
                    <form id="import_excel_form" enctype="multipart/form-data" class="flex">
                        <label for="import_excel" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer mb-0! mt-3.5">
                            <i class="fa fa-file-excel text-green-600"></i> Import
                        </label>
                        <input type="file" id="import_excel" name="import_excel" accept=".xls,.xlsx" hidden>
                    </form>

                    <a href="<?= base_url('assets/dist/Example Excel Upload Rider.xlsx') ?>"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" download>
                        <i class="fa fa-download text-primary"></i> Template
                    </a>
                </div>

                <button class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors" id="add_user" type="button">
                    <i class="fa fa-plus"></i> Add Rider
                </button>
            </div>
        </div>

        <!-- Card Body -->
        <div class="px-5 py-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm" data-paginated-table data-per-page="10" data-searchable>
                    <thead class="bg-gray-50">
                        <tr class="text-center whitespace-nowrap">
                            <th class="w-[10%] px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Company</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">User Name</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">User Role</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Email</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Phone Number</th>
                            <th class="w-[15%] px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($user as $val): ?>
                        <tr class="text-center border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3"><?= $no++ ?></td>
                            <td class="px-4 py-3"><?= $val['CompanyName'] ?></td>
                            <td class="px-4 py-3"><?= $val['Fullname'] ?></td>
                            <td class="px-4 py-3"><?= $val['UserRole'] ?></td>
                            <td class="px-4 py-3"><?= $val['Email'] ?></td>
                            <td class="px-4 py-3"><?= $val['PhoneNumber'] ?></td>
                            <td class="px-4 py-3">
                                <button data-userid="<?= $val['UserID'] ?>" class="btn-tw-warning buttonEditUser">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button data-userid="<?= $val['UserID'] ?>" data-email="<?= $val['Email'] ?>"
                                    class="btn-tw-danger buttonDeleteUser">
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

<!-- Add/Edit User Modal (Preline) -->
<div id="modal" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto [--overlay-backdrop:static]" role="dialog">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl">
            <!-- Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800" id="modalAddLabel"></h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" data-hs-overlay="#modal">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-5 py-4 overflow-y-auto max-h-[70vh]">
                <div class="mt-2 <?= ($this->session->userdata('Role') == 1) ? 'hidden' : '' ?>">
                    <span id="company_package_badge" class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">Package: -</span>
                </div>
                <form id="formAddUser" method="post">
                    <div class="form-group mb-4 <?= ($this->session->userdata('Role') != 1) ? 'hidden' : '' ?>">
                        <label for="company_selected" class="block text-sm font-medium text-gray-700 mb-1">Select Company</label>
                        <select name="company_selected" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm select2For_modal" id="company_selected" required>
                            <option value="">---- Select Company ----</option>
                            <?php foreach($list_company as $val): ?>
                                <option value="<?= $val['ListCompanyID'] ?>" data-subscribe="<?= $val['CompanySubscribe'] ?>" <?= ($this->session->userdata('CompanyID') == $val['ListCompanyID']) ? "selected" : "" ?> <?= ($this->session->userdata('Role') != 1) ? "disabled" : "" ?>><?= $val['CompanyName'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="inline-error" id="error-company_selected"></span>
                    </div>
                    <div class="form-group mb-4">
                        <input type="hidden" id="user_id" name="user_id">
                        <label for="fullname" class="block text-sm font-medium text-gray-700 mb-1">Fullname</label>
                        <input type="text" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="fullname" name="fullname" placeholder="Enter Full Name">
                        <span class="inline-error" id="error-fullname"></span>
                    </div>
                    <div class="form-group mb-4">
                        <label for="user_role" class="block text-sm font-medium text-gray-700 mb-1">Select User Role</label>
                        <select name="user_role" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm select2For_modal" id="user_role" required>
                            <option value="">Select Role</option>
                            <option value="monitor">Monitor</option>
                            <option value="field">Field</option>
                        </select>
                        <span class="inline-error" id="error-user_role"></span>
                    </div>
                    <div class="form-group mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="email" name="email" placeholder="Enter Email">
                        <span class="inline-error" id="error-email"></span>
                    </div>
                    <div class="form-group mb-4">
                        <label for="pass" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="pass" name="pass">
                        <span class="inline-error" id="error-pass"></span>
                    </div>
                    <div class="form-group mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <div class="flex">
                            <span class="input-group-text-tw inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">+63</span>
                            <input type="text" class="tw-input block w-full rounded-r-lg rounded-l-none border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors" id="phone" name="phone" placeholder="9XXXXXXXXX" maxlength="13">
                        </div>
                        <span class="inline-error" id="error-phone"></span>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-200">
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-600 transition-colors" data-hs-overlay="#modal">Close</button>
                <button type="submit" form="formAddUser" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Modal (Preline) -->
<div id="modal_delete" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto [--overlay-backdrop:static]" role="dialog">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl">
            <!-- Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800" id="modalDeleteLabel"></h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" data-hs-overlay="#modal_delete">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-5 py-4">
                <form id="formDelete" method="post">
                    <input type="hidden" id="user_id" name="user_id">
                    <input type="hidden" id="current_job" name="current_job">
                    <p id="detail_reason_delete" class="mb-0 text-sm text-gray-700"></p>
                    <span class="text-sm">Do you want to delete the rider : <strong id="email_users"></strong></span>
                </form>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-200">
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-600 transition-colors" data-hs-overlay="#modal_delete">Close</button>
                <button type="submit" form="formDelete" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery"></script>

<script>
$(document).ready(function() {
    let buttonAddUser = $('#add_user');
    let buttonEditUser = $('.buttonEditUser');
    let buttonDeleteUser = $('.buttonDeleteUser');
    let modal = $('#modal');
    let modalDelete = $('#modal_delete');
    let textHeaderModal = $("#modalAddLabel");
    let textHeaderModalDelete = $("#modalDeleteLabel");
    let formUser = $("#formAddUser");
    let formUserDelete = $("#formDelete");

    $('#phone').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // handle button add
    buttonAddUser.on('click', function(e) {
        e.preventDefault();
        showModal('#modal');
        modal.find('.container_notes').show();

        textHeaderModal.text('Add User');
        modal.find('#user_id').val('');
        modal.find('#user_role').val('');
        modal.find('#fullname').val('');
        modal.find('#email').val('');
        modal.find('#pass').val('');
        modal.find('#phone').val('');

        clearAllFieldErrors('#modal');

        formUser.attr("action", '<?= base_url('create-user') ?>')
    });

    // handle button edit
    buttonEditUser.on('click', function(e) {
        e.preventDefault();
        showModal('#modal');
        modal.find('.container_notes').hide();

        clearAllFieldErrors('#modal');

        let userID = $(this).data('userid');

        textHeaderModal.text('Edit User');
        formUser.attr("action", '<?= base_url('edit-user') ?>');

        $.ajax({
            url: '<?= base_url('User/getUser') ?>',
            method: 'post',
            data: {
                userID: userID
            },
            dataType: 'json',

            success: function(response) {
                let phoneNumber = response.PhoneNumber.replace(/^\+63/, '');
                phoneNumber = parseInt(phoneNumber);

                modal.find('#user_id').val(response.UserID);
                modal.find('#fullname').val(response.Fullname);
                modal.find('#email').val(response.Email);
                modal.find('#phone').val(phoneNumber);
                modal.find('#pass').attr('placeholder', 'Leave blank to keep current password').val('');

                let roleVal = response.UserRole ? response.UserRole.toLowerCase() : 'monitor';

                modal.find('#company_selected')
                    .val(response.ListCompanyID)
                    .trigger('change');

                applyRoleRestrictionBasedOnCompany(roleVal);
            },
        })
    })


    // handle button delete
    buttonDeleteUser.on('click', function (e) {
        e.preventDefault();

        const userID = $(this).data('userid');
        const email  = $(this).data('email');

        if (!userID) {
            alert('Invalid user');
            return;
        }

        // Reset modal state
        modalDelete.find('#detail_reason_delete').text('');
        modalDelete.find('#current_job').val('');
        modalDelete.find('#user_id').val(userID);
        modalDelete.find('#email_users').text(email);

        textHeaderModalDelete.text('Delete Rider');
        formUserDelete.attr('action', '<?= base_url("User/delete") ?>');

        showModal('#modal_delete');

        // Load job info (AJAX GET)
        $.ajax({
            url: '<?= base_url("User/get_data_user_for_delete/") ?>' + userID,
            type: 'GET',
            dataType: 'json',
            success: function (resp) {
                if (resp && resp.JobID) {
                    modalDelete.find('#detail_reason_delete')
                        .text(`This rider currently has an ongoing job: ${resp.JobName}`);
                    modalDelete.find('#current_job').val(resp.JobID);
                }
            },
            error: function () {
                console.error('Failed to fetch job data');
            }
        });
    });


    function setRoleOptions(optionsHtml, value) {
        let $role = modal.find('#user_role');
        $role.empty().html(optionsHtml);
        if (typeof value !== 'undefined' && value !== null) {
            $role.val(value);
        }
        if ($role.data('select2')) {
            $role.trigger('change.select2');
        } else {
            $role.trigger('change');
        }
    }

    function applyRoleRestrictionBasedOnCompany(targetRole = null) {
        let $sel = modal.find('#company_selected');
        let $badge = modal.find('#company_package_badge');
        let pkg = $sel.find('option:selected').data('subscribe');

        let optionsAll = '<option value="">Select Role</option><option value="monitor">Monitor</option><option value="field">Field</option>';
        let optionsMonitorOnly = '<option value="">Select Role</option><option value="monitor">Monitor</option>';

        if (typeof pkg === 'undefined' || pkg === '' || pkg === null) {
            $badge.text('Package: -').removeClass('bg-green-100 text-green-800 bg-amber-100 text-amber-800').addClass('bg-gray-100 text-gray-800');
            setRoleOptions(optionsAll, targetRole);
            return;
        }

        if (parseInt(pkg) === 1) {
            $badge.text('Package: Basic').removeClass('bg-green-100 text-green-800 bg-gray-100 text-gray-800').addClass('bg-amber-100 text-amber-800');
            let forcedRole = (targetRole === 'field') ? 'monitor' : targetRole;
            setRoleOptions(optionsMonitorOnly, forcedRole);
        } else {
            $badge.text('Package: Pro').removeClass('bg-amber-100 text-amber-800 bg-gray-100 text-gray-800').addClass('bg-green-100 text-green-800 mb-3');
            setRoleOptions(optionsAll, targetRole);
        }
    }

    modal.on('change', '#company_selected', function() {
        applyRoleRestrictionBasedOnCompany();
    });

    // When modal opens for Add, apply role restrictions
    document.querySelector('#modal').addEventListener('open.hs.overlay', function() {
        if (modal.find('#user_id').val() === "") {
            applyRoleRestrictionBasedOnCompany();
        }
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
            url: '<?= base_url("User/import_excel"); ?>',
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Upload Successful!',
                        html: response.message,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        location.reload();
                    });
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

<script>
$(document).ready(function () {

    $('#formAddUser').on('submit', function (e) {
        e.preventDefault();

        var hasError = false;
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        var company  = $('#company_selected').val();
        var fullname = $('#fullname').val().trim();
        var role     = $('#user_role').val();
        var email    = $('#email').val().trim();
        var phoneRaw = $('#phone').val().trim();

        clearAllFieldErrors('#modal');

        if ($('#company_selected').is(':visible') && !company) {
            setFieldError('company_selected', 'Please select a company.');
            hasError = true;
        }

        if (!fullname) {
            setFieldError('fullname', 'Full name is required.');
            hasError = true;
        }

        if (!role) {
            setFieldError('user_role', 'Please select a user role.');
            hasError = true;
        }

        if (!email) {
            setFieldError('email', 'Email is required.');
            hasError = true;
        } else if (!emailRegex.test(email)) {
            setFieldError('email', 'Please enter a valid email address.');
            hasError = true;
        }

        var password = $('#pass').val();
        var isCreate = $('#user_id').val() === '';

        if (isCreate && !password) {
            setFieldError('pass', 'Password is required.');
            hasError = true;
        } else if (password && password.length < 8) {
            setFieldError('pass', 'Password must be at least 8 characters.');
            hasError = true;
        }

        var digits = phoneRaw.replace(/\D/g, '');

        if (!phoneRaw) {
            setFieldError('phone', 'Phone number is required.');
            hasError = true;
        } else if (
            !(
                (digits.length === 11 && digits.startsWith('09')) ||
                (digits.length === 10 && digits.startsWith('9')) ||
                (digits.length === 12 && digits.startsWith('63'))
            )
        ) {
            setFieldError('phone', 'Please enter a valid mobile number.');
            hasError = true;
        }

        if (hasError) {
            return;
        }

        this.submit();
    });

});
</script>
<script>
$(document).ready(function () {
    if ($('.alert-success').length) {
        hideModal('#modal');
    }
});
</script>
<script>
setTimeout(function () {
    $('.alert-success').fadeOut('slow');
}, 3000);
</script>
