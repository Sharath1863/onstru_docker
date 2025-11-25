@php
    $badges = [
        'project' => [
            [
                'title' => 'Emerging Contractor',
                'content' => 'Earn the <span class="text-dark fw-bold">Emerging Contractor</span> Badge by successfully completing five or more projects, showcasing your growing expertise and reliability. Valid till end of this month',
                'badge' => '5P',
            ],
            [
                'title' => 'Proven Contractor',
                'content' => 'Complete ten or more projects successfully to earn the <span class="text-dark fw-bold">Proven Contractor</span> Badge, highlighting your experience, trustworthiness, and consistent performance. Valid till end of this month',
                'badge' => '10P',
            ],
            [
                'title' => 'Elite Contractor',
                'content' => 'Successfully complete fifteen or more projects to earn the <span class="text-dark fw-bold">Elite Contractor</span> Badge, recognizing your exceptional expertise, dedication, and professionalism. Valid till end of this month',
                'badge' => '15P',
            ],
        ],
        'product' => [
            [
                'title' => 'Titan Seller',
                'content' => 'Earn the <span class="text-dark fw-bold">Titan Seller</span> Badge by successfully achieving monthly sales of 5 lakhs in this month, showcasing your growing expertise and reliability. Valid till end of this month',
                'badge' => '5',
            ],
            [
                'title' => 'Crown Seller',
                'content' => 'Complete achieving 10 lakhs sales successfully to earn the <span class="text-dark fw-bold">Crown Seller</span> Badge, highlighting your experience, trustworthiness, and consistent performance. Valid till end of this month',
                'badge' => '10',
            ],
            [
                'title' => 'Empire Seller',
                'content' => 'Achieve monthly sales of 15 lakhs or more to earn the <span class="text-dark fw-bold">Empire Seller</span> Badge, showcasing exceptional performance and market leadership. Valid till end of this month',
                'badge' => '15',
            ],
        ],
        'premium' => [
            [
                'title' => 'Learner',
                'content' => 'Earn the <span class="text-dark fw-bold">Learner Badge</span> by subscribing to premium 3 times — proving your passion for growth, knowledge, and continuous learning.',
                'badge' => '3PM',
            ],
            [
                'title' => 'Intermediate',
                'content' => 'Earn the <span class="text-dark fw-bold">Intermediate Badge</span> badge by subscribing to premium 6 times — showcasing your strong dedication to mastering knowledge and consistent growth.',
                'badge' => '6PM',
            ],
            [
                'title' => 'Explorer',
                'content' => 'Earn the <span class="text-dark fw-bold">Explorer Badge</span> badge by subscribing to premium 9 times — showcasing your strong dedication to mastering knowledge and consistent growth.',
                'badge' => '9PM',
            ],
        ],
    ]
@endphp

@foreach ($badges as $category => $items)
    <div class="modal modal-md fade" id="{{ $category }}Badges" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0 text-capitalize">{{ $category }} Badges</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="row flex-wrap">
                        @foreach ($items as $badge)
                            <div class="col-sm-12 mb-3">
                                <div class="avatar-div-100 position-relative mb-2">
                                    <img src="{{ asset(auth()->user()->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . auth()->user()->profile_img : 'assets/images/Avatar.png') }}"
                                        class="avatar-100" alt="">
                                    <img src="{{ asset('assets/images/Badge_' . $badge['badge'] . '.png') }}" class="badge-100"
                                        alt="">
                                </div>
                                <h5 class="mb-2 text-center">{{ $badge['title'] }}</h5>
                                <h6 class="mb-0 text-center">{!! $badge['content'] !!}</h6>
                            </div>
                        @endforeach
                    </div>

                    @if ($category === 'project')
                        <div class="col-sm-12 d-flex align-items-center justify-content-center">
                            <button class="formbtn" data-bs-toggle="modal" data-bs-target="#addProject">+ Add Project</button>
                        </div>
                    @elseif ($category === 'product')
                        <div class="col-sm-12 d-flex align-items-center justify-content-center">
                            <a href="{{ url('add-product') }}">
                                <button class="formbtn">+ Add Product</button>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach