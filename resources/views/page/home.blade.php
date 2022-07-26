@extends('layouts.base')

@section('content')
    @include('partials.newslist')
@endsection

@section('sidebar')
    @if (true)
        @include('partials.wahlkarten')
    @endif
    @include('partials.eventlist')
@endsection
