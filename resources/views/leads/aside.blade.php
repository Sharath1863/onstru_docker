<div class="flex-sidebar">
    <div class="flex-shrink-0 filter-sidebar">
        <ul class="main-ul list-unstyled ps-0 pt-2">
            @include('leads.menu')
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
                @include('leads.menu')
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
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.filter-checkbox');
        const serviceCards = document.querySelectorAll('.service-card');
        const searchInput = document.getElementById('searchInput');
        const minPriceInput = document.getElementById('minPrice');
        const maxPriceInput = document.getElementById('maxPrice');
        const buildupInput = document.getElementById('buildupSearch');
        const noCard = document.getElementById("noCard");

        checkboxes.forEach(cb => cb.addEventListener('change', applyFilters));
        searchInput.addEventListener('input', applyFilters);
        buildupInput.addEventListener('input', applyFilters);
        minPriceInput.addEventListener('input', applyFilters);
        maxPriceInput.addEventListener('input', applyFilters);

        function applyFilters() {
            const selectedLocation = [...document.querySelectorAll('.loc-filter:checked')].map(cb => cb.value.toLowerCase());
            const selectedType = [...document.querySelectorAll('.type-filter:checked')].map(cb => cb.value.toLowerCase());
            const nrmlSearch = searchInput.value.toLowerCase();
            const minPrice = parseFloat(minPriceInput.value) || 0;
            const maxPrice = parseFloat(maxPriceInput.value) || Infinity;
            const buildupVal = parseFloat(buildupInput.value);

            let hasVisibleCards = false;

            serviceCards.forEach(card => {
                const location = card.dataset.location.toLowerCase();
                const type = card.dataset.type.toLowerCase();
                const search = card.textContent.toLowerCase();
                const price = parseFloat(card.dataset.price) || 0;
                const buildup = parseFloat(card.dataset.buildup);

                const locationMatch = selectedLocation.length === 0 || selectedLocation.includes(location);
                const typeMatch = selectedType.length === 0 || selectedType.includes(type);
                const serviceMatch = nrmlSearch === '' || search.includes(nrmlSearch);
                const priceMatch = price >= minPrice && price <= maxPrice;
                const buildupMatch = isNaN(buildupVal) || buildup === buildupVal;

                if (locationMatch && typeMatch && serviceMatch && priceMatch && buildupMatch) {
                    card.style.display = 'block';
                    hasVisibleCards = true;
                } else {
                    card.style.display = 'none';
                }
            });

            noCard.style.display = hasVisibleCards ? 'none' : 'block';
        }
        applyFilters();
    });
</script>