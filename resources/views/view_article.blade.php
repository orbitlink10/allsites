@extends('layouts.app')

@section('title', ($post->title ?? 'Article') . ' | ' . get_option('site_name', 'Gamun'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($post->meta_description ?? $post->description ?? ''), 155, ''))

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <article class="bg-white border rounded shadow-sm p-4 p-md-5">
            <h1 class="fw-bold mb-3">{{ $post->title }}</h1>
            <div class="text-muted small mb-4">
                {{ optional($post->updated_at ?: $post->created_at)->format('M d, Y') }}
            </div>

            @if(!empty($post->photo))
                @php
                    $imageUrl = \Illuminate\Support\Str::startsWith($post->photo, ['http://', 'https://', '/'])
                        ? $post->photo
                        : asset('storage/' . $post->photo);
                @endphp
                <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="img-fluid rounded mb-4" loading="lazy" decoding="async">
            @endif

            <div class="content">
                {!! $post->description !!}
            </div>
        </article>
    </div>
</section>
@endsection
