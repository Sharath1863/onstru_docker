<div class="body-head mt-3 mb-2">
    <h5>Review List</h5>
</div>

@if (count($reviews) > 0)
    <div class="side-cards border-0 p-0">
        <ul class="list-unstyled cards-content">
            @foreach ($reviews as $review)
                <li class="mb-3">
                    <div>
                        <div class="mb-2">
                            @for ($i = 0; $i < 5; $i++)
                                @if ($i < $review->stars)
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-muted"></i>
                                @endif
                            @endfor
                        </div>
                        <h6 class="mb-2">{{ $review->review }}</h6>
                        <h6 class="bio mb-3">{{ $review->created_at->format('d M, Y h:i A') }}
                        </h6>
                        <div class="d-flex align-items-center column-gap-2">
                            <img src="{{ asset( $review->user->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $review->user->profile_img :'assets/images/Avatar.png') }}" class="avatar-30" alt="">
                            <h5 class="mb-0">{{ $review->user->name }}</h5>
                        </div>
                    </div>
                </li>
                <hr>
            @endforeach
        </ul>
    </div>
@else
    <div class="side-cards shadow-none border-0">
        <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
            <img src="{{ asset('assets/images/Empty/NoReviews.png') }}" height="150px" class="d-flex mx-auto mb-2" alt="">
            <h5 class="text-center">No Reviews Found</h5>
            <h6 class="text-center">No reviews are available yet - be the first to share your
                experience and help others decide.</h6>
        </div>
    </div>
@endif