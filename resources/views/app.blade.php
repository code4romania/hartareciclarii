<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <x-seo::meta />
    <!-- Google tag (gtag.js) --> <script async src="https://www.googletagmanager.com/gtag/js?id=G-B9M9JYPJV7"></script>
    <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-B9M9JYPJV7'); </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
    @routes
</head>

<body class="min-h-screen antialiased">
    @inertia
</body>

</html>
