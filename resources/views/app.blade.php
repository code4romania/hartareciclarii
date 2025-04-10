<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <x-seo::meta />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
    @routes

    <x-analytics />
</head>

<body class="min-h-screen antialiased">
    @inertia
</body>

</html>
