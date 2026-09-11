@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'publishedAt' => null,
    'author' => null,
])

@php
    $siteName = config('app.name', 'Folklore');
    $fullTitle = $title ? "{$title} | {$siteName}" : $siteName;
    $fullDescription = $description ?? 'منصة الفلكلور الرقمية - اكتشف التراث والقصص الشعبية والحرف اليدوية من كل مكان.';
    $fullImage = $image ?? asset('images/og-default.jpg');
    $currentUrl = url()->current();
@endphp

{{-- ✅ Basic Meta --}}
<meta name="description" content="{{ Str::limit(strip_tags($fullDescription), 160) }}">
<meta name="author" content="{{ $author ?? $siteName }}">
<meta name="robots" content="index, follow">

{{-- ✅ Open Graph (Facebook, WhatsApp, LinkedIn) --}}
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($fullDescription), 160) }}">
<meta property="og:image" content="{{ $fullImage }}">
<meta property="og:url" content="{{ $currentUrl }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_AR' : 'en_US' }}">

{{-- ✅ Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ Str::limit(strip_tags($fullDescription), 160) }}">
<meta name="twitter:image" content="{{ $fullImage }}">

{{-- ✅ Article Specific --}}
@if($publishedAt)
    <meta property="article:published_time" content="{{ $publishedAt->toIso8601String() }}">
@endif
@if($author)
    <meta property="article:author" content="{{ $author }}">
@endif

{{-- ✅ Canonical URL --}}
<link rel="canonical" href="{{ $currentUrl }}">