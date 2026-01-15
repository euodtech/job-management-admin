<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Updated</title>
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
            text-align: center;
        }

        .email-header {
            background: #070f26 !important;
            color: white;
            padding: 25px;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .email-body {
            padding: 30px;
        }

        .email-body p {
            font-size: 16px;
            line-height: 1.6;
        }

        .success-icon {
            font-size: 50px;
            color: green;
            margin-bottom: 15px;
        }

        .reset-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            font-size: 16px;
            background: #070f26 !important;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
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
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>Password Updated</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="success-icon">✅</div>
            <p><strong>Your password has been successfully updated.</strong></p>
            <p>You can now login using your new password.</p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <strong>Copyright &copy; 2025</strong>  —  All rights reserved.
        </div>
    </div>
</body>

</html>
