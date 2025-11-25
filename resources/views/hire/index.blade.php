@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">


    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Filter Sidebar -->
            @include('hire.aside')
            <!-- Cards -->
            <div class="flex-cards pt-2">
                <div class="body-head mb-3">
                    <h5>Recommended Hire List For You!</h5>
                    <button class="border-0 m-0 p-0 filter-responsive" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvas-filter" aria-controls="offcanvas-filter">
                        <i class="fas fa-filter fs-4"></i>
                    </button>
                </div>

                <!-- Empty State -->
                <div class="side-cards shadow-none border-0" id="noCard"
                    style="{{ $hires->isEmpty() ? 'display: block;' : 'display: none;' }}">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoJobs.png') }}" height="200px" class="d-flex mx-auto mb-2"
                            alt="">
                        <h5 class="text-center mb-0">No Hiring Found</h5>
                        <h6 class="text-center bio">No hiring opportunities are available right now - please check back
                            later or refine your search.</h6>
                    </div>
                </div>

                <!-- Hire Table -->
                <div class="container-fluid listtable p-0 border-0">
                    <div class="table-wrapper">
                        <table class="example table" id="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Experience</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($hires as $index => $hire)
                                    <tr class="hire-row" data-location="{{ $hire['location'] }}"
                                        data-category="{{ $hire['category'] }}" data-type="{{ $hire['type'] }}"
                                        data-exp="{{ $hire['experience'] }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ asset($hire['profile_img'] ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $hire['profile_img'] : 'assets/images/Avatar.png') }}"
                                                    class="avatar-30" alt="">
                                                <span>{{ $hire['name'] }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $hire['category'] }}</td>
                                        <td>{{ $hire['experience'] }}</td>
                                        <td>{{ $hire['location'] }}</td>
                                        <td>{{ $hire['type'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($hire['signed_resume_url'])
                                                    <a href="{{ $hire['signed_resume_url'] }}" download data-bs-toggle="tooltip"
                                                        data-bs-title="Download Resume">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @else
                                                    <span>No Resume</span>
                                                @endif
                                                <a href="{{ url('user-profile/' . $hire['user_id']) }}">
                                                    <i class="fas fa-external-link" data-bs-toggle="tooltip"
                                                        data-bs-title="View Profile"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection