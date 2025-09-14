<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Auth')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Background gradasi animasi */
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #70392b, #79553a, #455779, #4d7e7a);
            background-size: 400% 400%;
            animation: gradientMove 15s ease infinite;
        }

        @keyframes gradientMove {
            0% { background-position: 100% 0%; }
            50% { background-position: 0% 100%; }
            100% { background-position: 100% 0%; }
        }
    </style>
</head>
<body>

    <main class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
