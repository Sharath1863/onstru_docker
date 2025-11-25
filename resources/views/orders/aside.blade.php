<div class="flex-sidebar">
    <div class="flex-shrink-0 filter-sidebar">
        <ul class="main-ul list-unstyled ps-0 pt-2">
            @include('orders.menu')
        </ul>
    </div>
</div>

<div class="offcanvas offcanvas-start offcanvas-filter" tabindex="-1" id="offcanvas-filter"
    aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <button type="button" class="btn-close bg-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="flex-shrink-0 filter-sidebar">
            <ul class="list-unstyled mt-2 ps-0">
                @include('orders.menu')
            </ul>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        var collapse = $(".filter-sidebar .collapse");
        collapse.on("show.bs.collapse", function () {
            $(this)
                .prev("button")
                .find(".toggle-icon")
                .removeClass("fa-angle-right")
                .addClass("fa-angle-down");
        });

        collapse.on("hide.bs.collapse", function () {
            $(this)
                .prev("button")
                .find(".toggle-icon")
                .removeClass("fa-angle-down")
                .addClass("fa-angle-right");
        });
    });
</script>