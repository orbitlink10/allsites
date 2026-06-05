@extends('layouts.app')

@section('title', 'Posts | ' . get_option('site_name', 'Gamun'))
@section('meta_description', 'Browse the latest posts from ' . get_option('site_name', 'Gamun') . '.')

@section('content')
    @include('partials.insight-list', [
        'heading' => 'Posts',
        'intro' => 'Browse the latest posts and updates.'
    ])
@endsection
