<!-- Toast Container -->
<div class="toast-main" style="z-index: 1100">
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body">
            <!-- Message goes here -->
            <img src="{{ asset('assets/images/Favicon_2.png') }}" height="30px" alt="">
            <span id="toast-message"></span>
        </div>
    </div>
</div>

<!-- Toast Data -->
<div id="toast-data" data-success="{{ session('success') }}" data-error="{{ $errors->any() ? $errors->first() : '' }}">
</div>

<!-- Toast Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toastData = document.getElementById('toast-data');
        const success = toastData.getAttribute('data-success');
        const error = toastData.getAttribute('data-error');
        if (success) {
            showToast(success, "success");
        } else if (error) {
            showToast(error, "danger");
        }
    });

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        toastMessage.textContent = message;
        // Show toast
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
</script>