@extends('layouts.base')

@section('content')
    @if ($news)

        <h2 class="newslist_header mb-3">News</h2>
        <ol class="newslist list-unstyled">

            @foreach ($news as $news_item)

            <li class="newslist_item mb-1">
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

    @else

        <p>No news entries available</p>

    @endif
@endsection

@section('sidebar')
    @if ($grouped_events)

        <div class="calender">
            <ol class="list-unstyled">
                @foreach ($grouped_events as $month => $events)
                    <li class="media calendar_month_header mb-3">{{ $month }}</li>

                    @foreach ($events as $event)

                        <li class="media mb-3">
                            <div class="mr-3 calendar_date">
                                <div class="calendar_date_month">{{ $event->date->format('M') }}</div>
                                <div class="calendar_date_day">{{ $event->date->format('d') }}</div>
                            </div>
                            <div class="media-body calendar_day">
                                <div class="calendar_day_body">{!! $event->text !!}</div>
                                <div class="calendar_day_meta"></div>
                            </div>
                        </li>

                    @endforeach

                @endforeach

            </ol>
        </div>

    @endif
@endsection
