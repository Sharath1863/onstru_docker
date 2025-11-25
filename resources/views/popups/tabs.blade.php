<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tabButtons = document.querySelectorAll(
            '#myTab button[data-bs-toggle="tab"]'
        );
        // Lat active tab from localStorage
        const activeTab = localStorage.getItem("activeTab") || "#posts";
        const someTab = document.querySelector(
            `#myTab button[data-bs-target="${activeTab}"]`
        );
        if (someTab) {
            new bootstrap.Tab(someTab).show();
        }
        // Saves active tab
        tabButtons.forEach((button) => {
            button.addEventListener("shown.bs.tab", function (event) {
                const target = event.target.getAttribute("data-bs-target");
                localStorage.setItem("activeTab", target);
            });
        });
    });
</script>