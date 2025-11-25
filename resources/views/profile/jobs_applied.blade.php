@extends('layouts.app')

@section('title', 'Onstru | Jobs Applied')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">

    <style>
        .flex-sidebar {
            display: block !important;
        }

        @media screen and (min-width: 1024px) {
            .flex-side {
                grid-template-columns: 35% 64% !important;
            }
        }
    </style>

    <div class="container-xl main-div">
        <div class="body-head mb-3">
            <a href="javascript:history.back()">
                <h5><i class="fas fa-angle-left pe-1"></i> Back</h5>
            </a>
        </div>
        <div class="flex-side">
            <!-- Flex Left -->
            <div class="flex-sidebar border-0">
                <div class="flex-cards">

                    <div class="side-cards mb-3">
                        <div class="cards-head">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="mb-2">{{ $job->title ?? '-' }}</h5>
                                <button
                                    class="toggle-job-status-btn mb-2 {{ $job->status === 'active' ? 'green-label' : 'red-label' }}"
                                    data-id="{{ $job->id }}">
                                    {{ ucfirst($job->status) }}
                                </button>
                            </div>
                            <h6 class="mb-2">
                                {{ $job->categoryRelation->value ?? '-' }}
                            </h6>
                        </div>
                        <div class="cards-content mt-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                <h6 class="mb-2 d-flex align-items-center gap-2">
                                    <i class="fas fa-briefcase"></i>
                                    {{ $job->experience == 0 ? 'Fresher' : $job->experience . '+Year' }}
                                </h6>
                                <h6 class="mb-2 d-flex align-items-center gap-2">
                                    <i class="fas fa-indian-rupee-sign"></i>
                                    {{ $job->salary ?? '-' }}
                                </h6>
                                <h6 class="mb-2 d-flex align-items-center gap-2">
                                    <i class="fas fa-location-dot"></i>
                                    {{ $job->locationRelation->value ?? '-' }}
                                </h6>
                                <h6 class="mb-2 d-flex align-items-center gap-2">
                                    <i class="fas fa-location"></i>
                                    {{ $job->sublocality ?? '-' }}
                                </h6>
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                <div>
                                    <label class="mb-1">Posted :</label>
                                    <h6 class="mb-0">{{ $job->created_at->diffforhumans() ?? '-' }}</h6>
                                </div>
                                <div>
                                    <label class="mb-1">Openings :</label>
                                    <h6 class="mb-0">{{ $job->no_of_openings ?? '-' }}</h6>
                                </div>
                                <div>
                                    <label class="mb-1">Applicants :</label>
                                    <h6 class="mb-0">{{ count($list) ?? '-' }}</h6>
                                </div>
                                @if (count($list) == 0 && $isBoosted == false && $job->highlighted == 0)
                                    <div>
                                        <a href="{{ url('job-edit/' . $job->id) }}">
                                            <button class="listbtn">Edit Job</button>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-2">
                                <label>Notes <span>*</span></label>
                                <h6 class="mb-0">Once a candidate applies to your job, further edits will be hidden, ensuring fairness and transparency in the hiring process.</h6>
                            </div>
                        </div>
                    </div>

                    <div class="side-cards mb-3">
                        <div class="body-head mb-3">
                            <h5 class="mb-0">Overview</h5>
                            <h6 class="mb-0" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#sharePopup"
                                data-share-title='{{ $job->title }}'
                                data-share-url='{{ env('BASE_URL') . 'job-details/' . $job->id }}'
                                data-share-text='{{ $job->categoryRelation->value }}'>
                                <i class="fas fa-share-nodes pe-1 share-btn" data-bs-toggle="tooltip" data-bs-title="Share"
                                    data-job-id={{ $job->id }} data-share-type="job"></i>
                            </h6>
                        </div>
                        <div class="cards-content row">
                            <div class="col-sm-12 mb-3">
                                <h5 class="mb-2">Description</h5>
                                <h6 class="mb-0">{{ $job->description ?? 'No description available.' }}</h6>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <h5 class="mb-2">Job Type</h5>
                                <h6 class="mb-0">{{ $job->shift ?? '-' }}
                                </h6>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <h5 class="mb-2">Required Skills</h5>
                                <h6 class="mb-0">{{ $job->skills ?? 'No Skills Required' }}</h6>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <h5 class="mb-2">Qualification</h5>
                                <h6 class="mb-0">
                                    {{ $job->qualification ?? "Diploma or Bachelor's degree in a related field." }}
                                </h6>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <h5 class="mb-2">Benefits / Perks</h5>
                                <h6 class="mb-0">{{ $job->benfit ?? 'No Benefits available.' }}</h6>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <h5 class="mb-2">Admin Status</h5>
                                <h6
                                    class="{{ $job->approvalstatus == 'approved' ? 'text-success' : 'text-danger' }} text-capitalize mb-0">
                                    {{ $job->approvalstatus }}</h6>
                            </div>
                            @if (auth()->user())
                                <div class="col-sm-12 mb-3">
                                    <h5 class="mb-2">Admin Remarks</h5>
                                    <h6 class="mb-0">{{ $job->remarks ?? 'No Remarks.' }}</h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flex Right -->
            <div class="flex-cards">
                <div class="side-cards job-card mb-3">
                    <div class="body-head mb-3">
                        <h6>Applied ({{ count($list) }})</h6>
                    </div>

                    <div class="container-fluid listtable p-0 border-0">
                        <div class="table-wrapper">
                            <table class="example table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Experience</th>
                                        <th>Contact</th>
                                        <th>Location</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($list as $user)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user->user->profile_img ?? 'assets/images/Avatar.png') }}"
                                                        height="25px" class="rounded-circle" alt="">
                                                    {{ $user->user->name ?? '-' }}
                                                </div>
                                            </td>
                                            <td>{{ $user->experience == 0 ? 'Fresher' : $user->experience . '+Year' }}</td>
                                            <td>{{ $user->user->number ?? '-' }}</td>
                                            <td>{{ $user->locationRelation->value ?? '-' }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ $user->resume ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user->resume : '' }}"
                                                        download>
                                                        <i class="fas fa-download" data-bs-toggle="tooltip"
                                                            data-bs-title="Download Resume"></i>
                                                    </a>
                                                    <a data-bs-toggle="modal"
                                                        data-bs-target="#viewApplied{{ $user->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- View Applied Modal -->
                                        <div class="modal modal-lg fade" id="viewApplied{{ $user->id }}"
                                            tabindex="-1" aria-labelledby="viewAppliedLabel" aria-hidden="true">
                                            <div
                                                class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="m-0">Applied Candicate</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body my-3">
                                                        <div class="row">
                                                            <div class="col-sm-12 col-md-5 mb-2">
                                                                <div class="modal-left-content">
                                                                    <img src="{{ asset($user->user->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user->user->profile_img : 'assets/images/Avatar.png') }}"
                                                                        height="150px"
                                                                        class="d-flex mx-auto object-fit-cover rounded-circle mb-2"
                                                                        alt="">
                                                                    <h4 class="text-center mb-2">
                                                                        {{ $user->user->name ?? '-' }}</h4>
                                                                    <h5 class="text-center text-capitalize mb-2">
                                                                        {{ $user->user->you_are ?? '-' }}
                                                                        {{ $user->user->type_of == 30 ? '- Working' : '' }}
                                                                        {{ $user->user->type_of == 29 ? '- Student' : '' }}
                                                                    </h5>
                                                                    <h6 class="text-center mb-3">
                                                                        <span class="text-dark">Applied On:
                                                                        </span>{{ $user->created_at->format('d-m-Y') ?? '-' }}
                                                                    </h6>
                                                                    <a href="{{ $user->resume ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $user->resume : '' }}"
                                                                        download class="d-flex mx-auto mb-3">
                                                                        <button class="formbtn w-100">Download
                                                                            Resume</button>
                                                                    </a>
                                                                    <div
                                                                        class="d-flex justify-content-center align-items-center gap-2 mt-2">
                                                                        <a href="mailto: {{ $user->user->email ?? '-' }}"
                                                                            class="w-100">
                                                                            <button class="editbtn w-100">Email</button>
                                                                        </a>
                                                                        <a href="tel: +91 {{ $user->user->number ?? '-' }}"
                                                                            class="w-100">
                                                                            <button class="editbtn w-100">Call</button>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-12 col-md-7">
                                                                <div class="modal-right-content">
                                                                    <div class="row">
                                                                        <div class="col-sm-12 mb-2">
                                                                            <h6>Role Information</h6>
                                                                        </div>
                                                                        <div class="col-sm-12 col-md-6 modal-img mb-3">
                                                                            <img src="{{ asset('assets/images/modal_1.png') }}"
                                                                                height="30px" alt="">
                                                                            <div>
                                                                                <h5 class="mb-1">Role Applied</h5>
                                                                                <h6 class="mb-0">
                                                                                    {{ $job->categoryRelation->value ?? '' }}
                                                                                </h6>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12 col-md-6 modal-img mb-3">
                                                                            <img src="{{ asset('assets/images/modal_2.png') }}"
                                                                                height="30px" alt="">
                                                                            <div>
                                                                                <h5 class="mb-1">Qualification</h5>
                                                                                <h6 class="mb-0">
                                                                                    {{ $user->qualification ?? '-' }}</h6>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12 my-2">
                                                                            <h6>Skill Information</65>
                                                                        </div>
                                                                        <div class="col-sm-12 col-md-6 modal-img mb-3">
                                                                            <img src="{{ asset('assets/images/modal_3.png') }}"
                                                                                height="30px" alt="">
                                                                            <div>
                                                                                <h5 class="mb-1">Skills</h5>
                                                                                <h6 class="mb-0">
                                                                                    {{ $user->skills ?? '-' }}</h6>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12 col-md-6 modal-img mb-3">
                                                                            <img src="{{ asset('assets/images/modal_4.png') }}"
                                                                                height="30px" alt="">
                                                                            <div>
                                                                                <h5 class="mb-1">Work Experience</h5>
                                                                                <h6 class="mb-0">
                                                                                    {{ $user->experience == 0 ? 'Fresher' : $user->experience . '+ year' }}
                                                                                </h6>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12 col-md-6 modal-img mb-3">
                                                                            <img src="{{ asset('assets/images/modal_5.png') }}"
                                                                                height="30px" alt="">
                                                                            <div>
                                                                                <h5 class="mb-1">Location</h5>
                                                                                <h6 class="mb-0">
                                                                                    {{ $user->locationRelation->value ?? '-' }}
                                                                                </h6>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12 col-md-6 modal-img mb-3">
                                                                            <img src="{{ asset('assets/images/modal_6.png') }}"
                                                                                height="30px" alt="">
                                                                            <div>
                                                                                <h5 class="mb-1">Current Salary</h5>
                                                                                <h6 class="mb-0">
                                                                                    {{ $user->current_salary ?? '-' }}</h6>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12 col-md-6 modal-img mb-3">
                                                                            <img src="{{ asset('assets/images/modal_7.png') }}"
                                                                                height="30px" alt="">
                                                                            <div>
                                                                                <h5 class="mb-1">Expected Salary</h5>
                                                                                <h6 class="mb-0">
                                                                                    {{ $user->expected_salary ?? '-' }}
                                                                                </h6>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12 my-2">
                                                                            <h6>Additional Notes</h6>
                                                                        </div>
                                                                        <div class="col-sm-12 col-md-12 modal-img mb-3">
                                                                            <img src="{{ asset('assets/images/modal_8.png') }}"
                                                                                height="30px" alt="">
                                                                            <div>
                                                                                <h5 class="mb-1">Notes for Recruitment
                                                                                    Team</h5>
                                                                                <h6 class="mb-0">
                                                                                    {{ $user->notes ?? '-' }}</h6>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('popups.popup')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // DataTables List
        $(document).ready(function() {
            var table = $('.example').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "bDestroy": true,
                "info": false,
                "responsive": true,
                "pageLength": 10,
                "dom": '<"top"f>rt<"bottom"lp><"clear">',
            });
        });
    </script>

    <a id="hiddenDownloader" style="display: none;"></a>

    <script>
        function downloadResume(id) {
            const link = document.getElementById('hiddenDownloader');
            link.href = `/resume/download/${id}`;
            link.setAttribute('download', '');
            link.click();
        }
    </script>

    <script>
        $(document).ready(function() {
            $('.toggle-job-status-btn').click(function(e) {
                e.preventDefault();

                const button = $(this);
                const jobId = button.data('id');

                $.ajax({
                    // url: '/jobs/toggle-status/' + jobId,
                    url: "{{ route('jobs.toggleStatus', ['id' => '__jobId__']) }}/".replace('__jobId__', jobId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.success) {
                            const newStatus = response.new_status;

                            // Update button text
                            button.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));

                            // Update button class
                            if (newStatus === 'active') {
                                button.removeClass('red-label').addClass('green-label');
                            } else {
                                button.removeClass('green-label').addClass('red-label');
                            }
                        }
                    },
                    error: function(xhr) {
                        showToast('Something went wrong!');
                    }
                });
            });
        });
    </script>

    <!-- Pass Laravel data to JS -->
    <script>
        window.appData = {
            shareUrl: "{{ route('toggle.getShareList') }}",
            csrf: "{{ csrf_token() }}"
        };
    </script>
    <script src="{{ asset('assets/js/share_job.js') }}"></script>

@endsection
