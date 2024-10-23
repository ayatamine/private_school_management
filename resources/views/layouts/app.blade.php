<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(app()->getLocale() =='ar') dir="rtl" @else dir="ltr" @endif>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link
        rel="shortcut icon"
        href="{{asset('landing/assets/images/favicon.png')}}"
        type="image/x-icon"
      />
      <link rel="stylesheet" href="{{asset('landing/assets/css/swiper-bundle.min.css')}}" />
      <link rel="stylesheet" href="{{asset('landing/assets/css/animate.css')}}" />
      <link rel="stylesheet" href="{{asset('landing/assets/css/tailwind.css')}}" />
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">

      <style>

        * {
            font-family: "Noto Kufi Arabic", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
            font-variation-settings:
                "slnt" 0;
        }
        #carousel-inside{
          height: calc(100vh - 164px);
        }
        [x-cloak] { display: none !important; }

      </style>
      <!-- ==== WOW JS ==== -->
      <script src="{{asset('landing/assets/js/wow.min.js')}}"></script>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

            <livewire:layout.header />

            <!-- Page Heading -->
            {{-- @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main> --}}
            @yield('content')

            <livewire:layout.footer />
        </div>
    </body>
</html>
