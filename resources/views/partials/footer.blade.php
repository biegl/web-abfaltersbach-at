<footer class="footer">
    <div class="footer__inner">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mt-3 mt-md-0">
                <h2>Aktuelles Wetter</h2>
                <div class="weather-widget row" aria-live="polite">
                    <div class="weather-widget__temperature col">
                    <span class="weather-widget__temperature-degrees"></span>
                    <span class="weather-widget__temperature-unit">°C</span>
                    </div>
                    <div class="col">
                    <div class="weather-widget__icon"></div>
                    <div class="weather-widget__description"></div>
                    </div>
                </div>
                </div>
                <div class="col-md-4 mt-3 mt-md-0">
                <h2>Lage</h2>
                <div class="google-map">
                    <a href="https://www.google.com/maps/place/Abfaltersbach/" title="Google Maps öffnen" target="_blank">
                        <img src="https://maps.googleapis.com/maps/api/staticmap?center=Abfaltersbach&zoom=13&scale=2&size=510x246&maptype=roadmap&key=AIzaSyDujKm6Kqq5uAjJvjLR7kvnST3TgcEq8w8&format=png&visual_refresh=true&signature=0NevYTrioj5ZA6eHHgrkMxuF4mU=" alt="Google Map of Abfaltersbach">
                    </a>
                </div>
                </div>
                <div class="col-md-4 mt-3 mt-md-0">
                    <h2>Gemeinde Abfaltersbach</h2>
                    <address>
                        9913 Abfaltersbach 183<br>
                        Tirol<br>
                    </address>
                    <br>
                    <i class="fa fa-phone me-2" title="Telefon"></i> +43 (0) 4846 6210<br>
                    <i class="fa fa-fax me-2" title="Fax"></i> +43 (0) 4846 6210-5<br>
                    <i class="fas fa-envelope me-2" title="Email"></i> <a href="mailto:{!! safe_email('verwaltung@abfaltersbach.at') !!}">{!! safe_email('verwaltung@abfaltersbach.at') !!}</a><br>
                    <br><br>
                    <h3>Öffnungszeiten:</h3>
                    Montag - Freitag: 08:00 - 12:00
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                <a href="/impressum" class="me-3">Impressum</a>
                <a href="/datenschutz" class="me-3">Datenschutz</a>
                <a href="/barrierefreiheitserklaerung">Barrierefreiheitserklärung</a>
                </div>
            </div>
        </div>
    </div>
    <a href="#navigation" class="visually-hidden-focusable">Zur Navigation springen</a>
    <a href="#main-content" class="visually-hidden-focusable">Zum Inhalt springen</a>
</footer>
