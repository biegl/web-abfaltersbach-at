@extends('layouts.base')

@section('content')
    <div class="row">
        <div class="d-none d-md-block col-md-3">@include('partials.subnavigation')</div>
        <div class="col-xs-12 col-md-9">
            {!! $content !!}

            @if($inserts && count($inserts) > 0)
                @foreach($inserts as $insert)

                    @switch($insert->type)
                        @case('list')
                            @include('partials.modules.list', ['insert' => $insert])
                            @break
                        @default
                    @endswitch

                @endforeach

            @endif

        </div>
    </div>
@endsection

@section('sidebar')
    @include('partials.files')
@endsection
