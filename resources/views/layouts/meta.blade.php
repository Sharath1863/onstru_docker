@php
    ($meta = getMetaTags())
@endphp

<!-- Meta -->
<title>{{ $meta['title'] ?? 'Onstru' }}</title>
<meta name="description" content="{{ $meta['description'] ?? 'Onstru Social' }}">
<meta name="keywords" content="{{ $meta['keywords'] ?? '' }}">

<!-- Open Graph -->
<meta property="og:title" content="{{ $meta['title'] ?? 'Onstru' }}">
<meta property="og:description" content="{{ $meta['description'] ?? 'Onstru Social' }}">
<meta property="og:image" content="{{ $meta['image'] ?? asset('assets/images/Logo_Login.png') }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $meta['title'] ?? 'Onstru' }}">
<meta name="twitter:description" content="{{ $meta['description'] ?? 'Onstru Social' }}">
<meta name="twitter:image" content="{{ $meta['image'] ?? asset('assets/images/Logo_Login.png') }}">