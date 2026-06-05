@extends('theme.starlite.layouts.orbit_main')

@php
    $pageMetaDescription = \Illuminate\Support\Str::limit(
        \Illuminate\Support\Str::squish(strip_tags((string) ($page->meta_description ?: $page->description ?: $page->title))),
        160,
        ''
    );
    $pageImage = $page->photo ? url('/images?path=' . $page->photo) : get_option('hero_image', asset('assets/img/default-placeholder.jpg'));
@endphp

@section('title', $page->meta_title ?? $page->title)
@section('meta_description', $pageMetaDescription)

@section('og_title', $page->meta_title ?? $page->title)
@section('og_description', $pageMetaDescription)
@section('og_image', $pageImage)
@section('og_url', url()->current())
@section('og_type', 'article')

@section('twitter_title', $page->meta_title ?? $page->title)
@section('twitter_description', $pageMetaDescription)
@section('twitter_image', $pageImage)

@push('meta')
@php
    $pageBreadcrumb = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url('/'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $page->title,
                'item' => url()->current(),
            ],
        ],
    ];
@endphp
<script type="application/ld+json">@json($pageBreadcrumb, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)</script>
@endpush

@section('main')
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 text-center text-lg-start">
                <div class="text-muted small mb-3">
                    <i class="bi bi-calendar-event me-1"></i>{{ optional($page->updated_at ?: $page->created_at)->format('M d, Y') }}
                </div>
                <h1 class="display-5 fw-bold mb-3">{{ $page->title }}</h1>
                @if(!empty($page->meta_description))
                    <p class="lead text-secondary mb-4">{{ $page->meta_description }}</p>
                @endif
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a class="btn btn-lg btn-dark rounded-pill px-4 py-2 shadow" href="{{ url('shop') }}">Shop Now</a>
                    <a class="btn btn-lg btn-outline-dark rounded-pill px-4 py-2" href="{{ route('contacts') }}">Talk to an Expert</a>
                </div>
            </div>

            <div class="col-lg-6 text-center">
                <img class="img-fluid rounded shadow-lg"
                     src="{{ $pageImage }}"
                     alt="{{ $page->title }} image"
                     loading="lazy"
                     style="max-width: 90%; border-radius: 20px;"
                     onerror="this.src='{{ asset('assets/img/default-placeholder.jpg') }}'">
            </div>
        </div>
    </div>
</section>

@if(isset($medias) && $medias->count() > 0)
<section class="bg-light py-5" id="medias">
    <div class="container">
        <div class="row g-4 justify-content-center">
            @foreach($medias as $media)
                <div class="col-md-3 col-sm-6">
                    <a href="{{ asset($media->file_path) }}" class="d-block rounded overflow-hidden shadow-sm">
                        <img src="{{ asset($media->file_path) }}" alt="{{ $page->title }} media" class="img-fluid w-100" loading="lazy" style="object-fit: cover; height: 200px;">
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="py-5" id="homepage-description">
    <div class="container">
        {!! trim((string) ($pageBodyHtml ?? '')) !== '' ? $pageBodyHtml : ($page->description ?: '<p class="text-muted mb-0">Content coming soon.</p>') !!}
    </div>
</section>
@endsection
