@php
    $heading = $heading ?? 'Insights';
    $intro = $intro ?? 'Latest updates, guides, and announcements.';
@endphp

<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-end mb-4">
            <div class="col-lg-8">
                <h1 class="fw-bold mb-2">{{ $heading }}</h1>
                <p class="text-muted mb-0">{{ $intro }}</p>
            </div>
        </div>

        @if($posts->count() > 0)
            <div class="row g-4">
                @foreach($posts as $post)
                    @php
                        $image = $post->photo ?? null;
                        $imageUrl = $image
                            ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/']) ? $image : asset('storage/' . $image))
                            : get_option('hero_image', asset('assets/images/home.png'));
                        $description = \Illuminate\Support\Str::limit(strip_tags($post->meta_description ?? $post->description ?? ''), 150);
                        $href = !empty($post->slug) ? url($post->slug) : '#';
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <article class="h-100 bg-white border rounded shadow-sm overflow-hidden">
                            <a href="{{ $href }}" class="d-block">
                                <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="w-100" style="height: 220px; object-fit: cover;" loading="lazy" decoding="async">
                            </a>
                            <div class="p-4">
                                <div class="text-muted small mb-2">
                                    {{ optional($post->updated_at ?: $post->created_at)->format('M d, Y') }}
                                </div>
                                <h2 class="h5 fw-bold mb-2">
                                    <a href="{{ $href }}" class="text-dark text-decoration-none">{{ $post->title }}</a>
                                </h2>
                                @if($description)
                                    <p class="text-muted mb-3">{{ $description }}</p>
                                @endif
                                <a href="{{ $href }}" class="btn btn-outline-primary btn-sm">Read More</a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        @else
            <div class="bg-white border rounded p-5 text-center">
                <h2 class="h4 fw-bold mb-2">No posts found</h2>
                <p class="text-muted mb-0">Please check back soon for new updates.</p>
            </div>
        @endif
    </div>
</section>
