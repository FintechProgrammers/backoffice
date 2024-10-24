@extends('layouts.user.app')

@section('title', 'Creatives')

@section('content')
    <div id="app"></div>
@endsection
@push('scripts')
    @vite('resources/js/app.js')
@endpush
