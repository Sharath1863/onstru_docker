<div class="body-head mb-3">
    <h5>Delete My Account</h5>
</div>

<div class="container-fluid px-0 form-div">
    <form action="">
        <div class="body-head mb-3">
            <h6>If you need to delete a {{ auth()->user()->as_a ?? 'Consumer' }} account, you will be prompted to
                provide a reason.</h6>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <label class="mb-3">Why are you deleting your {{ auth()->user()->as_a ?? 'Consumer' }} account?</label>
                <div class="d-flex align-items-center justify-content-start column-gap-2 mb-2">
                    <input type="checkbox" id="reason_1">
                    <label for="reason_1" class="text-muted mb-0">No longer working in the construction
                        industry.</label>
                </div>
                <div class="d-flex align-items-center justify-content-start column-gap-2 mb-2">
                    <input type="checkbox" id="reason_2">
                    <label for="reason_2" class="text-muted mb-0">My company has merged or closed operations.</label>
                </div>
                <div class="d-flex align-items-center justify-content-start column-gap-2 mb-2">
                    <input type="checkbox" id="reason_3">
                    <label for="reason_3" class="text-muted mb-0">Switching to another construction
                        marketplace/app.</label>
                </div>
                <div class="d-flex align-items-center justify-content-start column-gap-2 mb-2">
                    <input type="checkbox" id="reason_4">
                    <label for="reason_4" class="text-muted mb-0">Found better pricing options elsewhere.</label>
                </div>
                <div class="d-flex align-items-center justify-content-start column-gap-2 mb-2">
                    <input type="checkbox" id="reason_5">
                    <label for="reason_5" class="text-muted mb-0">Low demand or inquiries for my
                        products/services.</label>
                </div>
                <div class="d-flex align-items-center justify-content-start column-gap-2 mb-2">
                    <input type="checkbox" id="reason_6">
                    <label for="reason_6" class="text-muted mb-0">Unable to manage projects effectively on this
                        app.</label>
                </div>
                <div class="d-flex align-items-center justify-content-start column-gap-2 mb-2">
                    <input type="checkbox" id="reason_7">
                    <label for="reason_7" class="text-muted mb-0">Issues with payment or transaction process.</label>
                </div>
                <div class="d-flex align-items-center justify-content-start column-gap-2 mb-2">
                    <input type="checkbox" id="reason_8">
                    <label for="reason_8" class="text-muted mb-0">Lack of relevant construction materials/services in my
                        area.</label>
                </div>
                <div class="d-flex align-items-center justify-content-start column-gap-2 mb-2">
                    <input type="checkbox" id="reason_9">
                    <label for="reason_9" class="text-muted mb-0">Privacy or security concerns with my data.</label>
                </div>
                <div class="d-flex align-items-center justify-content-start column-gap-2 mb-2">
                    <input type="checkbox" id="reason_10">
                    <label for="reason_10" class="text-muted mb-0">Others.</label>
                </div>
                <div class="col-sm-12 col-md-5 mb-2 d-none" id="other_reason_div">
                    <textarea rows="3" class="form-control" name="others" id="reason_10" placeholder="Enter your Reason"></textarea>
                </div>
            </div>

            <div class="col-sm-12 d-flex align-items-center justify-content-sm-start justify-content-md-end mt-3">
                <button class="formbtn">Confirm</button>
            </div>
        </div>
    </form>
</div>

<script>
    const checkbox = document.getElementById('reason_10');
    const otherDiv = document.getElementById('other_reason_div');

    checkbox.addEventListener('change', function () {
        if (this.checked) {
            otherDiv.classList.remove('d-none');
            otherDiv.classList.add('d-block');
        } else {
            otherDiv.classList.remove('d-block');
            otherDiv.classList.add('d-none');
        }
    });
</script>