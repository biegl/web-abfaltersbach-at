(function ($) {
    var queryParams = [
        "id=2783062",
        "lang=de",
        "appId=eb8433667be1dbb5b880f9fb619818cd",
        "units=metric",
    ];

    var requestUrl =
        "https://api.openweathermap.org/data/2.5/weather?" +
        queryParams.join("&");

    // Load weather information
    $.get(requestUrl).then(function (data) {
        console.log(data)
        var weather = data.weather[0];

        $(".weather-widget__temperature-degrees").text(
            Math.round(data.main.temp)
        );

        $(".weather-widget__icon").html(
            '<i class="wi wi-owm-' + weather.id + '"></i>'
        );

        $(".weather-widget__description").text(weather.description);
    });
})(window.jQuery || window.$);
