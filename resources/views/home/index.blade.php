@extends('layouts.base')

@section('content')
    @if ($news)

        <div class="col-md-9 text-content">
            <h2 class="newslist_header mb-3">News</h2>
            <ol class="newslist list-unstyled">

                @foreach ($news as $news_item)

                <li class"newslist_item="" mb-1"="">
                    <h3 class="newslist_item_title">
                        {{ $news_item->title }}
                        <small class="newslist_item_date">{{ $news_item->date->format('d.m.') }}</small>
                    </h3>

                    @isset ($news_item->text)

                    <div class="newslist_item_body">
                        {!! $news_item->text !!}
                    </div>

                    @endisset

                </li>

                @endforeach

            </ol>
        </div>

    @else

        <p>No news entries available</p>

    @endif
@endsection
