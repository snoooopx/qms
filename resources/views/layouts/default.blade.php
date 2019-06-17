<!doctype html>
<html>
<head>
    @include('includes.head')
</head>
<body>
    <div class="container">

        <header class="row">
            @include('includes.header')
        </header>

        <div id="main" class="row">

            @yield('content')

        </div>

        <footer class="row">
            @include('includes.footer')
        </footer>

    </div>

    <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>

    <script src="/js/main.js"></script>
</body>
</html>