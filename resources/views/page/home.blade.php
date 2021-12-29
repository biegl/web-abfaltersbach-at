@extends('layouts.base')

@section('content')
    @include('partials.newslist')
@endsection

@section('sidebar')
    @include('partials.wahlkarten')
    @include('partials.eventlist')
@endsection
