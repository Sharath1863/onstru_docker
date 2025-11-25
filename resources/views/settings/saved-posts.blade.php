<!-- Empty State -->
<div class="side-cards shadow-none border-0" style="{{ count($posts) > 0 ? 'display: none;' : 'display: block;' }}">
    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
        <img src="{{ asset('assets/images/Empty/NoPosts.png') }}" height="200px" class="d-flex mx-auto mb-2" alt="">
        <h5 class="text-center mb-0">No Posts Found</h5>
        <h6 class="text-center bio">No posts are available at the moment - try refreshing or check back later for new
            updates</h6>
    </div>
</div>

<div class="post-cards position-relative">
    @foreach ($posts as $index => $post)
        @php
            $files = is_array($post->file) ? $post->file : json_decode($post->file, true);
            $fileText = $post->caption ?? 'Check this out!';
        @endphp
        @php
            $fileUrls = array_map(fn($f) => 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $f, $files);
            $extensions = array_map(fn($f) => strtolower(pathinfo($f, PATHINFO_EXTENSION)), $fileUrls);
            $hasVideo = count(array_intersect($extensions, ['mp4', 'mov', 'avi', 'mkv'])) > 0;
            $type = $hasVideo ? 'video' : 'image';
            $assets = is_array($post['file']) ? $post['file'] : [$post['file']];
        @endphp
        <div class="product-card post-bg position-relative rounded-2 ind_post" data-post-id="{{ $post->id }}"
            data-bs-toggle="modal" data-bs-target="#ind_post" data-type="{{ $type }}"
            data-share-title="{{ $post->name ?? 'New Post' }}" data-share-url="{{ $fileUrls[0] ?? '' }}"
            data-share-text="{{ $fileText }}" data-assets="{{ json_encode($fileUrls) }}"
            data-post-like="{{ $post->like_cnt }}" data-post-con="{{ $post->created_at->format('M d') }}"
            data-is_like="{{ $post->is_liked }}" data-is_save="{{ $post->is_saved }}"
            data-is_report="{{ $post->is_reported }}" data-caption="{{ $post->caption ?? '' }}"
            data-location="{{ $post->location ?? '' }}">

            @if ($post->file_type == 'image')
                <img src="{{ asset($post['file'][0] ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $post['file'][0] : 'assets/images/NoImage.png') }}"
                    height="100%" class="w-100 object-fit-cover rounded-2" alt="">
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
                <video height="100%" class="videos w-100 object-fit-cover rounded-2" loop>
                    <source
                        src="{{ asset($post['file'][0] ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $post['file'][0] : 'assets/images/NoImage.png') }}"
                        type="video/mp4">
                </video>
                <h6 class="mb-0 position-absolute" style="top: 5%; right: 5%;">
                    <img src="{{ asset('assets/images/icon_video.png') }}" height="25px" alt="">
                </h6>
            @endif
        </div>
    @endforeach
</div>