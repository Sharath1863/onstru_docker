<div class="flex-sidebar">
    <div class="flex-shrink-0 filter-sidebar">
        <ul class="main-ul list-unstyled ps-0 pt-2">
            @include('hire.menu')
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
                @include('hire.menu')
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
        const hireRows = document.querySelectorAll('.hire-row');
        const keywordInput = document.getElementById('keywordSearch');
        const noCard = document.getElementById("noCard");
        const table = document.getElementById("table");

        // Listen to checkbox and keyword input changes
        checkboxes.forEach(cb => cb.addEventListener('change', applyFilters));
        keywordInput.addEventListener('input', applyFilters);

        function applyFilters() {
            const selectedLocation = [...document.querySelectorAll('.location-filter:checked')].map(cb => cb
                .value.toLowerCase());
            const selectedCategories = [...document.querySelectorAll('.category-filter:checked')].map(cb => cb
                .value.toLowerCase());
            const selectedTypes = [...document.querySelectorAll('.type-filter:checked')].map(cb => cb.value
                .toLowerCase());
            const selectedExperience = [...document.querySelectorAll('.exp-filter:checked')].map(cb => cb.value
                .toLowerCase());
            const keyword = keywordInput.value.toLowerCase();

            let hasVisibleRows = false;

            hireRows.forEach(row => {
                const location = row.dataset.location?.toLowerCase();
                const category = row.dataset.category?.toLowerCase();

                const type = row.dataset.type?.toLowerCase();
                const exp = row.dataset.exp?.toLowerCase();
                const text = row.textContent.toLowerCase();

                const locationMatch = selectedLocation.length === 0 || selectedLocation.some(loc => location.split(',').map(l => l.trim()).includes(loc));
                const categoryMatch = selectedCategories.length === 0 || selectedCategories.some(cat => category.split(',').map(c => c.trim()).includes(cat));
                const typeMatch = selectedTypes.length === 0 || type.split(',').some(t => selectedTypes.includes(t.trim()));
                const expMatch = selectedExperience.length === 0 || selectedExperience.includes(exp) || (selectedExperience.includes("5") && parseInt(exp) > 5);
                const keywordMatch = keyword === '' || text.includes(keyword);

                if (categoryMatch && typeMatch && expMatch && keywordMatch && locationMatch) {
                    row.style.display = '';
                    hasVisibleRows = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide the table and no results message
            if (hasVisibleRows) {
                table.style.display = '';
                noCard.style.display = 'none';
            } else {
                table.style.display = 'none';
                noCard.style.display = 'block';
            }
        }

        // Apply filters on page load
        applyFilters();
    });
</script>