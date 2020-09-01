<div class="container">
    <div class="header-topbar">
        <div class="row align-items-center">

            @include('partials.breadcrumbs')

        </div>
    </div>
</div>
@if (isset($navigation))
<nav id="navigation" class="navbar navbar-expand-lg sticky-top navbar-light bg-light">
    <div class="container">
        <img src="{{ asset('images/logo/wappen_abfaltersbach.png') }}" class="navigation__logo-image" alt="Wappen der Gemeinde Abfaltersbach">
        <a href="/" class="navbar__title">Gemeinde Abfaltersbach</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

            @foreach($navigation as $topLevelItem)

                @if ($topLevelItem->hasChildren)

                    <li class="nav-item dropdown{{ ($topLevelItem->isActive) ? ' active' : '' }}">
                        <a id="dropdown-{{ $topLevelItem->ID }}" class="nav-link dropdown-toggle" href="{{ $topLevelItem->url }}" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $topLevelItem->name }}
                            <span class="sr-only">(current)</span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdown-{{ $topLevelItem->ID }}">

                            @foreach($topLevelItem->children()->visible()->orderBy('position')->get() as $child)

                                <a class="dropdown-item{{ ($child->isActive) ? ' active' : '' }}" href="{{ $child->url }}">{{ $child->name }}</a>

                            @endforeach

                        </div>
                    </li>

                @else

                    <li class="nav-item{{ ($topLevelItem->isActive) ? ' active' : '' }}">
                        <a class="nav-link" href="{{ $topLevelItem->url }}" role="button">
                            {{ $topLevelItem->name }}
                            <span class="sr-only">(current)</span>
                        </a>
                    </li>

                @endif

            @endforeach

        </ul>
        </div>
    </div>
</nav>
@endif
