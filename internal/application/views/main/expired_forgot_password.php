<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Expired</title>
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

        .warning-icon {
            font-size: 50px;
            margin-bottom: 15px;
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
            <h1>Oops! This Link Has Expired</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="warning-icon">⚠️</div>
            <p><strong>Looks like you've already used this link...</strong></p>
            <p>No worries! It's not a bug — the reset link is just expired or already used.</p>
            <p>If you still need to reset your password, please request a new link.</p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <strong>Copyright &copy; 2025</strong>  —  All rights reserved.
        </div>
    </div>
</body>

</html>
