<!-- See More / See Less (Caption) -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.see-more').forEach(btn => {
            btn.addEventListener('click', () => {
                const reelId = btn.id.replace('see-more', '');
                const caption = document.getElementById(`caption${reelId}`);
                caption.classList.toggle('expanded');
                if (caption.classList.contains('expanded')) {
                    btn.textContent = 'See less';
                } else {
                    btn.textContent = 'See more';
                }
            });
        });
    });
</script>

<!-- Video Loader Condition -->
<!-- <script>
    videoDivs.forEach(div => {
        const video = div.querySelector('video');
        const loader = div.querySelector('.video-loader');
        loader.classList.remove('hidden');
        video.addEventListener('loadeddata', () => {
            loader.classList.add('hidden');
        });
    });
</script> -->
