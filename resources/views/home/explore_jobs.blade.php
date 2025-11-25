@php
    $jobs_data = collect($jobs_data); // flatten in case of nested collections
@endphp
@if ($jobs_data->isNotEmpty())
    <div class="my-2">
        <div class="body-head px-3 mb-2">
            <h5>Explore Jobs</h5>
            <a href="{{ url('jobs') }}">
                <h6>See More <i class="fas fa-arrow-right ps-1"></i></h6>
            </a>
        </div>

        {{-- @endif --}}
        <div class="home-carousel">
            @foreach ($jobs_data as $job)
                @php
                    // \Log::info('Jobs Data: ' . json_encode($job, JSON_PRETTY_PRINT));
                @endphp
                <div class="item side-cards {{ $job->id ?? 'no_id' }}">
                    <div class="product-card position-relative">
                        <img src="{{ asset('assets/images/NoImage.png') }}"
                            class="mb-2 w-100 rounded-3 object-fit-cover object-center" height="175px" alt="">
                        @if ($job->highlighted == '1')
                            <a class="badge d-flex align-items-center">
                                <img src="{{ asset('assets/images/icon_fire.png') }}" height="15px" class="pe-1"
                                    alt="">
                                <span>Boosted</span>
                            </a>
                        @endif
                        <div class="cards-head">
                            <h5 class="mb-1 long-text">{{ $job->title ?? ($job->title ?? '-') }}</h5>
                            <h6 class="mb-1 long-text">
                                {{ $job->categoryRelation->value ?? '-' }}
                            </h6>
                        </div>

                        <div class="cards-content">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <h6 class="mb-1">₹ {{ $job->salary ?? 'Not disclosed' }}</h6>
                                <h6 class="mb-1">
                                    <i class="fas fa-location-dot pe-1"></i>
                                    {{ $job->locationRelation->value ?? '-' }}
                                </h6>
                            </div>
                        </div>

                        <div class="row align-items-center justify-content-between">
                            <div id="cart-action-{{ $job->id }}">
                                <a target="_blank" href="{{ route('job.details', ['id' => $job->id]) }}">
                                    <button class="listbtn w-100">View Job</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <p class="text-center">No Jobs Found</p>
@endif
