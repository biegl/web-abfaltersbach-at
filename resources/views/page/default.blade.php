@extends('layouts.base')

@section('content')
    <div class="row">
        <div class="col-md-3">@include('partials.subnavigation')</div>
        <div class="col-md-9">
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
