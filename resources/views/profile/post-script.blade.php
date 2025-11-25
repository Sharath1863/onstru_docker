<!-- View Post -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let splideInstance;
        const modal = document.getElementById('ind_post');

        function stopAllVideos() {
            document.querySelectorAll('#video-container video').forEach(video => {
                video.pause();
                video.currentTime = 0;
                const overlay = video.closest('#video-container')?.querySelector('.play-overlay');
                if (overlay) overlay.style.opacity = '1';
            });
        }

        modal.addEventListener('hidden.bs.modal', () => {
            stopAllVideos();
        });

        modal.addEventListener('show.bs.modal', event => {
            const card = event.relatedTarget;
            const type = card.getAttribute('data-type');
            const assets = JSON.parse(card.getAttribute('data-assets'));

            const imageCarousel = modal.querySelector('#post-cl');
            const imageList = imageCarousel.querySelector('.splide__list');
            const videoContainer = modal.querySelector('#video-container');

            // Reset media state
            stopAllVideos();
            imageCarousel.classList.add('d-none');
            videoContainer.classList.add('d-none');
            imageList.innerHTML = '';
            videoContainer.innerHTML = '';

            // Handle image post
            if (type === 'image') {
                assets.forEach(src => {
                    const li = document.createElement('li');
                    li.classList.add('splide__slide');
                    li.innerHTML = `<img src="${src}" width="100%" class="object-fit-contain" alt="">`;
                    imageList.appendChild(li);
                });

                imageCarousel.classList.remove('d-none');
                if (splideInstance) splideInstance.destroy();
                splideInstance = new Splide('#post-cl', {
                    type: 'fade',
                    perPage: 1,
                    rewind: true,
                    arrows: false
                }).mount();
            }
            else if (type === 'video') {
                videoContainer.innerHTML = `
                <div class="video-wrapper position-relative h-100">
                    <video class="w-100 h-100" preload="metadata">
                        <source src="${assets[0]}" type="video/mp4">
                    </video>
                    <div class="play-overlay position-absolute top-50 start-50 translate-middle text-white fs-4" 
                        style="pointer-events:none; transition: opacity 0.3s;">
                        <i class="fas fa-play"></i>
                    </div>
                </div>
            `;
                videoContainer.classList.remove('d-none');

                const video = videoContainer.querySelector('video');
                const overlay = videoContainer.querySelector('.play-overlay');
                overlay.style.opacity = '1';
                videoContainer.onclick = (e) => {
                    if (e.target.tagName === 'I') return;
                    // Toggle play/pause
                    if (video.paused) {
                        video.play();
                    } else {
                        video.pause();
                    }
                };
                video.addEventListener('play', () => {
                    overlay.style.opacity = '0';
                });
                video.addEventListener('pause', () => {
                    overlay.style.opacity = '1';
                });
                video.addEventListener('ended', () => {
                    overlay.style.opacity = '1';
                });
            }
        });
    });
</script>

<script>
    const tribute = new Tribute({
        trigger: "#",
        values: function (text, cb) {
            fetch(`{{ route('hashtags.suggest') }}?q=${encodeURIComponent(text)}`)
                .then(res => res.json())
                .then(data => {
                    const limited = data.slice(0, 5);
                    cb(limited);
                })
                .catch(() => cb([]));
        },
        selectTemplate: function (item) {
            return item ? `#${item.original.value}` : null;
        },
        menuItemTemplate: function (item) {
            return `#${item.original.key}`;
        },
        lookup: "key",
        fillAttr: "value"
    });

    tribute.attach(document.getElementById("caption"));
    tribute.attach(document.getElementById("editcaption"));
</script>

<!-- Add Post -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const input = document.getElementById("addPostImage");
        const dropZone = document.getElementById("dropZone");

        const previewCarousel = document.getElementById("previewCarousel");
        const dotsContainer = document.getElementById("carouselDots");
        const addMoreBtn = document.getElementById("addMoreBtn");
        const addMoreInput = document.getElementById("addMoreImages");

        const MAX_IMAGES = 10;
        const MAX_TOTAL_MB = 50;
        let uploadedFiles = [];
        let currentIndex = 0;

        // Add More
        addMoreBtn.addEventListener("click", () => addMoreInput.click());
        addMoreInput.addEventListener("change", function () {
            handleFiles(this.files, false);
            this.value = "";
        });

        // Drag & Drop
        if (dropZone) {
            dropZone.addEventListener("dragover", e => e.preventDefault());
            dropZone.addEventListener("drop", e => {
                e.preventDefault();
                handleFiles(e.dataTransfer.files, true);
            });
        }

        // File Input
        if (input) {
            input.addEventListener("change", function () {
                handleFiles(this.files, true);
            });
        }

        function handleFiles(files, replaceAll) {
            let hasVideo = uploadedFiles.some(f => f.type.startsWith("video/"));

            if (replaceAll) {
                uploadedFiles = [];
                hasVideo = false;
            }

            let newFiles = [];

            Array.from(files).forEach(file => {
                if (file.type.startsWith("video/")) {
                    if (!hasVideo && uploadedFiles.length === 0) {
                        uploadedFiles.push(file);
                        hasVideo = true;
                    } else {
                        showToast("Only one video can be uploaded per post.");
                    }
                } else if (file.type.startsWith("image/")) {
                    if (hasVideo) {
                        showToast("You cannot add images along with a video.");
                    } else if (uploadedFiles.length + newFiles.length < MAX_IMAGES) {
                        newFiles.push(file);
                    } else {
                        showToast(`Maximum ${MAX_IMAGES} images allowed.`);
                    }
                }
            });

            // Check total combined size of images
            const totalSizeMB = [...uploadedFiles, ...newFiles]
                .reduce((acc, f) => acc + f.size, 0) / (1024 * 1024);

            if (totalSizeMB > MAX_TOTAL_MB) {
                showToast(`Total upload size exceeds ${MAX_TOTAL_MB} MB. Please remove some images.`);
                return;
            }

            uploadedFiles = [...uploadedFiles, ...newFiles];

            const newIndex = replaceAll ? Math.max(0, uploadedFiles.length - 1) : currentIndex;
            showPreview(uploadedFiles, newIndex);

            if (replaceAll) {
                const m1El = document.getElementById("addPost_1");
                const m2El = document.getElementById("addPost_2");
                if (m1El && m2El && window.bootstrap?.Modal) {
                    (bootstrap.Modal.getInstance(m1El) || new bootstrap.Modal(m1El)).hide();
                    new bootstrap.Modal(m2El).show();
                }
            }
        }

        function showPreview(files, goToIndex) {
            previewCarousel.innerHTML = "";
            dotsContainer.innerHTML = "";
            currentIndex = Math.max(0, Math.min(goToIndex || 0, Math.max(0, files.length - 1)));

            if (files.length === 1 && files[0].type.startsWith("video/")) {
                addMoreBtn.style.display = "none";
                const video = document.createElement("video");
                video.src = URL.createObjectURL(files[0]);
                video.className = 'object-fit-contain';
                video.setAttribute('controlsList', 'nodownload');
                video.style.height = '300px';
                video.controls = true;
                previewCarousel.appendChild(video);
                return;
            }

            addMoreBtn.style.display = files.length >= MAX_IMAGES ? "none" : "block";

            files.forEach((file, idx) => {
                const slide = document.createElement("div");
                slide.className = "carousel-slide";

                const img = document.createElement("img");
                img.src = URL.createObjectURL(file);
                img.className = 'object-fit-contain';
                img.style.height = '300px';
                slide.appendChild(img);

                previewCarousel.appendChild(slide);

                const dot = document.createElement("div");
                dot.className = "carousel-dot" + (idx === currentIndex ? " active" : "");
                dot.addEventListener("click", () => goToSlide(idx));
                dotsContainer.appendChild(dot);
            });

            updateCarousel();
        }

        function goToSlide(index) {
            currentIndex = Math.max(0, Math.min(index, uploadedFiles.length - 1));
            updateCarousel();
        }

        function updateCarousel() {
            const slides = previewCarousel.querySelectorAll(".carousel-slide");
            slides.forEach(slide => {
                slide.style.transform = `translateX(-${currentIndex * 100}%)`;
            });

            const dots = dotsContainer.querySelectorAll(".carousel-dot");
            dots.forEach((dot, idx) => dot.classList.toggle("active", idx === currentIndex));
        }

        // Attach files before submit in Modal 2
        const form = document.querySelector('#addPost_2 form');
        form.addEventListener('submit', e => {
            const container = document.getElementById('hiddenFileContainer');
            container.innerHTML = '';

            uploadedFiles.forEach((file, index) => {
                const input = new DataTransfer();
                input.items.add(file);
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = 'files[]';
                fileInput.files = input.files;
                container.appendChild(fileInput);
            });
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const postCards = document.querySelectorAll('.product-card');
        let editSplide; // store instance

        postCards.forEach(card => {
            card.addEventListener('click', function () {
                const type = this.getAttribute('data-type');
                const assets = JSON.parse(this.getAttribute('data-assets') || '[]');
                const caption = this.getAttribute('data-caption') || '';
                const location = this.getAttribute('data-location') || '';
                const postId = this.getAttribute('data-post-id');

                // Fill the form inputs
                document.getElementById('editPostId').value = postId;
                document.getElementById('editcaption').value = caption;
                document.getElementById('editlocation').value = location;

                const deleteBtn = document.querySelector('#viewPost .delete-post-btn');
                if (deleteBtn) {
                    deleteBtn.setAttribute('data-post-id', postId);
                }

                // Fill media in carousel
                const mediaList = document.querySelector('.editPostCarousel .splide__list');
                mediaList.innerHTML = ''; // Clear old content

                if (type === 'video' && assets.length > 0) {
                    const video = document.createElement('video');
                    video.setAttribute('controls', true);
                    video.className = 'w-100 object-fit-contain';
                    video.style.height = '300px'

                    const source = document.createElement('source');
                    source.src = assets[0];
                    source.type = 'video/mp4';
                    video.appendChild(source);

                    const li = document.createElement('li');
                    li.classList.add('splide__slide');
                    li.appendChild(video);
                    mediaList.appendChild(li);

                } else if (type === 'image' && assets.length > 0) {
                    assets.forEach(url => {
                        const li = document.createElement('li');
                        li.classList.add('splide__slide');
                        li.innerHTML =
                            `<img src="${url}" height="300px" class="object-fit-contain" alt="">`;
                        mediaList.appendChild(li);
                    });
                }

                // Init / Re-init Splide
                if (editSplide) {
                    editSplide.destroy();
                }
                editSplide = new Splide('.editPostCarousel', {
                    type: 'fade',
                    perPage: 1,
                    rewind: true,
                    arrows: false
                }).mount();
            });
        });
    });
</script>

<!-- See More / See Less (Caption) -->
<script>
    $(document).on('click', '.see-more', function () {
        var postId = $(this).attr('id').replace('see-more', '');  // Get the post ID dynamically
        var caption = $('#caption' + postId);
        var seeMoreLink = $(this);

        // Toggle caption visibility
        if (caption.hasClass('expanded')) {
            caption.removeClass('expanded');
            seeMoreLink.text('See more');
        } else {
            caption.addClass('expanded');
            seeMoreLink.text('See less');
        }
    });
</script>

<!-- Hashtag -->
<script>
    function linkHashtags(container) {
        container.find('.caption').each(function () {
            let text = $(this).text();
            let linkedText = text.replace(/#(\w+)/g, function (match, tag) {
                let baseUrl = `{{ url('explore') }}`;
                let url = `${baseUrl}/${tag}`;
                return `<a href="${url}" class="hashtag-link">#${tag}</a>`;
            });
            $(this).html(linkedText);
        });
    }
</script>