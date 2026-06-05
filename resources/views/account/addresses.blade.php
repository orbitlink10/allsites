@extends('layouts.appbar')
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <h3 class="mb-4">Addresses</h3>
    @if(count($addresses) > 0)
        <ul class="list-group">
            @foreach($addresses as $address)
                <li class="list-group-item">
                    <h5>{{ $address->label }}</h5>
                    <p>{{ $address->address }}</p>
                    <p>{{ $address->city }}, {{ $address->state }}, {{ $address->country }}</p>
                    <p>ZIP: {{ $address->zip }}</p>
                </li>
            @endforeach
        </ul>
    @else
        <p>No saved addresses found.</p>
    @endif
@endsection
