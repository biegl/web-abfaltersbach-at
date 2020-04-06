@if ($extension)
    <i class="fileicon fileicon--{{ $extension }}"></i>&nbsp;
@else
    <i class="fileicon"></i>&nbsp;
@endif
<a href="{{ $url }}">{{ $title }}</a>
