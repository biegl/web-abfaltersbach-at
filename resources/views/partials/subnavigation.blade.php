    <nav aria-label="subnavigation">
        <ol class="subnavigation" itemscope>

            @forelse($subnavigation as $navItem)

                <li class="nav-item{{ $navItem->isActive ? ' active' : '' }}">
                    <a href="{{ $navItem->url }}" itemprop="url">{{ $navItem->name }}</a>
                </li>

            @empty

            @endforelse

        </ol>
    </nav>
