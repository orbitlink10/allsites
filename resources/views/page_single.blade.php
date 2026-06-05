@extends('layouts.app')

@php
    $pageMeta = \Illuminate\Support\Str::limit(\Illuminate\Support\Str::squish(strip_tags($page->meta_description ?: $page->description ?: $page->title)), 155, '');
@endphp

@section('title')
    {{ $page->title }} | {{ get_option('site_name', 'Gamun') }}
@endsection

@section('meta_description', $pageMeta)

@section('content')
<section class="container py-5 cms-page-content" style="display:block!important;visibility:visible!important;opacity:1!important;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <article class="bg-white p-4 p-md-5 rounded-4 shadow-sm" style="display:block!important;visibility:visible!important;opacity:1!important;">
                <h1 class="fw-bold mb-4">{{ $page->title }}</h1>
                <div class="text-muted small mb-3">
                    <i class="bi bi-calendar-event me-1"></i>{{ optional($page->updated_at ?: $page->created_at)->format('M d, Y') }}
                </div>
                <div class="content fs-5">
                    {!! $pageBodyHtml ?? $page->description !!}
                </div>
            </article>
            @if(!empty($page->photo))
                <div class="mt-4 text-center">
                    <img src="/images?path={{ $page->photo }}" alt="{{ $page->title }}" class="img-fluid rounded shadow-sm" style="max-height: 420px; width: auto; object-fit: contain;" loading="lazy" onerror="this.src='{{ asset('assets/images/home.png') }}'">
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
