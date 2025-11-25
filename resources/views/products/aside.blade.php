<div class="flex-sidebar">
    <div class="flex-shrink-0 filter-sidebar">
        <ul class="main-ul list-unstyled ps-0 pt-2">
            @include('products.menu')
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
                @include('products.menu')
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
    // document.addEventListener("DOMContentLoaded", function() {
    //     const checkboxes = document.querySelectorAll('.filter-checkbox');

    //     const keywordInput = document.getElementById('keywordSearch');
    //     const minPriceInput = document.getElementById('minPrice');
    //     const maxPriceInput = document.getElementById('maxPrice');
    //     const noCard = document.getElementById('noCard');

    //     // console.log(productCards);

    //     // Run on checkbox/input changes
    //     checkboxes.forEach(cb => cb.addEventListener('change', applyFilters));
    //     keywordInput.addEventListener('input', applyFilters);
    //     minPriceInput.addEventListener('input', applyFilters);
    //     maxPriceInput.addEventListener('input', applyFilters);

    //     function applyFilters() {
    //         const productCards = document.querySelectorAll('.product-card');
    //         const selectedLocation = [...document.querySelectorAll('.loc-filter:checked')].map(cb => cb.value
    //             .toLowerCase());
    //         const selectedCategories = [...document.querySelectorAll('.category-filter:checked')].map(cb => cb
    //             .value.toLowerCase());
    //         const selectedStocks = [...document.querySelectorAll('.stock-filter:checked')].map(cb => cb.value
    //             .toLowerCase());
    //         const selectedSizes = [...document.querySelectorAll('.size-filter:checked')].map(cb => cb.value
    //             .toLowerCase());
    //         const selectedHighlights = [...document.querySelectorAll('.highlight-filter:checked')].map(cb => cb
    //             .value);

    //         const minPrice = parseFloat(minPriceInput.value) || 0;
    //         const maxPrice = parseFloat(maxPriceInput.value) || Infinity;
    //         const keyword = keywordInput.value.toLowerCase();
    //         // console.log(keyword)
    //         let noImage = false;
    //         productCards.forEach(card => {
    //             const location = card.dataset.location?.toLowerCase();
    //             const category = card.dataset.category?.toLowerCase();
    //             const stock = card.dataset.stock?.toLowerCase();
    //             const price = parseFloat(card.dataset.price) || 0;
    //             const size = card.dataset.size?.toLowerCase();
    //             const text = card.textContent.toLowerCase();
    //             const highlight = card.dataset.highlight;


    //             const locationMatch = selectedLocation.length === 0 || selectedLocation.includes(
    //                 location);
    //             const categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(
    //                 category);
    //             const stockMatch = selectedStocks.length === 0 || selectedStocks.includes(stock);
    //             const priceMatch = price >= minPrice && price <= maxPrice;
    //             const sizeMatch = selectedSizes.length === 0 || selectedSizes.includes(size);
    //             const keywordMatch = keyword === '' || text.includes(keyword);
    //             const highlightMatch = selectedHighlights.length === 0 || selectedHighlights.includes(
    //                 highlight);

    //             if (locationMatch && categoryMatch && stockMatch && priceMatch && sizeMatch &&
    //                 keywordMatch && highlightMatch) {
    //                 card.style.display = 'block';
    //                 noImage = true;
    //             } else {
    //                 card.style.display = 'none';
    //             }
    //         });

    //         noCard.style.display = noImage ? 'none' : 'block';
    //     }
    //     applyFilters();
    // });
</script>