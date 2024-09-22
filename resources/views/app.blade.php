<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--{{ config('app.name', 'Laravel') }}-->
    <title>iMarket</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    
    
</head>
<body>
    <div id="app">
        <form action="{{route('product.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            @method ('post')
            image:
            <input type="file">
            <button type="submit">submit</button>
        </form>
    
</body>
</html>
