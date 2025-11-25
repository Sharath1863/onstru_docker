$(document).on("click", ".share-btn", function () {
    let job_id = $(this).data("job-id");
    let $button = $(this);

    if ($button.data("loading")) return; // prevent double click
    $button.data("loading", true);

    let share_type = $(this).data("share-type");

    $.ajax({
        url: window.appData.shareUrl,  // from Blade
        method: "POST",
        data: {
            post_id: job_id,
            share_type: share_type,
            _token: window.appData.csrf  // from Blade
        },
        success: function (response) {
            $("#share_list").html(response.html);
            $("#indent_link").html(response.link);
            $("#share_list").data("share-url", response.share_url);
            $("#share_list").data("share-text", response.share_text);
        },
        error: function () {
            $("#share_list").html(
                '<h6 class="text-center text-danger">Failed to load</h6>'
            );
        },
        complete: function () {
            $button.data("loading", false);
        }
    });
});
