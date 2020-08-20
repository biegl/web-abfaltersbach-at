@extends('layouts.base')

@section('content')
    <div class="row">
    <div class="col-md-3">@include('partials.subnavigation')</div>
        <div class="col-md-9">{!! $content !!}</div>
    </div>
@endsection

@section('sidebar')
    @include('partials.files')
@endsection
