<!-- Comment Popup -->
<div class="modal fade" id="commentPopup" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="commentPopupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="max-height: 60vh;">
            <div class="modal-header">
                <h4 class="modal-title">Comments</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3" id="commentList" style="overflow-y: auto; flex: 1;">
                <h6 class="text-center">No Comments Yet...</h6>
                <!-- Home Controller (Get Comment List) -->
            </div>
            <div class="p-2 border-top">
                <div class="input-group">
                    <input type="hidden" name="post_id" id="post_id">
                    <input type="text" class="form-control" name="comment" id="commentInput"
                        placeholder="Add a comment...">
                    <button class="formbtn" id="postCommentBtn">Comment</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comment Option -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let comments = document.querySelectorAll(".comment-modal .dropdown");

        comments.forEach(function (comment) {
            let dropdownMenu = comment.querySelector(".dropdown-menu");
            let userContent = comment.querySelector(".user-content");

            userContent.addEventListener("click", function (e) {
                e.stopPropagation();
                document.querySelectorAll(".dropdown-menu.show").forEach(menu => {
                    menu.classList.remove("show");
                });
                document.querySelectorAll(".comment-modal").forEach(c => {
                    c.classList.remove("blurred");
                });
                dropdownMenu.classList.add("show");
                document.querySelectorAll(".comment-modal").forEach(c => {
                    if (c !== comment.closest(".comment-modal")) {
                        c.classList.add("blurred");
                    }
                });

            });
            document.addEventListener("click", function () {
                dropdownMenu.classList.remove("show");
                document.querySelectorAll(".comment-modal").forEach(c => {
                    c.classList.remove("blurred");
                });
            });
        });
    });
</script>