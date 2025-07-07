<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title inertia>{{ config('app.name', 'Laravel') }}</title>
  <link rel="manifest" href="/manifest.webmanifest">
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
  <link rel="icon" type="image/x-icon" href="/images/favicon.ico"> @routes
  @viteReactRefresh @vite(['resources/js/app.tsx', "resources/js/pages/{$page['component']}.jsx"]) @inertiaHead

  <style>
    .g-recaptcha {
        padding: 10px
    }
  </style>
</head>
<body class="font-sans antialiased">
  @inertia
</body>
<script>
  function onSubmit(token) {
    document.getElementById("demo-form").submit();
  }

</script>
@if (Route::current()->getName() == 'login')
  <script src='https://www.google.com/recaptcha/api.js?render=6Le8DV4rAAAAAF-ouzsNUjLykUkSRauVLJ6gfmJm'></script>
@endif
</html>
