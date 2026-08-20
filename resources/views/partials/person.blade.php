<div class="media mb-5">
    @if($person->image)
        <img class="person-image me-3" src="{{ $person->image->downloadPath }}" alt="{{ $person->name }}">
    @else
        <div class="person-placeholder">
            <i class="fas fa-user-circle"></i>
        </div>
    @endif
    <div class="media-body">
        <h2 class="person-name">{{ $person->name }}</h2>

        @if($person->role)
            <h3 class="person-role mb-2 text-muted">{{ $person->role }}</h3>
        @endif

        @if($person->phone || $person->email)

            <p class="person-contact">

                @if($person->phone)
                    <i class="fas fa-phone-square-alt" title="Telefon"></i>&nbsp;{{ $person->phone }}<br/>
                @endif
                @if($person->email)
                    <i class="fas fa-envelope" title="Email"></i>&nbsp;<a href="mailto: {!! safe_email($person->email) !!}">{!! safe_email($person->email) !!}</a>
                @endif

            </p>

        @endif

    </div>
</div>
