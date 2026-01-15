<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .email-header {
            background: #070f26 !important;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .email-body {
            padding: 20px;
            text-align: center;
        }

        .email-body p {
            font-size: 16px;
            line-height: 1.6;
        }

        .reset-button {
            display: inline-block;
            margin: 20px 0;
            padding: 12px 25px;
            font-size: 16px;
            background: #070f26 !important;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .reset-button:hover {
            background: #5548c8;
            cursor: pointer;
        }

        .email-footer {
            background: #f4f4f9;
            text-align: center;
            padding: 15px;
        }

        .social-icons {
            margin: 10px 0;
        }

        .social-icons a {
            margin: 0 8px;
            text-decoration: none;
        }

        .social-icons svg {
            width: 24px;
            height: 24px;
            fill: #6c63ff;
            transition: fill 0.3s;
        }

        .social-icons svg:hover {
            fill: #5548c8;
        }
        /* Container form */
    .password-form {
        max-width: 400px;
        margin: 40px auto;
        padding: 20px;
        background: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        font-family: Arial, sans-serif;
    }

    /* Label */
    .password-form label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #333;
    }

    /* Input Field */
    .password-form input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .password-form input[type="password"]:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 5px rgba(0,123,255,0.3);
    }

    /* Submit Button */
    .btn-submit {
        width: 100%;
        padding: 10px;
        background: #007bff;
        color: white;
        border: none;
        font-size: 16px;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-submit:hover {
        background: #0056b3;
    }

        /* Responsive Styles */
@media screen and (max-width: 600px) {
    .email-container {
        width: 90%;
        margin: 10px auto;
    }

    .email-body p {
        font-size: 14px;
        line-height: 1.5;
    }

    .reset-button {
        font-size: 14px;
        padding: 10px 20px;
    }

    .social-icons svg {
        width: 20px;
        height: 20px;
    }

    .email-header h1 {
        font-size: 20px;
    }
}
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>Reset Your Password</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <form action="<?= base_url('User/submit_new_password') ?>" method="post" class="password-form">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
                    <input type="hidden" name="user_login_id" value="<?= $user_id ?>">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
                    <p id="password_message" style="font-size: 12px; margin-top: -5px; text-align: left;"></p>
                </div>

                <button type="submit" class="reset-button" id="button_submit_password" disabled>Submit</button>
            </form>



            
            
            <!-- <a href="https://yourwebsite.com/reset-password" class="reset-button">Reset Password</a> -->
        </div>

        <!-- Footer -->
        <div class="email-footer">
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