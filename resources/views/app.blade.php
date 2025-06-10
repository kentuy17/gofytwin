<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title inertia>{{ config('app.name', 'Laravel') }}</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
  <link rel="icon" type="image/x-icon" href="/images/favicon.ico"> @routes
  @viteReactRefresh @vite(['resources/js/app.tsx', "resources/js/pages/{$page['component']}.jsx"]) @inertiaHead
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
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
{{-- <button class="g-recaptcha" data-sitekey="reCAPTCHA_site_key" data-callback='onSubmit' data-action='submit'>Submit</button> --}}

</html>
