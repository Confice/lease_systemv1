<!DOCTYPE html>
<html
  lang="en"
  class="light-style"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('sneat/assets/') }}/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    
    <title>@yield('title', 'LeaseEase X StoreEdge')</title>
    <meta name="description" content="@yield('meta_description', 'LeaseEase Marketplace Management')" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('sneat/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />
    <!-- Custom Theme Override -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/css/custom-theme-override.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    
  <!-- Perfect Scrollbar (Sneat vendor) -->
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
  <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('sneat/assets/js/config.js') }}"></script>
  @stack('styles')
  </head>
  <body class="authentication-bg">
    @yield('content')

    <!-- Core JS -->
    <script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>

    <!-- Sneat Main -->
    <script src="{{ asset('sneat/assets/js/main.js') }}"></script>

    @stack('scripts')

    <!-- Global SweetAlert Configuration (loads after all scripts, including SweetAlert) -->
    <script src="{{ asset('sneat/assets/js/sweetalert-global-config.js') }}"></script>

    <!-- Small helper to ensure show/hide works if Sneat main.js isn't enabled for some reason -->
    <script>
      document.addEventListener('click', function(e){
        if(e.target.closest('.form-password-toggle .input-group-text')){
          const wrapper = e.target.closest('.form-password-toggle');
          const input = wrapper.querySelector('input[type="password"], input[type="text"]');
          const icon = wrapper.querySelector('.input-group-text i');
          if(input.type === 'password'){ input.type = 'text'; icon.classList.remove('bx-hide'); icon.classList.add('bx-show'); }
          else { input.type = 'password'; icon.classList.remove('bx-show'); icon.classList.add('bx-hide'); }
        }
      });
    </script>
  </body>
</html>
