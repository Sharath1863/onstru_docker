 @foreach ($post_data as $index => $pt)
     <div class="side-cards border-0 mb-0">
         <div class="cards-content">
             <!-- Header -->
             <div class="home-post-header mb-2 d-flex justify-content-between">
                 <a href="{{ url('user-profile', ['id' => $pt->user->id]) }}" class="d-flex align-items-center gap-2">
                     <div class="avatar-div-30 position-relative">
                         <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $pt->user->profile_img ?? 'assets/images/Avatar.png') }}"
                             class="avatar-30" alt="">
                         @if ($pt->user->badge != 0 && $pt->user->badge != null)
                             <img src="{{ asset('assets/images/Badge_' . $pt->user->badge . '.png') }}" class="badge-30"
                                 alt="">
                         @endif
                     </div>
                     <div class="user-content">
                         <h6 class="mb-1 text-lowercase">{{ $pt->user->user_name ?? '' }}</h6>
                         <h6 class="m-0 bio">{{ $pt->user->as_a ?? 'Consumer' }}</h6>
                     </div>
                 </a>

                 <div class="d-flex align-items-center column-gap-3">
                     @php
                         $isFollowing = auth()->check() ? auth()->user()->isFollowing($pt->user) : false;
                     @endphp
                     @if ($isFollowing)
                         <button class="followingbtn follow-btn" data-user-id="{{ $pt->user->id }}" data-following="1">
                             <span class="label">Following</span>
                         </button>
                     @else
                         @if (auth()->id() != $pt->user->id)
                             <button class="followersbtn follow-btn" data-user-id="{{ $pt->user->id }}"
                                 data-following="0">
                                 <span class="label">Follow</span>
                             </button>
                         @endif
                     @endif
                     <div class="dropdown">
                         <a data-bs-toggle="dropdown" aria-expanded="false">
                             <i class="fas fa-ellipsis-vertical text-dark"></i>
                         </a>
                         <ul class="dropdown-menu z-4">
                             @if (!$pt->is_reported)
                                 <li>
                                     <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#postReport"
                                         data-id="{{ $pt->id }}">
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
             <div class="home-post-body mb-3">
                 @php
                     $assets = is_array($pt->file) ? $pt->file : [$pt->file] ?? null;
                 @endphp

                 @if (count($assets) > 1)
                     <!-- Carousel with dots -->
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
                                 <button type="button" data-bs-target="#carousel{{ $index }}"
                                     data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}"
                                     aria-current="{{ $i === 0 ? 'true' : 'false' }}"
                                     aria-label="Slide {{ $i + 1 }}">
                                 </button>
                             @endforeach
                         </div>
                     </div>
                 @else
                     <!-- Single Image -->
                     <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $assets[0]) }}"
                         class="w-100" alt="">
                 @endif
             </div>

             <!-- Actions -->
             <div class="home-post-actions">
                 <div class="home-actions mb-2">
                     <div class="d-flex align-items-center column-gap-4">
                         <a href="javascript:void(0)">
                             <i class="fa-{{ $pt->is_liked ? 'solid active' : 'regular' }} fa-heart like-btn"
                                 data-post-id="{{ $pt->id }}"></i>
                         </a>
                         <a class="comment-count-pop" data-bs-toggle="modal" data-bs-target="#commentPopup"
                             data-post-id="{{ $pt->id }}">
                             <i class="fa-regular fa-message comment-btn"></i>
                         </a>
                         <a data-bs-toggle="modal" data-bs-target="#sharePopup" data-share-url="{{ $shareUrl ?? '' }}"
                             data-share-text="{{ $shareText ?? '' }}">
                             <i class="far fa-paper-plane share-btn" data-post-id="{{ $pt->id }}"
                                 data-share-type="{{ 'post' }}"></i>
                         </a>
                     </div>
                     <div>
                         <a><i class="fa-{{ $pt->is_saved ? 'solid active' : 'regular' }}
                                                                                                    fa-bookmark save-btn"
                                 data-post-id="{{ $pt->id }}"></i></a>
                     </div>
                 </div>
                 <h6 class="mb-2 fw-semibold likes-count" data-post-id="{{ $pt->id }}" data-bs-toggle="modal"
                     data-bs-target="#likesPopup" id="likes_count_{{ $pt->id }}">
                     {{ $pt->like_cnt >= 1000 ? number_format($pt->like_cnt / 1000, 1) . 'k' : $pt->like_cnt ?? 0 }}
                     Likes
                 </h6>
                 <h6 class="mb-1 d-flex align-items-start column-gap-1">
                     <a href="{{ url('user-profile/' . $pt->user->id) }}"
                         class="text-dark fw-semibold text-lowercase pe-1">
                         {{ $pt->user->user_name ?? '' }}
                     </a>
                     <span class="caption">{{ $pt->caption }}</span>
                 </h6>
                 <h6 class="mb-1 see-more text-muted" style="cursor: pointer;">See more</h6>
                 <label class="m-0">On {{ $pt->created_at->format('M d') }}</label>
             </div>
         </div>
     </div>
 @endforeach
