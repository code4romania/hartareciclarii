@if (seo('title'))
    <title inertia>@seo('title')</title>

    @unless (seo()->hasTag('og:title'))
        {{-- If an og:title tag is provided directly, it's included in the @foreach below --}}
        <meta property="og:title" content="@seo('title')" inertia />
    @endunless
@endif

@if (seo('description'))
    <meta property="og:description" content="@seo('description')" inertia />
    <meta name="description" content="@seo('description')" inertia />
@endif

@if (seo('keywords'))
    <meta name="keywords" content="@seo('keywords')" inertia />
@endif

@if (seo('type'))
    <meta property="og:type" content="@seo('type')" inertia />
@else
    <meta property="og:type" content="website" inertia />
@endif

@if (seo('site'))
    <meta property="og:site_name" content="@seo('site')" inertia />
@endif

@if (seo('locale'))
    <meta property="og:locale" content="@seo('locale')" inertia />
@endif

@if (seo('image'))
    <meta property="og:image" content="@seo('image')" inertia />
@endif

@if (seo('url'))
    <meta property="og:url" content="@seo('url')" inertia />
    <link rel="canonical" href="@seo('url')" inertia />
@endif

@foreach (seo()->tags() as $tag)
    {!! $tag !!}
@endforeach

@foreach (seo()->extensions() as $extension)
    <x-dynamic-component :component="$extension" />
@endforeach
