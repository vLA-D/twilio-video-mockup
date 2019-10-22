<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>

<div id="app" class="container">
    <div class="row mt-5">
        <div class="col-10 border border-info p-5">
            <client-component></client-component>
        </div>
    </div>
</div>

</body>
<script src="{{ asset('js/app.js') }}" defer></script>
</html>
