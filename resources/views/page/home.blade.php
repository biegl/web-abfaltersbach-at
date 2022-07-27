@extends('layouts.base')

@section('content')
    @include('partials.newslist')
@endsection

@section('sidebar')
    @if (app(App\Models\GeneralSettings::class)->is_proxy_card_feature_available)
        @include('partials.wahlkarten')
    @endif
    @include('partials.eventlist')
@endsection
