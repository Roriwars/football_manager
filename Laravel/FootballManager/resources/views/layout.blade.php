<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href=" {{asset('css/bulma.css')}}"/>
        <link rel="stylesheet" href=" {{asset('css/bulma.min.css')}}"/>
        <title>Football Manager</title>
        <style>
            body{
                background-image:url(images/terrain_foot.png);
                background-position:center center;
            }
        </style>
    </head>
    <body>
        
        @yield('content')
    </body>
</html>