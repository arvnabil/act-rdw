@php
    // Detect if content is a full HTML document (has <html> or <!DOCTYPE>)
    $isFullDocument = preg_match('/<html[\s>]/i', $safeHtml) || preg_match('/<!DOCTYPE/i', $safeHtml);
@endphp

@if($isFullDocument)
{{--
    Full HTML document mode — render the sanitized HTML as a standalone page.
    We inject our SEO meta + JSON-LD into the <head> via JS, then write the
    full document. The safeHtml has already been sanitized (scripts/inline events removed).
    We use a document.write approach with our SEO data injected first.

    Actually: for a full-document campaign, just output it directly.
    The sanitizer has already stripped scripts and dangerous attributes.
    SEO data is appended after the closing </html>.
--}}
{!! $safeHtml !!}

{{-- Inject JSON-LD after body for SEO crawlers --}}
@foreach($jsonLd as $schema)
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endforeach

@else
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO Meta --}}
    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="robots" content="{{ $meta['robots'] }}">
    <link rel="canonical" href="{{ $meta['canonical'] }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="{{ $meta['og_type'] }}">
    <meta property="og:title"       content="{{ $meta['og_title'] }}">
    <meta property="og:description" content="{{ $meta['og_description'] }}">
    <meta property="og:url"         content="{{ $meta['og_url'] }}">
    @if($meta['og_image'])
    <meta property="og:image"       content="{{ asset($meta['og_image']) }}">
    @endif

    {{-- JSON-LD Structured Data --}}
    @foreach($jsonLd as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endforeach
</head>
<body>
    {!! $safeHtml !!}
</body>
</html>
@endif