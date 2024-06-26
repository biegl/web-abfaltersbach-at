<a href="https://t.me/abfaltersbach" target="_blank" class="telegram_channel float-right">
    Follow us on Telegram
    <svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 128 128">
    <defs>
        <linearGradient id="tlogo-a" x1="50%" x2="50%" y1="0%" y2="99.258%">
        <stop offset="0%" stop-color="#2AABEE"/>
        <stop offset="100%" stop-color="#229ED9"/>
        </linearGradient>
    </defs>
    <g fill="none" fill-rule="evenodd">
        <circle cx="64" cy="64" r="64" fill="url(#tlogo-a)" fill-rule="nonzero"/>
        <path fill="#FFF" fill-rule="nonzero" d="M28.9700376,63.3244248 C47.6273373,55.1957357 60.0684594,49.8368063 66.2934036,47.2476366 C84.0668845,39.855031 87.7600616,38.5708563 90.1672227,38.528 C90.6966555,38.5191258 91.8804274,38.6503351 92.6472251,39.2725385 C93.294694,39.7979149 93.4728387,40.5076237 93.5580865,41.0057381 C93.6433345,41.5038525 93.7494885,42.63857 93.6651041,43.5252052 C92.7019529,53.6451182 88.5344133,78.2034783 86.4142057,89.5379542 C85.5170662,94.3339958 83.750571,95.9420841 82.0403991,96.0994568 C78.3237996,96.4414641 75.5015827,93.6432685 71.9018743,91.2836143 C66.2690414,87.5912212 63.0868492,85.2926952 57.6192095,81.6896017 C51.3004058,77.5256038 55.3966232,75.2369981 58.9976911,71.4967761 C59.9401076,70.5179421 76.3155302,55.6232293 76.6324771,54.2720454 C76.6721165,54.1030573 76.7089039,53.4731496 76.3346867,53.1405352 C75.9604695,52.8079208 75.4081573,52.921662 75.0095933,53.0121213 C74.444641,53.1403447 65.4461175,59.0880351 48.0140228,70.8551922 C45.4598218,72.6091037 43.1463059,73.4636682 41.0734751,73.4188859 C38.7883453,73.3695169 34.3926725,72.1268388 31.1249416,71.0646282 C27.1169366,69.7617838 23.931454,69.0729605 24.208838,66.8603276 C24.3533167,65.7078514 25.9403832,64.5292172 28.9700376,63.3244248 Z"/>
    </g>
    </svg>
</a>
<h2 class="newslist_header mb-3">{{ __('newslist.header') }}</h2>
<ol class="newslist list-unstyled">

    @forelse ($news as $news_item)

        <li class="newslist_item mb-4">
            <h3 class="newslist_item_title">{{ $news_item->title }} <small class="newslist_item_date">{{ $news_item->date->format('d.m.') }}</small></h3>

            @isset ($news_item->text)

                <div class="newslist_item_body">{!! $news_item->text !!}</div>

            @endisset

            @if ($news_item->attachments->count() > 0)

                <div>
                    <ul class="newslist_item_files">

                        @foreach ($news_item->attachments as $file)

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

        </li>

    @empty

        <li>Dieser Newseintrag existiert nicht</li>

    @endforelse

</ol>

@if (request()->has('newsID'))
    <a href="/">Alle Einträge anzeigen</a>
@endif