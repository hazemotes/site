<!doctype html>
<html>
    @include('layouts.header')
    <link href="{{ asset('css/home.css') }}" rel="stylesheet" type="text/css">
    <title>Clay Holt</title>
</head>
    <body>

    @include('layouts.topstuff')

    <ul>
        <li><a href="/projects" class="blinkLink">Projects</a></li>
        {{--<li><a href="/blog" class="blinkLink">Blog</a></li>--}}
    </ul>

    </body>
</html>
