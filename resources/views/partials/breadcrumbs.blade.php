<nav aria-label="breadcrumb" class="col">
    <ol class="breadcrumb" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">

        @foreach($breadcrumbs as $crumb)

            <li class="breadcrumb-item">

                @if($loop->last)

                    <span itemprop="title" aria-current="page">{{ $crumb->name }}</span>

                @else

                    <a href="{{ $crumb->url }}" itemprop="url"><span itemprop="title">{{ $crumb->name }}</span></a>

                @endif

            </li>

        @endforeach

    </ol>
</nav>
