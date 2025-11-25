@include('layouts.header')

@include('layouts.navbar')

<main>
    @yield('content')
</main>

@include('layouts.footer')

{{-- <script src="{{ asset('assets/js/notify.js') }}"></script> --}}
