<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: { DEFAULT: '#070f26', dark: '#0a1431' },
                    accent: { DEFAULT: '#4e54c8', light: '#8f94fb' },
                },
            },
        },
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
</head>

<body class="bg-gray-100 text-gray-800 font-['Inter',sans-serif] m-0 p-0">
    <div class="max-w-xl mx-auto my-5 bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-primary text-white text-center p-5">
            <h1 class="text-2xl font-bold m-0">Reset Your Password</h1>
        </div>

        <!-- Body -->
        <div class="p-5 text-center">
            <form action="<?= base_url('Auth/submit_new_password') ?>" method="post" class="max-w-sm mx-auto p-5 bg-white border border-gray-200 rounded-lg shadow-sm text-left">
                <?php if (config_item('csrf_protection')): ?>
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <?php endif; ?>
                <div class="mb-4">
                    <label for="new_password" class="block font-semibold text-gray-700 mb-1.5">New Password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    <input type="hidden" name="user_login_id" value="<?= $user_id ?>">
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="block font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    <p id="password_message" class="text-xs mt-1"></p>
                </div>

                <button type="submit" class="inline-block my-5 px-6 py-3 bg-primary text-white rounded-md hover:bg-accent transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed" id="button_submit_password" disabled>Submit</button>
            </form>
        </div>

        <!-- Footer -->
        <div class="bg-gray-100 text-center p-4 text-sm text-gray-500">
            <strong>Copyright &copy; 2025</strong>  —  All rights reserved.
        </div>
    </div>
    <script>
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const message = document.getElementById('password_message');
    const buttonSubmit = document.getElementById('button_submit_password');

    confirmPassword.addEventListener('keyup', function() {
        if (confirmPassword.value.length === 0) {
            message.textContent = '';
            return;
        }

        if (newPassword.value === confirmPassword.value) {
            message.style.color = 'green';
            message.textContent = 'Password cocok';
            buttonSubmit.classList.remove('disabled');
            buttonSubmit.disabled = false;
        } else {
            message.style.color = 'red';
            message.textContent = 'Password tidak cocok';
            buttonSubmit.classList.add('disabled');
            buttonSubmit.disabled = true;
        }
    });
</script>


</body>

</html>
