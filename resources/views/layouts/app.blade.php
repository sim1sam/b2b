@extends('adminlte::page')

@push('meta')
    @if(isset($favicon) && $favicon)
        <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ $favicon }}">
    @endif
@endpush

@push('js')
    @include('components.logo-link')
@endpush

