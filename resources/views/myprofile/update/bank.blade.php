<div class="profile-cards mb-2">
    <div class="container-xl form-div">
        <div class="body-head mb-3">
            <h5>Bank Information</h5>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="bank_name">Bank Name</label>
                <input type="text" class="form-control" name="bank_name" id="bank_name" value="{{ $profile->bank_name ?? '' }}">
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="acct_holder">Account Holder Name</label>
                <input type="text" class="form-control" name="acct_holder" id="acct_holder" value="{{ $profile->acct_holder ?? '' }}">
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="acct_no">Account Number</label>
                <input type="text" class="form-control" name="acct_no" id="acct_no" value="{{ $profile->acct_no ?? '' }}">
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="acct_type">Account Type</label>
                <select class="form-select" name="acct_type" id="acct_type">
                    <option value="" selected disabled>Select Account Type</option>
                    <option value="Savings" {{ optional($profile)->acct_type == 'Savings' ? 'selected' : '' }}>Savings</option>
                    <option value="Current" {{ optional($profile)->acct_type == 'Current' ? 'selected' : '' }}>Current</option>
                </select>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            
                <label for="ifsc_code">IFSC Code</label>
                <input type="text" class="form-control" name="ifsc_code" id="ifsc_code" value="{{ $profile->ifsc_code ?? '' }}">
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="branch_name">Branch Name</label>
                <input type="text" class="form-control" name="branch_name" id="branch_name" value="{{ $profile->branch_name ?? '' }}">
            </div>
        </div>
    </div>
</div>