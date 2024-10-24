@extends('layouts.user.app')

@section('title', 'Signal')

@section('content')
    <div id="app">
        <signal-component />
    </div>
@endsection
@push('scripts')
    @vite('resources/js/app.js')
@endpush
