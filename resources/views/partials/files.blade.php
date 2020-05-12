@if ($files && count($files) > 0)

    <div class="card">
        <div class="card-header">
            <h4 class="card-header__title">{{ __('files.header') }}</h4>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">

                @foreach ($files as $file)

                    <li class="media mb-2 download-file__item">
                        <i class="far fa-file {{ create_fa_ext_icon($file) }} mr-2 download-file__extension" title="{{ __('files.type') }}: {{ strtoupper($file->extension) }}"></i>
                        <div class="media-body">
                            <a href="https://files.abfaltersbach.at/{{ str_replace('/upload/', '', $file->file) }}" target="_blank">
                                <div class="download-file__name">{{ $file->title }}</div>
                            </a>

                            @if ($file->fileSize)

                                <small class="download-file__size"><span class="sr-only">{{ __('files.filesize') }}</span> {{ $file->fileSize }}</small>

                            @endif

                        </div>
                    </li>

                @endforeach

            </ul>
        </div>
    </div>

@endif
