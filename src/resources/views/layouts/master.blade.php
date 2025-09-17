<!doctype html>
<html lang="ca">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>

    @vite(['resources/scss/main.scss', 'resources/js/app.js'])
</head>

<body>
    <header>
        <x-navigation />
    </header>
    <main>
        <section>
            @yield('content')
        </section>
    </main>
    <script src="https://kit.fontawesome.com/ae2976ddfe.js" crossorigin="anonymous"></script>
</body>

</html>
