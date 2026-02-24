<!-- Profile Modal -->
<div id="modal-profile" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto [--overlay-backdrop:static]" role="dialog">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl">

            <!-- Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">My Profile</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" data-hs-overlay="#modal-profile">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 px-5">
                <nav class="flex gap-1" aria-label="Profile Tabs">
                    <button type="button"
                            class="profile-tab py-2.5 px-3 text-sm font-medium border-b-2 transition-colors border-primary text-primary"
                            data-tab="profile-info">
                        <i class="fa-solid fa-user mr-1.5"></i>Profile Info
                    </button>
                    <button type="button"
                            class="profile-tab py-2.5 px-3 text-sm font-medium border-b-2 transition-colors border-transparent text-gray-500 hover:text-gray-700"
                            data-tab="change-password">
                        <i class="fa-solid fa-lock mr-1.5"></i>Change Password
                    </button>
                </nav>
            </div>

            <!-- Tab Content: Profile Info -->
            <div id="tab-profile-info" class="profile-tab-content px-5 py-5 overflow-y-auto max-h-[70vh]">
                <form id="formUpdateProfile">
                    <div class="form-group mb-5">
                        <label for="profile-fullname" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                        <input type="text" id="profile-fullname" name="fullname"
                               class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors"
                               placeholder="Enter your full name">
                        <span class="inline-error text-xs text-red-500 mt-1" id="error-profile-fullname"></span>
                    </div>
                    <div class="form-group">
                        <label for="profile-email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" id="profile-email" name="email"
                               class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors"
                               placeholder="Enter your email">
                        <span class="inline-error text-xs text-red-500 mt-1" id="error-profile-email"></span>
                    </div>
                </form>
            </div>

            <!-- Tab Content: Change Password -->
            <div id="tab-change-password" class="profile-tab-content hidden px-5 py-4 overflow-y-auto max-h-[70vh]">
                <form id="formChangePassword">
                    <div class="form-group mb-4">
                        <label for="profile-current-pass" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" id="profile-current-pass" name="current_password"
                               class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors"
                               placeholder="Enter current password">
                        <span class="inline-error text-xs text-red-500 mt-1" id="error-profile-current-pass"></span>
                    </div>
                    <div class="form-group mb-4">
                        <label for="profile-new-pass" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" id="profile-new-pass" name="new_password"
                               class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors"
                               placeholder="Minimum 8 characters">
                        <span class="inline-error text-xs text-red-500 mt-1" id="error-profile-new-pass"></span>
                    </div>
                    <div class="form-group mb-4">
                        <label for="profile-confirm-pass" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" id="profile-confirm-pass" name="confirm_password"
                               class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors"
                               placeholder="Re-enter new password">
                        <span class="inline-error text-xs text-red-500 mt-1" id="error-profile-confirm-pass"></span>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-200">
                <button type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-gray-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-600 transition-colors"
                        data-hs-overlay="#modal-profile">
                    Close
                </button>
                <button type="button" id="btn-save-profile" data-action="profile"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors">
                    Save Changes
                </button>
            </div>

        </div>
    </div>
</div>

<script>
(function() {
    var profileBaseUrl = '<?= base_url() ?>';

    // ── Open profile modal & load data ──────────────────────────────
    window.openProfileModal = function() {
        // Reset to Profile Info tab
        switchProfileTab('profile-info');

        // Clear password fields
        $('#profile-current-pass, #profile-new-pass, #profile-confirm-pass').val('');
        clearAllFieldErrors('#modal-profile');

        showModal('#modal-profile');

        // Fetch profile data
        $.ajax({
            url: profileBaseUrl + 'profile/get',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    var d = res.data;
                    $('#profile-fullname').val(d.Fullname);
                    $('#profile-email').val(d.Email);
                }
            }
        });
    };

    // ── Tab switching ────────────────────────────────────────────────
    function switchProfileTab(tabName) {
        $('.profile-tab').each(function() {
            var isActive = ($(this).data('tab') === tabName);
            $(this).toggleClass('border-primary text-primary', isActive)
                   .toggleClass('border-transparent text-gray-500 hover:text-gray-700', !isActive);
        });
        $('.profile-tab-content').addClass('hidden');
        $('#tab-' + tabName).removeClass('hidden');

        // Update save button
        if (tabName === 'change-password') {
            $('#btn-save-profile').text('Update Password').attr('data-action', 'password');
        } else {
            $('#btn-save-profile').text('Save Changes').attr('data-action', 'profile');
        }
    }

    $(document).on('click', '.profile-tab', function() {
        switchProfileTab($(this).data('tab'));
    });

    // ── Save button handler ─────────────────────────────────────────
    $(document).on('click', '#btn-save-profile', function() {
        var action = $(this).attr('data-action') || 'profile';
        if (action === 'profile') {
            submitProfileUpdate();
        } else {
            submitPasswordChange();
        }
    });

    // ── Profile update ──────────────────────────────────────────────
    function submitProfileUpdate() {
        clearAllFieldErrors('#tab-profile-info');

        var fullname = $('#profile-fullname').val().trim();
        var email    = $('#profile-email').val().trim();
        var hasError = false;

        if (!fullname) {
            setFieldError('profile-fullname', 'Full name is required.');
            hasError = true;
        }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            setFieldError('profile-email', 'A valid email is required.');
            hasError = true;
        }
        if (hasError) return;

        $.ajax({
            url: profileBaseUrl + 'profile/update',
            method: 'POST',
            data: { fullname: fullname, email: email },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    // Update sidebar name in real-time
                    var formatted = res.data.Fullname.toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                    $('#sidebar-user-panel p.text-sm.font-semibold').text(formatted);

                    Swal.fire({
                        icon: 'success',
                        title: 'Profile Updated',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else if (res.status === 'validation_error') {
                    $.each(res.errors, function(field, msg) {
                        setFieldError('profile-' + field, msg);
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            }
        });
    }

    // ── Password change ─────────────────────────────────────────────
    function submitPasswordChange() {
        clearAllFieldErrors('#tab-change-password');

        var currentPass = $('#profile-current-pass').val();
        var newPass     = $('#profile-new-pass').val();
        var confirmPass = $('#profile-confirm-pass').val();
        var hasError    = false;

        if (!currentPass) {
            setFieldError('profile-current-pass', 'Current password is required.');
            hasError = true;
        }
        if (!newPass) {
            setFieldError('profile-new-pass', 'New password is required.');
            hasError = true;
        } else if (newPass.length < 8) {
            setFieldError('profile-new-pass', 'Password must be at least 8 characters.');
            hasError = true;
        }
        if (newPass !== confirmPass) {
            setFieldError('profile-confirm-pass', 'Passwords do not match.');
            hasError = true;
        }
        if (hasError) return;

        $.ajax({
            url: profileBaseUrl + 'profile/change-password',
            method: 'POST',
            data: {
                current_password: currentPass,
                new_password: newPass,
                confirm_password: confirmPass
            },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#profile-current-pass, #profile-new-pass, #profile-confirm-pass').val('');

                    Swal.fire({
                        icon: 'success',
                        title: 'Password Changed',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else if (res.status === 'validation_error') {
                    $.each(res.errors, function(field, msg) {
                        var fieldId = 'profile-' + field.replace(/_/g, '-');
                        setFieldError(fieldId, msg);
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            }
        });
    }
})();
</script>
