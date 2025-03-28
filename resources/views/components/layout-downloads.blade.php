<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>STL @yield('title')</title>
        {{-- <link rel="manifest" href="/manifest.json"> --}}
        <link rel="manifest" href="/manifest.webmanifest">
        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">


        <link href="https://vjs.zencdn.net/7.2.3/video-js.css" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
        <style>
             
            
            body {  
                width: 100vw;
                height: 100vh;
                background-size: cover;
                background-repeat: repeat;
                /* background-size: 100px; */
                
            } 
            .receipt{
                background-image: url('{{asset("images/296x464.png")}}'); 
                background-size: cover;
                background-repeat: no-repeat;
            }
            /* .hero{
                background-image: url('{{asset("images/downloads-bg-min.png")}}'); 
                background-size:     cover;                      /* <------ */
                background-repeat:   no-repeat;
                background-position: center center;  /
            } */
            
           
        </style>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-1FG3RNCF6P"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-1FG3RNCF6P');
        </script>
    </head>
    <body>
        <x-flash-message />
        <main class="main bg-white">
            {{$slot}}
        </main>
        {{-- <footer class="fixed bottom-0" >
                       <img src="{{URL('images/footer.png')}}" class="img-fluid main-images" alt="...">
        </footer> --}}
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>    </body>
        <script src="https://vjs.zencdn.net/ie8/ie8-version/videojs-ie8.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.14.1/videojs-contrib-hls.js"></script>
        <script src="https://vjs.zencdn.net/7.2.3/video.js"></script>

        <script>
        var player = videojs("hls-example");
        player.play();
        </script>
</html>
