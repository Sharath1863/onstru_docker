<div class="body-head mb-3">
    <h5>Posts</h5>
    <div class="d-flex align-items-center column-gap-2">
        <a data-bs-toggle="modal" data-bs-target="#addPost_1">
            <button class="listbtn">+ Add Post</button>
        </a>
    </div>
</div>

@if (count($posts) == 0)
    <div class="side-cards shadow-none border-0">
        <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
            <img src="{{ asset('assets/images/Empty/NoPosts.png') }}" height="200px" class="d-flex mx-auto mb-2" alt="">
            <h5 class="text-center">No Posts Found</h5>
            <h6 class="text-center">No posts are available at the moment - try refreshing or check back later for new
                updates</h6>
        </div>
    </div>
@else
    <div class="post-cards position-relative">
        @foreach ($posts as $post)
            @php
                $files = is_array($post->file) ? $post->file : json_decode($post->file, true);
                $fileText = $post->caption ?? 'Check this out!';
            @endphp

            @if (is_array($files) && count($files) > 0)
                @php
                    $fileUrls = array_map(fn($f) => 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $f, $files);
                    $extensions = array_map(fn($f) => strtolower(pathinfo($f, PATHINFO_EXTENSION)), $fileUrls);
                    $hasVideo = count(array_intersect($extensions, ['mp4', 'mov', 'avi', 'mkv'])) > 0;
                    $type = $hasVideo ? 'video' : 'image';
                @endphp

                <div class="product-card post-bg position-relative rounded-2 ind_post" data-post-id="{{ $post->id }}"
                    data-bs-toggle="modal" data-bs-target="#ind_post" data-type="{{ $type }}"
                    data-share-title="{{ $post->name ?? 'New Post' }}" data-share-url="{{ $fileUrls[0] ?? '' }}"
                    data-share-text="{{ $fileText }}" data-assets="{{ json_encode($fileUrls) }}"
                    data-post-like="{{ $post->like_cnt }}" data-post-con="{{ $post->created_at->format('M d') }}"
                    data-is_like="{{ $post->is_liked }}" data-is_save="{{ $post->is_saved }}"
                    data-caption="{{ $post->caption ?? '' }}" data-location="{{ $post->location ?? '' }}">

                    @if ($type === 'image')
                        <img src="{{ $fileUrls[0] }}" height="100%" class="w-100 object-fit-cover rounded-2">
                        <h6 class="mb-0 position-absolute" style="top: 5%; right: 5%;">
                            <img src="{{ asset('assets/images/icon_image.png') }}" height="25px" alt="">
                        </h6>
                        <!-- Sensitive Content -->
                        @if ($post->sense == 1)
                            <div class="sensitive-overlay z-3">
                                <div class="overlay-content text-center">
                                    <img src="{{ asset('assets/images/Sensitive.png') }}" height="50px" class="d-flex mx-auto mb-2" alt="">
                                </div>
                            </div>
                        @endif
                    @else
                        <video height="100%" class="w-100 object-fit-cover rounded-2">
                            <source src="{{ $fileUrls[0] }}" type="video/mp4">
                        </video>
                        <h6 class="mb-0 position-absolute" style="top: 5%; right: 5%;">
                            <img src="{{ asset('assets/images/icon_video.png') }}" height="25px" alt="">
                        </h6>
                    @endif
                </div>
            @else
                <div class="side-cards shadow-none border-0">
                    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                        <img src="{{ asset('assets/images/Empty/NoPosts.png') }}" width="100%" class="d-flex m-auto" alt="">
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif

@include('popups.follow')

@include('profile.post-popup')

@include('profile.post-script')