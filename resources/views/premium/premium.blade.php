<div class="body-head mb-3">
    <h5>Premium</h5>
    <div class="d-flex align-items-center column-gap-2">
        <a data-bs-toggle="modal" data-bs-target="#premiumInv">
            <button type="button" class="listbtn">Invoice</button>
        </a>
        <a data-bs-toggle="modal" data-bs-target="#premiumBadges">
            <button class="iconbtn"><i class="fas fa-info-circle" data-bs-toggle="tooltip"
                    data-bs-title="About"></i></button>
        </a>
    </div>
</div>

<div class="side-cards border-0 p-0 {{ !$hasSubscription ? 'blurred' : '' }}">
    @foreach ($premium as $pre)
        @if ($pre->premium_type == 'blog')
            <div class="premium incoming">
                <img src="assets/images/Favicon.png" height="30px" width="30px" class="avatar-30" alt="">
                <div class="premium-content">
                    <h6 class="mb-1 caption">{{ $pre->caption }}</h6>
                    <h6 class="mb-2 see-more" style="cursor: pointer;">See more</h6>
                    <span class="time">{{ $pre->created_at->format('g:i A | M d, Y') }}</span>
                </div>
            </div>
        @endif
        @if ($pre->premium_type == 'post')
            <div class="premium incoming">
                <img src="assets/images/Favicon.png" height="30px" width="30px" class="avatar-30" alt="">
                <div class="premium-content">
                    <div class=" mb-3">
                        <div class="item premium-main-div">
                            <img src="{{ 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $pre->image }}"
                                height="275px" alt="Image">
                        </div>
                    </div>
                    <h6 class="mb-1 caption">{{ $pre->caption }}</h6>
                    <h6 class="mb-2 see-more" style="cursor: pointer;">See more</h6>
                    <span class="time">{{ $pre->created_at->format('g:i A | M d, Y') }}</span>
                </div>
            </div>
        @endif
        @if ($pre->premium_type == 'reel')
            <div class="premium incoming">
                <img src="assets/images/Favicon.png" height="30px" width="30px" class="avatar-30" alt="">
                <div class="premium-content">
                    <div class=" mb-3">
                        <div class="item premium-main-div">
                            <video height="300px" controls controlsList="nodownload" loop>
                                <source src={{ 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $pre->video }}
                                    type="video/mp4">
                            </video>
                        </div>
                    </div>
                    <h6 class="mb-1 caption">{{ $pre->caption }}</h6>
                    <h6 class="mb-2 see-more" style="cursor: pointer;">See more</h6>
                    <span class="time">{{ $pre->created_at->format('g:i A | M d, Y') }}</span>
                </div>
            </div>
        @endif
    @endforeach
</div>

<!-- See More / See Less (Caption) -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.see-more').forEach(btn => {
            btn.addEventListener('click', () => {
                const caption = btn.previousElementSibling; // h6
                caption.classList.toggle('expanded');
                btn.textContent = caption.classList.contains('expanded') ? 'See less' :
                    'See more';
            });
        });
    });
</script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $(".owl-carousel").owlCarousel({
            loop: true,
            margin: 15,
            nav: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 4000,
            items: 1,
        });
    });
</script>
