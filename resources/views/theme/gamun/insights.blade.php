@extends('theme.gamun.layouts.main')

@section('title', 'Insights | ' . get_option('site_name', 'Gamun'))
@section('meta_description', 'Read the latest insights, guides, and updates from ' . get_option('site_name', 'Gamun') . '.')

@section('main')
    @include('partials.insight-list', [
        'heading' => 'Insights',
        'intro' => 'Latest updates, buying guides, and service information.'
    ])
@endsection
