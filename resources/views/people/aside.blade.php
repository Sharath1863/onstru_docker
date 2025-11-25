<div class="flex-sidebar">
    <div class="flex-shrink-0 filter-sidebar">
        <ul class="main-ul list-unstyled ps-0 pt-2">
            @include('people.menu')
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
                @include('people.menu')
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


<!-- Filtering Script -->
<script>
    // document.addEventListener('DOMContentLoaded', function () {
    //     const checkboxes = document.querySelectorAll('.filter-checkbox');
    //     const peopleCards = document.querySelectorAll('.people-card');
    //     const searchInput = document.getElementById('searchInput');
    //     const noCard = document.getElementById("noCard");

    //     checkboxes.forEach(cb => cb.addEventListener('change', applyFilters));
    //     searchInput.addEventListener('input', applyFilters);

    //     function applyFilters() {
    //         const selectedLocation = [...document.querySelectorAll('.loc-filter:checked')].map(cb => cb.value.toLowerCase());
    //         const selectedCategory = [...document.querySelectorAll('.category-filter:checked')].map(cb => cb.value.toLowerCase());
    //         const nrmlSearch = searchInput.value.toLowerCase();

    //         let hasVisibleCards = false;

    //         peopleCards.forEach(card => {
    //             const location = (card.dataset.location || '').toLowerCase();
    //             const category = (card.dataset.category || '').toLowerCase();
    //             const search = card.textContent.toLowerCase();

    //             const combinedSearch = search + ' ' + category + ' ' + ' ' + location;

    //             const locationMatch = selectedLocation.length === 0 || selectedLocation.includes(location);
    //             const categoryMatch = selectedCategory.length === 0 || selectedCategory.includes(category);
    //             const peopleMatch = nrmlSearch === '' || combinedSearch.includes(nrmlSearch);

    //             if (locationMatch && categoryMatch && peopleMatch) {
    //                 card.style.display = 'block';
    //                 hasVisibleCards = true;
    //             } else {
    //                 card.style.display = 'none';
    //             }
    //         });
    //         noCard.style.display = hasVisibleCards ? 'none' : 'block';
    //     }
    //     applyFilters();
    // });
</script>