<!DOCTYPE html>
<html>
<head>
    @vite('resources/css/app.css')
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body>
<div id="app"></div> @vite('resources/js/app.js')</body>
<script>
	window.config = {
		app: {
			url: '{{ url('/') }}',
			api_url: '{{ url('/api') }}',
		},
		services: {
			nominatim: {
				url: '{{ config('services.nominatim.url') }}'
            },
		},
		recaptcha: {
			api_site_key: '{{ config('recaptcha.api_site_key') }}',
		}
	};
</script>
</html>