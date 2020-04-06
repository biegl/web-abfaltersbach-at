<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Gemeinde Abfaltersbach</title>

        <link rel="stylesheet"  href="//fonts.googleapis.com/css?family=Source+Sans+Pro%3A400%2C400italic%2C600%2C600italic%2C700%2C700italic%7CLora%3A400%2C400italic%2C700%2C700italic&#038;ver=5.1.1" type="text/css" media="all">
        <link href="/css/app.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>

        @include('partials.header')

        <section class="content">
            <div class="container">
                <div class="row">
                    <div class="col-md-9 text-content">
                        @yield('content')
                    </div>
                    <aside class="col-md-3">
                        @yield('sidebar')
                    </aside>
                </aside>
            </div>
        </section>

        @include('partials.footer')

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="/js/app.js"></script>

    </body>
</html>
