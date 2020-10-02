<div class="header-titlebar header-titlebar--large header-titlebar--has-topbar header-titlebar--has-logo">
    <div class="header-titlebar__inner">
        <div class="container">
        <div class="header-titlebar__logo">
            <a href="/" class="header-titlebar__logo-link">
                <img src="{{ asset('images/logo/wappen_abfaltersbach.png') }}" class="header-titlebar__logo-image" alt="Wappen der Gemeinde Abfaltersbach">
            </a>
        </div>
        <div class="header-titlebar__text">
            <h2 class="header-titlebar__title">
                <a href="/" class="header-titlebar__title-link">Gemeinde Abfaltersbach</a>
            </h2>
            <p class="header-titlebar__description">Herzlich Willkommen im wunderschönen Pustertal</p>
        </div>
        </div>
    </div>
    <div class="header-titlebar__background header-titlebar__background--align-center header-titlebar__background--single" data-slideshow-speed="5">
        <div class="header-titlebar__background-image header-titlebar__background-image--default" style="background-image: url({{ asset('images/header/abfaltersbach.jpg') }}); "></div>
    </div>
    <span class="header-titlebar__overlay"></span>
</div>

@include('partials.navigation')
