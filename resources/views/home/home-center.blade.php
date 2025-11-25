<div class="flex-center mt-2" id="feed-container">
    <div class="flex-cards">
        @php
            $postCount = 0;
            $jobCount = 0;
            $productCount = 0;
            $serviceCount = 0;
            $typeCounts = $posts_data->groupBy('type')->map(fn($group) => count($group));
        @endphp

        @foreach ($posts_data as $index => $item)
            @php
                // $post = collect($data['post'] ?? []);
                // $job = collect($data['job'] ?? []);
                // $product = collect($data['product'] ?? []);
                // $services = collect($data['service'] ?? []);
                // \Log::info('Post Item: ' . json_encode($post, JSON_PRETTY_PRINT));
                // \Log::info('Job Item: ' . json_encode($job, JSON_PRETTY_PRINT));
                // \Log::info('Product Item: ' . json_encode($product, JSON_PRETTY_PRINT));
                // \Log::info('Service Item: ' . json_encode($services, JSON_PRETTY_PRINT));
            @endphp

            @if ($item->type === 'post' && $postCount < 24)
                <div class="side-cards border-0 mb-0 pt-0 pb-3">
                    <div class="cards-content">
                        <!-- Header -->
                        <div class="home-post-header mb-2 d-flex justify-content-between">
                            <a href="{{ url('user-profile', ['id' => $item->user->id]) }}"
                                class="d-flex align-items-center gap-2">
                                <div class="avatar-div-30 position-relative">
                                    <img src="{{ asset( $item->user->profile_img ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $item->user->profile_img : 'assets/images/Avatar.png') }}"
                                        class="avatar-30" alt="">
                                    @if ($item->user->badge != 0 && $item->user->badge != null)
                                        <img src="{{ asset('assets/images/Badge_' . $item->user->badge . '.png') }}"
                                            class="badge-30" alt="">
                                    @endif
                                </div>
                                <div class="user-content">
                                    <h6 class="mb-1 text-lowercase">{{ $item->user->user_name ?? '' }}</h6>
                                    @if ($item->category === 'service' || $item->category === 'products')
                                        <h6 class="m-0 bio">{{ $item->locationRelation->value ?? '' }}</h6>
                                    @else
                                        <h6 class="m-0 bio">{{ $item->location ?? '' }}</h6>
                                    @endif
                                </div>
                            </a>

                            <div class="d-flex align-items-center column-gap-3">
                                @php
                                    $isFollowing = auth()->check() ? auth()->user()->isFollowing($item->user) : false;
                                @endphp
                                @if ($isFollowing)
                                    <button class="followingbtn follow-btn" data-user-id="{{ $item->user->id }}" data-following="1">
                                        <span class="label">Following</span>
                                    </button>
                                @else
                                    @if (auth()->id() != $item->user->id)
                                        <button class="followersbtn follow-btn" data-user-id="{{ $item->user->id }}" data-following="0">
                                            <span class="label">Follow</span>
                                        </button>
                                    @endif
                                @endif
                                <div class="dropdown">
                                    <a data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-vertical text-dark"></i>
                                    </a>
                                    <ul class="dropdown-menu z-4">
                                        @if (!$item->is_reported)
                                            <li>
                                                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#postReport"
                                                    data-id="{{ $item->id }}">
                                                    <i class="fas fa-triangle-exclamation text-danger pe-1"></i>
                                                    Report
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item">
                                                    <i class="fas fa-circle-check text-success pe-1"></i>
                                                    Reported
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Post Body -->
                        <div class="home-post-body mb-3 position-relative">
                            @php
                                $assets = is_array($item->file) ? $item->file : [$item->file] ?? null;
                            @endphp

                            <div class="media-wrapper position-relative">
                                @if (count($assets) > 1)
                                    <div id="carousel{{ $index }}" class="carousel slide">
                                        <div class="carousel-inner">
                                            @foreach ($assets as $i => $asset)
                                                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                                    <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $asset) }}"
                                                        class="d-block w-100" alt="">
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="carousel-indicators">
                                            @foreach ($assets as $i => $asset)
                                                <button type="button" data-bs-target="#carousel{{ $index }}" data-bs-slide-to="{{ $i }}"
                                                    class="{{ $i === 0 ? 'active' : '' }}"
                                                    aria-current="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $i + 1 }}">
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    @if ($item->file_type === 'image')
                                        <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $assets[0]) }}"
                                            class="w-100" alt="">
                                    @elseif ($item->file_type === 'video')
                                        <div class="video-wrapper position-relative">
                                            <video src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $assets[0]) }}"
                                                class="feed-video w-100" playsinline autoplay loop></video>
                                            <button class="video-play-btn">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </div>
                                    @endif
                                @endif

                                @if ($item->file_type === 'image')
                                    @if ($item->sense == 1)
                                        <div class="sensitive-overlay z-3">
                                            <div class="overlay-content text-center">
                                                <img src="{{ asset('assets/images/Sensitive.png') }}" height="75px"
                                                    class="d-flex mx-auto mb-2" alt="">
                                                <h6 class="mb-2">This content may be sensitive</h6>
                                                <button class="viewSensitive followingbtn">View Anyway</button>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            @if (!is_null($item->category))
                                @php
                                    if ($item->category === 'service' && $item->created_by != auth()->id()) {
                                        $route = url('individual-service', ['id' => $item->category_id]);
                                        $type = 'Apply Service';
                                    } elseif ($item->category === 'products') {
                                        $route = url('individual-product', ['id' => $item->category_id]);
                                        $type = 'Buy Now';
                                    } elseif ($item->category === 0) {
                                        $route = url('premium');
                                        $type = 'Subscribe Now';
                                    } else {
                                        $route = null;
                                        $type = null;
                                    }
                                @endphp
                                <a href="{{ $route }}">
                                    <button class="postbtn w-100">{{ $type }}<i class="fas fa-arrow-right ps-1"></i></button>
                                </a>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="home-post-actions">
                            <div class="home-actions mb-2">
                                <div class="d-flex align-items-center column-gap-4">
                                    <a href="javascript:void(0)">
                                        <i class="fa-{{ $item->is_liked ? 'solid active' : 'regular' }} fa-heart like-btn"
                                            data-post-id="{{ $item->id }}"></i>
                                    </a>
                                    <a class="comment-count-pop" data-bs-toggle="modal" data-bs-target="#commentPopup"
                                        data-post-id="{{ $item->id }}">
                                        <i class="fa-regular fa-message comment-btn"></i>
                                    </a>
                                    @php
                                        //   $id = $pt->id ?? null;
                                        //     $shareUrl = url('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $id);
                                        //     $shareText = $pt->caption ?? 'Check this out!';
                                        //     $shareTitle = $pt->name ?? 'New Post';

                                        // $shareUrl = url('post/' . $item->id);
                                        // $shareText =
                                        //     'Check out this post by ' .
                                        //     ($item->user->user_name ?? 'a user') .
                                        //     ' on Onstru: ' .
                                        //     $shareUrl;
                                    @endphp
                                    <a data-bs-toggle="modal" data-bs-target="#sharePopup"
                                        data-share-url="{{ $shareUrl ?? '' }}" data-share-text="{{ $shareText ?? '' }}">
                                        <i class="far fa-paper-plane share-btn" data-post-id="{{ $item->id }}"
                                            data-share-type="{{ 'post' }}"></i>
                                    </a>
                                </div>
                                <div>
                                    <a>
                                        <i class="fa-{{ $item->is_saved ? 'solid active' : 'regular' }} fa-bookmark save-btn"
                                            data-post-id="{{ $item->id }}"></i>
                                    </a>
                                </div>
                            </div>
                            <h6 class="mb-2 fw-semibold likes-count" data-post-id="{{ $item->id }}" data-bs-toggle="modal"
                                data-bs-target="#likesPopup" id="likes_count_{{ $item->id }}">
                                {{ $item->like_cnt >= 1000 ? number_format($item->like_cnt / 1000, 1) . 'k likes' : ($item->like_cnt == 0 ? '' : $item->like_cnt . ' likes') }}

                            </h6>
                            <h6 class="mb-1 caption" id="caption{{ $item->id }}">
                                <span><a href="{{ url('user-profile/' . $item->user->id) }}"
                                        class="text-dark fw-semibold text-lowercase pe-1">
                                        {{ $item->user->user_name ?? '' }}
                                    </a></span>
                                <span class="fw-medium">{{ $item->caption }}</span>
                            </h6>
                            <h6 class="mb-0 see-more text-muted" id="see-more{{ $item->id }}" style="cursor: pointer;">See more
                            </h6>
                            <label class="m-0">On {{ $item->created_at->format('M d') }}</label>
                        </div>
                    </div>
                </div>
                @php $postCount++; @endphp
            @elseif($item->type === 'job' && $jobCount < 5)
                @if ($jobCount == 0)
                    <div class="body-head px-3 my-2">
                        <h5>Explore Jobs</h5>
                        <a href="{{ url('jobs') }}">
                            <h6>See More <i class="fas fa-arrow-right ps-1"></i></h6>
                        </a>
                    </div>
                    <div class="home-carousel mb-3">
                @endif
                    <div class="item side-cards {{ $item->id ?? 'no_id' }}">
                        <div class="product-card position-relative">
                            <img src="{{ asset('assets/images/NoImage.png') }}"
                                class="mb-2 w-100 rounded-3 object-fit-cover object-center" height="175px" alt="">
                            @if ($item->highlighted == '1')
                                <a class="badge d-flex align-items-center">
                                    <img src="{{ asset('assets/images/icon_fire.png') }}" height="15px" class="pe-1" alt="">
                                    <span>Boosted</span>
                                </a>
                            @endif
                            <div class="cards-head">
                                <h5 class="mb-1 long-text">{{ $item->title ?? ($item->title ?? '-') }}
                                </h5>
                                <h6 class="mb-1 long-text">
                                    {{ $item->categoryRelation->value ?? '-' }}
                                </h6>
                            </div>
                            <div class="cards-content">
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <h6 class="mb-1">₹ {{ $item->salary ?? 'Not disclosed' }}</h6>
                                    <h6 class="mb-1">
                                        <i class="fas fa-location-dot pe-1"></i>
                                        {{ $item->locationRelation->value ?? '-' }}
                                    </h6>
                                </div>
                            </div>
                            <div class="row align-items-center justify-content-between">
                                <div id="cart-action-{{ $item->id }}">
                                    <a target="_blank" href="{{ route('job.details', ['id' => $item->id]) }}">
                                        <button class="listbtn w-100">View Job</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php $jobCount++; @endphp
                    @if ($typeCounts['job'] == $jobCount)
                        </div>
                    @endif
            @elseif($item->type === 'product' && $productCount < 5)
                @if ($productCount == 0)
                    <div class="body-head px-3 my-2">
                        <h5>Explore Products</h5>
                        <a href="{{ url('products') }}">
                            <h6>See More <i class="fas fa-arrow-right ps-1"></i></h6>
                        </a>
                    </div>
                    <div class="home-carousel mb-3">
                @endif
                    <div class="item side-cards">
                        <div class="product-card position-relative">
                            <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $item->cover_img ?? 'assets/images/NoImage.png') }}"
                                class="mb-2 w-100 rounded-3 object-fit-cover object-center" height="175px" alt="">
                            @if ($item->highlighted == '1')
                                <a class="badge d-flex align-items-center">
                                    <img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px" class="pe-1" alt="">
                                    <span>Highlighted</span>
                                </a>
                            @endif
                            <div class="cards-head">
                                <h5 class="mb-1 long-text">{{ $item->name ?? '-' }}</h5>
                                <h6 class="mb-1 long-text">{{ $item->categoryRelation->value ?? '-' }}</h6>
                            </div>
                            <div class="cards-content">
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <h6 class="mb-1">
                                        <i class="fas fa-star pe-1 text-warning"></i>
                                        {{ number_format($item->reviews_avg_stars, 1) }}
                                        ({{ $item->reviews_count ?? 0 }})
                                    </h6>
                                </div>
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <h6 class="mb-1">
                                        ₹ {{ $item->sp ?? '-' }}
                                        <span class="dashed-line ps-1"><i
                                                class="fas fa-indian-rupee-sign pe-1"></i>{{ $item->mrp ?? '-' }}</span>
                                    </h6>
                                    <h6 class="mb-1"><i class="fas fa-location-dot pe-1"></i>
                                        {{ $item->locationRelation->value ?? '-' }}</h6>
                                </div>
                            </div>
                            <a href="{{ url('individual-product/' . $item->id) }}">
                                <button class="listbtn w-100">View Product</button>
                            </a>
                        </div>
                    </div>
                    @php $productCount++; @endphp
                    @if ($typeCounts['product'] == $productCount)
                        </div>
                    @endif
            @elseif($item->type === 'service' && $serviceCount < 5)
                @if ($serviceCount == 0)
                    <div class="body-head px-3 my-2">
                        <h5>Explore Service</h5>
                        <a href="{{ url('services') }}">
                            <h6>See More <i class="fas fa-arrow-right ps-1"></i></h6>
                        </a>
                    </div>
                    <div class="home-carousel mb-3">
                @endif
                    <div class="item side-cards">
                        <div class="product-card position-relative">
                            <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $item->image ?? 'assets/images/NoImage.png') }}"
                                class="mb-2 w-100 rounded-3 object-fit-cover object-center" height="175px" alt="">
                            @if ($item->highlighted == 1)
                                <a class="badge d-flex align-items-center">
                                    <img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px" class="pe-1" alt="">
                                    <span>Highlighted</span>
                                </a>
                            @endif
                            <div class="cards-head">
                                <h5 class="mb-1 long-text">{{ $item->title ?? '-' }}</h5>
                                <h6 class="mb-1 long-text">
                                    {{ $item->serviceType->value ?? '-' }}
                                </h6>
                            </div>

                            <div class="cards-content">
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <h6 class="mb-1">₹ {{ $item->price_per_sq_ft ?? '-' }}</h6>
                                    <h6 class="mb-1">
                                        <i class="fas fa-location-dot pe-1"></i>
                                        {{ $item->locationRelation->value ?? '-' }}
                                    </h6>
                                </div>
                            </div>

                            <div class="row align-items-center justify-content-between">
                                <div id="cart-action-{{ $item->id }}">
                                    <a href="{{ route('service.show', ['id' => $item->id]) }}">
                                        <button class="listbtn w-100">View Service</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php $serviceCount++; @endphp
                    @if ($typeCounts['service'] == $serviceCount)
                        </div>
                    @endif
            @endif
        @endforeach
    </div>

</div>

<script>
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".viewSensitive");
        if (!btn) return;

        const wrapper = btn.closest(".media-wrapper");
        const overlay = btn.closest(".sensitive-overlay");

        if (wrapper) wrapper.classList.remove("blurred");
        if (overlay) overlay.remove();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.caption').forEach(captionEl => {
            let text = captionEl.textContent;

            // Replace hashtags with anchor tags
            let linkedText = text.replace(/#(\w+)/g, (match, tag) => {
                let baseUrl = `{{ url('explore') }}`;
                let url = `${baseUrl}/${tag}`; // JS appends the dynamic part
                return `<a href="${url}" class="hashtag-link">#${tag}</a>`;
            });

            captionEl.innerHTML = linkedText;
        });
    });
</script>