@extends('layouts.app')

@section('title', 'Onstru | Application Submitted')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">

    <style>
        .flex-cards {
            height: 80vh;
        }
    </style>

    <div class="container main-div">
        <div class="flex-cards d-flex align-items-center justify-content-center flex-column mx-auto">
            <div class="side-cards border-0 shadow-none">
                <img src="{{ asset('assets/images/success.png') }}" height="100px" class="d-flex mx-auto mb-4"
                    alt="">
                <h5 class="mb-3 text-center">Application Submitted Successfully</h5>
                @if (session('success'))
                    <h6 class="mb-4 text-center">
                        Thank you for applying for the <span class="fw-bold">{{ session('jobTitle') }}</span> role.
                        Our team will review your application and get back to you shortly.
                    </h6>
                @endif

                <a href="{{ route('jobs') }}" class="d-flex align-items-center justify-content-center">
                    <button class="formbtn">Back to Jobs</button>
            </div>
            </a>
        </div>
    </div>

@endsection
