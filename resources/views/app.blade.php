<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ Vite::asset('resources/images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ Vite::asset('resources/images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ Vite::asset('resources/images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ Vite::asset('resources/images/favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ Vite::asset('resources/images/favicon/safari-pinned-tab.svg') }}" color="#219537">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="theme-color" content="#ffffff">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
    @routes
</head>

<body class="min-h-screen antialiased">
    @inertia
</body>

</html>
