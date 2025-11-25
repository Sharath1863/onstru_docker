<div class="flex-sidebar">
    <div class="flex-shrink-0 filter-sidebar">
        <ul class="main-ul list-unstyled ps-0 pt-2">
            @include('jobs.menu')
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
                @include('jobs.menu')
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
        const jobCards = document.querySelectorAll('.job-card');
        const keywordInput = document.getElementById('keywordSearch');
        const noCard = document.getElementById("noCard");
        const minSalaryInput = document.getElementById('minSalary');
        const maxSalaryInput = document.getElementById('maxSalary');

        // delegated listener
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('filter-checkbox')) {
                applyFilters();
            }
        });

        checkboxes.forEach(cb => cb.addEventListener('change', applyFilters));
        minSalaryInput.addEventListener('input', applyFilters);
        maxSalaryInput.addEventListener('input', applyFilters);
        keywordInput.addEventListener('input', applyFilters);

        function applyFilters() {
            const selectedLocation = [...document.querySelectorAll('.location-filter:checked')].map(cb => cb
                .value.toLowerCase());
            const selectedSubLocation = [...document.querySelectorAll('.sub-location-filter:checked')].map(cb =>
                cb.value.toLowerCase());
            const selectedCategories = [...document.querySelectorAll('.category-filter:checked')].map(cb => cb
                .value.toLowerCase());
            const selectedTypes = [...document.querySelectorAll('.type-filter:checked')].map(cb => cb.value
                .toLowerCase());
            const selectedExperience = [...document.querySelectorAll('.exp-filter:checked')].map(cb => cb.value
                .toLowerCase());
            const selectedSalaries = [...document.querySelectorAll('.salary-filter:checked')].map(cb => cb
                .value);
            const selectedHighlights = [...document.querySelectorAll('.highlight-filter:checked')].map(cb => cb
                .value);
            const minSalary = parseFloat(minSalaryInput.value) || 0;
            const maxSalary = parseFloat(maxSalaryInput.value) || Infinity;
            const keyword = keywordInput.value.toLowerCase();

            let noImage = false;
            jobCards.forEach(card => {
                const location = card.dataset.location?.toLowerCase();
                const sublocation = card.dataset.sublocation?.toLowerCase();
                const category = card.dataset.category?.toLowerCase();
                const type = card.dataset.type?.toLowerCase();
                const exp = card.dataset.exp?.toLowerCase();
                const salary = parseInt(card.dataset.salary) || 0;
                const text = card.textContent.toLowerCase();
                const highlight = card.dataset.highlight;

                const locationMatch = selectedLocation.length === 0 || selectedLocation.includes(
                    location);
                const sublocationMatch = selectedSubLocation.length === 0 || selectedSubLocation
                    .includes(sublocation);
                const categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(
                    category);
                const typeMatch = selectedTypes.length === 0 || selectedTypes.includes(type);
                const expMatch = selectedExperience.length === 0 || selectedExperience.includes(exp);
                const keywordMatch = keyword === '' || text.includes(keyword);
                const highlightMatch = selectedHighlights.length === 0 || selectedHighlights.includes(
                    highlight);
                const salaryMatch = salary >= minSalary && salary <= maxSalary;

                if (categoryMatch &&
                    typeMatch && expMatch && salaryMatch && keywordMatch && locationMatch &&
                    sublocationMatch && highlightMatch) {
                    card.style.display = 'block';
                    noImage = true;
                } else {
                    card.style.display = 'none';
                }
            });
            noCard.style.display = noImage === false ? 'block' :
                'none';
        }
        applyFilters();
    });
</script>