<div class="profile-cards mb-2">
    <div class="container-xl px-sm-0 px-md-2">
        <div class="body-head mb-3">
            <h5>Bank Information</h5>
        </div>
        <div class="cards-content row">
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Bank Name</h4>
                <h6>{{ $profile->bank_name ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Account Holder Name</h4>
                <h6>{{ $profile->acct_holder ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Account Number</h4>
                <h6>{{ $profile->acct_no ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Account Type</h4>
                <h6>{{ $profile->acct_type ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>IFSC Code</h4>
                <h6>{{ $profile->ifsc_code ?? '-' }}</h6>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h4>Branch Name</h4>
                <h6>{{ $profile->branch_name ?? '-' }}</h6>
            </div>
        </div>
    </div>
</div>