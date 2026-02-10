<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Expired</title>
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
    <div class="max-w-xl mx-auto my-5 bg-white rounded-lg shadow-lg overflow-hidden text-center">
        <!-- Header -->
        <div class="bg-primary text-white p-6">
            <h1 class="text-2xl font-bold m-0">Oops! This Link Has Expired</h1>
        </div>

        <!-- Body -->
        <div class="p-8">
            <div class="text-5xl mb-4">&#x26A0;&#xFE0F;</div>
            <p class="text-base leading-relaxed"><strong>Looks like you've already used this link...</strong></p>
            <p class="text-base leading-relaxed">No worries! It's not a bug — the reset link is just expired or already used.</p>
            <p class="text-base leading-relaxed">If you still need to reset your password, please request a new link.</p>
        </div>

        <!-- Footer -->
        <div class="bg-gray-100 text-center p-4 text-sm text-gray-500">
            <strong>Copyright &copy; 2025</strong>  —  All rights reserved.
        </div>
    </div>
</body>

</html>
