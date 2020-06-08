<div class="calender">
    <div class="calendar_date--big">
        <div class="calendar_date">
            <div class="calendar_date_month">{{ \Carbon\Carbon::today()->formatLocalized('%a, %b') }}</div>
            <div class="calendar_date_day">{{ \Carbon\Carbon::today()->formatLocalized('%d') }}</div>
        </div>

        <ol class="list-unstyled calendar_date--big mb-0">

            @forelse ($current_events as $event)

                <li class="media mb-3">
                    <div class="media-body calendar_day">
                        <div class="calendar_day_body">{!! $event->text !!}</div>
                        <div class="calendar_day_meta"></div>
                    </div>
                </li>

            @empty

                <li class="calendar_day mb-3">{{ __('events.no_events_today') }}</li>

            @endforelse

        </ol>
    </div>

    <ol class="list-unstyled mt-5">

        @forelse ($grouped_events as $month => $events)

            <li class="media calendar_month_header mb-3">{{ $month }}</li>

            @forelse ($events as $event)

                <li class="media mb-3">
                    <div class="mr-3 calendar_date">
                        <div class="calendar_date_month">{{ $event->date->formatLocalized('%b') }}</div>
                        <div class="calendar_date_day">{{ $event->date->formatLocalized('%d') }}</div>
                    </div>
                    <div class="media-body calendar_day">
                        <div class="calendar_day_body">{!! $event->text !!}</div>
                        <div class="calendar_day_meta"></div>
                    </div>
                </li>

            @empty

                <li>{{ __('events.no_events_this_month') }}</li>

            @endforelse

        @empty

            <li>{{ __('events.no_events') }}</li>

        @endforelse

    </ol>
</div>
