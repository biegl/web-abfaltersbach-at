<div class="calender">
    <ol class="list-unstyled">

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

                <li>No events this month</li>

            @endforelse

        @empty

            <li>No events available!</li>

        @endforelse

    </ol>
</div>
