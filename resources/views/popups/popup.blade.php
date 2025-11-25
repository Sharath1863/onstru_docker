<!-- Like Modal -->
<div class="modal fade" id="likesPopup" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="likesPopupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Likes</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body overflow-y-auto" style="max-height: 35vh;">
                <div class="position-sticky sticky-top bg-white py-3">
                    <div class="inpleftflex">
                        <i class="fas fa-search text-muted text-center"></i>
                        <input type="text" name="search" id="likeSearch" class="form-control border-0"
                            placeholder="Search">
                    </div>
                </div>
                <div id="likesModalBody">
                    <!-- Home Controller (Get Likes List) -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="sharePopup" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="sharePopupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Share</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-0">
                <div class="position-sticky sticky-top bg-white py-3">
                    <div class="inpleftflex">
                        <i class="fas fa-search text-muted text-center"></i>
                        <input type="text" name="search" id="shareSearch" class="form-control border-0 position-relative" placeholder="Search">
                    </div>
                </div>
                <div class="overflow-y-auto" style="max-height: 35vh;">
                    <div class="share-grid" id="share_list">
                        <!-- Home Controller (Get Shares List) -->
                    </div>
                </div>
                <div class="share-links position-sticky sticky-bottom bg-white border-top py-3" id="indent_link">

                </div>
            </div>
            <div class="modal-footer d-none" id="sendFooter">
                <button type="button" class="formbtn w-100" id="sendButton">Send</button>
            </div>
        </div>
    </div>
</div>

<!-- Report Modal -->
<!-- User Report -->
<div class="modal fade" id="userReport" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="userReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="userReportForm">
                @csrf
                <div class="modal-header">
                    <h4 class="m-0">Report</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-0">
                    <div class="col-sm-12 my-2">
                        <label class="mb-3">Why are you reporting this?</label>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="user_report_spam" value="Spam">
                            <label for="user_report_spam" class="mb-0 text-muted">It's Spam</label>
                        </div>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="user_report_inappropriate"
                                value="Inappropriate">
                            <label for="user_report_inappropriate" class="mb-0 text-muted">It's Inappropriate</label>
                        </div>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="user_report_pretending" value="Pretending">
                            <label for="user_report_pretending" class="mb-0 text-muted">Pretending to be
                                someone</label>
                        </div>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="user_report_harassment"
                                value="Harassment/Bullying">
                            <label for="user_report_harassment" class="mb-0 text-muted">Harassment or bullying</label>
                        </div>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="user_report_something" value="Something">
                            <label for="user_report_something" class="mb-0 text-muted">Something else</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center py-2">
                    <button type="submit" class="formbtn">Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Post/Reel Report -->
<div class="modal fade" id="postReport" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="postReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="postReelReportForm">
                @csrf
                <div class="modal-header">
                    <h4 class="m-0">Report</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-0">
                    <div class="col-sm-12 my-2">
                        <label class="mb-3">Why are you reporting this?</label>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="post_report_like" value="Don't Like">
                            <label for="post_report_like" class="mb-0 text-muted">I just don't like it</label>
                        </div>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="post_report_unwant" value="Bullying/Unwanted">
                            <label for="post_report_unwant" class="mb-0 text-muted">Bullying or unwanted
                                contact</label>
                        </div>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="post_report_suicide" value="Suicide/Disorders">
                            <label for="post_report_suicide" class="mb-0 text-muted">Suicide, self-injury or eating
                                disorders</label>
                        </div>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="post_report_voilence" value="Voilence">
                            <label for="post_report_voilence" class="mb-0 text-muted">Voilence, hate or
                                exploitation</label>
                        </div>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="post_report_selling" value="Restricted">
                            <label for="post_report_selling" class="mb-0 text-muted">Selling or promoting restricted
                                items</label>
                        </div>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="post_report_nudity" value="Nudity/Sexual">
                            <label for="post_report_nudity" class="mb-0 text-muted">Nudity or sexual activity</label>
                        </div>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="post_report_scam" value="Scam/Fraud">
                            <label for="post_report_scam" class="mb-0 text-muted">Scam, Fraud or Spam</label>
                        </div>
                        <div class="d-flex align-items-center column-gap-2 mb-1">
                            <input type="radio" name="message" id="post_report_false" value="False information">
                            <label for="post_report_false" class="mb-0 text-muted">False information</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center py-2">
                    <button type="submit" class="formbtn">Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- User Report -->
<script>
    $(document).ready(function() {
        // User Report AJAX
        $('#userReport').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var userId = button.data('user-id');
            $(this).data('user-id', userId);
        });
        $('#userReport').on('hidden.bs.modal', function() {
            $(this).removeData('user-id');
            $('#userReportForm')[0].reset();
            $('#userReportForm button[type="submit"]').prop('disabled', false).html('Report');
        });
        $('#userReportForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            var reason = $("input[name='message']:checked").val();
            var userId = $('#userReport').data('user-id');
            var submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Reporting...'
            );
            if (!reason) {
                alert("Please select a reason for reporting.");
                return;
            }
            $.ajax({
                url: "{{ route('user.report') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    message: reason,
                    type: 'user',
                    f_id: userId,
                },
                success: function(response) {
                    if (response.success) {
                        showToast("User Reported");
                        $('#userReport').modal('hide');
                        $('[data-user-id="' + userId + '"]')
                            .closest('li')
                            .html(
                                '<span class="dropdown-item text-muted"><i class="fas fa-check-circle text-success pe-1"></i> Reported</span>'
                            );
                    } else {
                        alert("There was an error submitting your report.");
                        submitBtn.prop('disabled', false).html('Report');
                    }
                },
                error: function() {
                    alert("An unexpected error occurred.");
                    submitBtn.prop('disabled', false).html('Report');
                }
            });
        });
    });
</script>

<!-- Post Report -->
<script>
    $(document).ready(function() {
        $('#postReport').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var entityId = button.data('id');
            $(this).data('id', entityId);
        });
        $('#postReport').on('hidden.bs.modal', function() {
            $(this).removeData('id').removeData('entity-type');
            $('#postReelReportForm')[0].reset();
            $('#userReportForm button[type="submit"]').prop('disabled', false).html('Report');
        });
        $('#postReelReportForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            var reason = $("input[name='message']:checked").val();
            var entityId = $('#postReport').data('id');
            var submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Reporting...'
            );
            if (!reason) {
                alert("Please select a reason for reporting.");
                return;
            }
            $.ajax({
                url: "{{ route('post.report') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    message: reason,
                    type: 'post',
                    f_id: entityId,
                },
                success: function(response) {
                    if (response.success) {
                        showToast("Post Reported");
                        $('#postReport').modal('hide');
                        $('[data-id="' + entityId + '"]')
                            .closest('li')
                            .html(
                                '<span class="dropdown-item text-muted"><i class="fas fa-check-circle text-success pe-1"></i> Reported</span>'
                            );
                    } else {
                        alert("There was an error submitting your report.");
                        submitBtn.prop('disabled', false).html('Report');
                    }
                },
                error: function() {
                    alert("An unexpected error occurred.");
                    submitBtn.prop('disabled', false).html('Report');
                }
            });
        });
    });
</script>

<!-- Share Popup Tick Icon -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const shareList = document.getElementById("share_list");
        const sendFooter = document.getElementById("sendFooter");
        const sendButton = document.getElementById("sendButton");
        let selectedIndexes = [];
        let selectedUserData = [];

        if (shareList) {
            shareList.addEventListener("click", function(e) {
                const user = e.target.closest(".modal-user");
                if (!user) return;
                user.classList.toggle("selected");
                const tick = user.querySelector(".tick-icon");
                if (tick) tick.classList.toggle("d-none");
                const selectedUsers = shareList.querySelectorAll(".modal-user.selected");
                selectedIndexes = [...selectedUsers].map(el => el.dataset.index);
                selectedUserData = [...selectedUsers].map(user => {
                    return {
                        id: user.dataset.index,
                        name: user.querySelector("h6")?.textContent.trim(),
                        profileImg: user.querySelector("img.avatar-40")?.src,
                        badgeImg: user.querySelector("img.badge-40")?.src
                    };
                });
                // Show or hide footer
                if (sendFooter) {
                    sendFooter.classList.toggle("d-none", selectedUsers.length === 0);
                }
            });
        }

        // Send button action
        sendButton.addEventListener("click", function() {
            if (selectedIndexes.length === 0) return;
            sendButton.disabled = true;
            const post_id = $('#post_id_share').val();
            const share_type = $('#share_type').val();

            $.ajax({
                url: "{{ route('share.post') }}",
                type: "POST",
                data: {
                    selected: selectedIndexes,
                    post_id: post_id,
                    share_type: share_type,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    showToast(share_type + " Shared successfully!");
                    $("#sharePopup").modal("hide");
                },
                error: function(err) {
                    showToast("Something went wrong!");
                },
                complete: function() {
                    sendButton.disabled = false;
                    $("#share_list").empty("");
                }
            });
        });

    });

    // Reset Share Popup
    function resetSharePopup() {
        const searchInput = document.getElementById("shareSearch");
        if (searchInput) searchInput.value = "";

        document.querySelectorAll(".modal-user").forEach(user => {
            user.classList.remove("selected");
            const tick = user.querySelector(".tick-icon");
            if (tick) tick.classList.add("d-none");
            user.style.display = 'block';
        });

        const sendFooter = document.getElementById("sendFooter");
        if (sendFooter) sendFooter.classList.add("d-none");
    }

    resetSharePopup();
</script>

<!-- Popup Search -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function setupSearch(inputId, cardClass) {
            const searchInput = document.getElementById(inputId);
            if (!searchInput) return;

            searchInput.addEventListener('input', function() {
                const keyword = this.value.toLowerCase();
                document.querySelectorAll(cardClass).forEach(card => {
                    card.style.display = card.textContent.toLowerCase().includes(keyword) ?
                        'block' : 'none';
                });
            });
        }

        setupSearch('likeSearch', '.like-modal');
        setupSearch('shareSearch', '.share-modal');
    });
</script>

<!-- Intent functionality -->
{{--
<script>
    // document.addEventListener("DOMContentLoaded", function() {
    const sharePopup = document.getElementById("sharePopup");

    sharePopup.addEventListener("show.bs.modal", function (event) {
        const trigger = event.relatedTarget;
        const shareUrl = trigger?.getAttribute("data-share-url") || window.location.href;
        const shareText = trigger?.getAttribute("data-share-text") || "";
        // const shareTitle = trigger?.getAttribute("data-share-title") || document.title;

        // Social Links
        sharePopup.querySelector(".share-facebook").href =
            `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`;

        sharePopup.querySelector(".share-whatsapp").href =
            `https://wa.me/?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`;

        sharePopup.querySelector(".share-telegram").href =
            `https://t.me/share/url?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareText)}`;

        sharePopup.querySelector(".share-twitter").href =
            `https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareText)}`;

        // Copy to Clipboard
        sharePopup.querySelector(".share-copy").onclick = function () {
            navigator.clipboard.writeText(shareUrl).then(() => {
                showToast("Link copied to clipboard!");
                // alert("Link copied to clipboard!");
            });
        };

        // Share More
        const moreBtn = sharePopup.querySelector(".share-more");
        moreBtn.onclick = function () {
            if (navigator.share) {
                navigator.share({
                    // title: shareTitle,
                    text: shareText,
                    url: shareUrl
                });
            } else {
                navigator.clipboard.writeText(shareUrl);
                alert("Link copied to clipboard!");
            }
        };

        // Download
        const downloadBtn = sharePopup.querySelector(".share-download");
        downloadBtn.setAttribute("href", shareUrl);
        downloadBtn.setAttribute("download", "shared-file");
    });
    // });
</script> --}}

<script>
    $('#indent_link').on('click', '.share-copy, .share-more', function() {
        const shareUrl = $(this).data('link');
        const shareText = $(this).data('text') || '';

        if ($(this).hasClass('share-copy')) {
            // Copy to clipboard
            navigator.clipboard.writeText(shareUrl).then(() => {
                showToast("Link copied to clipboard!");
            }).catch(err => console.error("Failed to copy: ", err));
        }

        if ($(this).hasClass('share-more')) {
            // Web Share API with fallback

            if (navigator.share) {
                navigator.share({
                    title: shareText,
                    url: shareUrl
                }).catch(error => console.log('Error sharing:', error));
            } else {
                // Fallback for browsers without Web Share API
                navigator.clipboard.writeText(shareUrl).then(() => {
                    showToast("Link copied to clipboard!");
                }).catch(err => console.error("Failed to copy: ", err));
            }
        }
    });
</script>
