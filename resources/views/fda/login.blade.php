<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    @viteReactRefresh
    @vite('resources/js/fdaLogin.jsx')
</head>
<body class="bg-gray-100">
    <div id="root"></div>
</body>
</html>
