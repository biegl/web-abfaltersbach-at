<div class="calender">
    <div class="calendar_date--big">
        <div class="calendar_date">
            <div class="calendar_date_month">{{ \Carbon\Carbon::today()->formatLocalized('%a, %b') }}</div>
            <div class="calendar_date_day">{{ \Carbon\Carbon::today()->formatLocalized('%d') }}</div>
        </div>

        <ol class="list-unstyled calendar_date--big mb-0">

            @forelse ($current_events as $event)

                <li class="media pt-3">
                    <div class="media-body calendar_day">
                        <div class="calendar_day_body">{!! $event->text !!}</div>

                        @if ($event->attachments->count() > 0)

                            <div>
                                <ul class="calendar_day_files mt-2">

                                    @foreach ($event->attachments as $file)

                                        <li>
                                            <i class="far fa-file {{ create_fa_ext_icon($file) }} mr-2 download-file__extension" title="{{ __('files.type') }}: {{ strtoupper($file->extension) }}"></i>
                                            <a href="/files/{{ $file->title }}">
                                                {{ $file->title }}
                                            </a>
                                        </li>

                                    @endforeach

                                </ul>
                            </div>

                        @endif

                        <div class="calendar_day_meta">
                        </div>
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

                        @if ($event->attachments->count() > 0)

                            <div>
                                <ul class="calendar_day_files">

                                    @foreach ($event->attachments as $file)

                                        <li>
                                            <i class="far fa-file {{ create_fa_ext_icon($file) }} mr-2 download-file__extension" title="{{ __('files.type') }}: {{ strtoupper($file->extension) }}"></i>
                                            <a href="/files/{{ $file->title }}">
                                                {{ $file->title }}
                                            </a>
                                        </li>

                                    @endforeach

                                </ul>
                            </div>

                        @endif

                        <div class="calendar_day_meta"></div>
                    </div>
                </li>

            @empty

                <li>{{ __('events.no_events_this_month') }}</li>

            @endforelse

        @empty

            @if(request()->has('eventID'))
                <li><a href="/">Alle Veranstaltungen anzeigen</a></li>
            @else
                <li>{{ __('events.no_events') }}</li>
            @endif

        @endforelse

    </ol>
</div>
