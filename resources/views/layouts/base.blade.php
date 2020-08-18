<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Gemeinde Abfaltersbach</title>

        <link rel="icon" href="{{ asset('images/favicon.ico') }}">

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css" crossorigin="anonymous">
        <link rel="stylesheet"  href="//fonts.googleapis.com/css?family=Source+Sans+Pro%3A400%2C400italic%2C600%2C600italic%2C700%2C700italic%7CLora%3A400%2C400italic%2C700%2C700italic&#038;ver=5.1.1" type="text/css" media="all">
        <link href="/css/app.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        @if (!isset($_COOKIE['ga-disabled']))
            <script>
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                ga('create', 'UA-44166088-1', 'abfaltersbach.at');
                ga('send', 'pageview');
            </script>
        @endif
    </head>
    <body>
        <a href="#main-content" class="sr-only">Zum Inhalt springen</a>
        @include('partials.disclaimer')

        @include('partials.header')

        <section id="main-content" class="content">
            <div class="container">
                <div class="row">
                    <div class="col-md-9 text-content">
                        @yield('content')
                    </div>
                    <div class="col-md-3">
                        <aside class="sidebar">
                            @yield('sidebar')
                        </aside>
                    </div>
                </div>
            </div>
        </section>

        @include('partials.footer')

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="{{ mix('js/app.js') }}"></script>

    </body>
</html>
