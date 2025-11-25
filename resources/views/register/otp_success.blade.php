@extends('layouts.app')

@section('title', 'Onstru | OTP Verification Success')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">

    <div class="login-main">
        <div class="login-div">
            <div class="login-left">
                <img src="{{ asset('assets/images/Portal_OTP_Success.png') }}" class="d-flex mx-auto" width="90%" alt="">
            </div>
            <div class="login-right mx-auto">
                <div class="login-head">
                    <h5 class="text-center">OTP Success</h5>
                    <h6 class="text-center">Congratulation, You have been Successfully Authenticated</h6>
                </div>
                <div class="login-form">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-xl-12 mb-4">
                            <img src="{{ asset('assets/images/success.png') }}" class="d-flex mx-auto" height="150px"
                                alt="">
                        </div>
                        <div class="col-sm-12 col-md-12 col-xl-12">
                            <a href="{{ route('home') }}">
                                <button type="submit" class="loginbtn w-100">Continue To Home</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection