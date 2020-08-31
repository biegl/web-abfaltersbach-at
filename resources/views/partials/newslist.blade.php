<h2 class="newslist_header mb-3">{{ __('newslist.header') }}</h2>
<ol class="newslist list-unstyled">

    @forelse ($news as $news_item)

        <li class="newslist_item mb-4">
            <h3 class="newslist_item_title">{{ $news_item->title }} <small class="newslist_item_date">{{ $news_item->date->format('d.m.') }}</small></h3>

            @isset ($news_item->text)

                <div class="newslist_item_body">{!! $news_item->text !!}</div>

            @endisset

        </li>

    @empty

        <li>No news entries available</li>

    @endforelse

</ol>
