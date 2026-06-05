@extends('layouts.app')

@section('title', 'Articles | ' . get_option('site_name', 'Gamun'))
@section('meta_description', 'Browse the latest articles from ' . get_option('site_name', 'Gamun') . '.')

@section('content')
    @include('partials.insight-list', [
        'heading' => 'Articles',
        'intro' => 'Helpful articles, guides, and updates.'
    ])
@endsection
