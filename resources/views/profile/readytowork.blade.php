<!-- Add Ready to Work Modal -->
<div class="modal fade" id="addready" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addreadyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Ready to Work</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addReadyForm" action="{{ route('ready-to-work.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row mt-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <label class="my-2">Wallet : <span class="text-muted">₹
                                    {{ auth()->user()->balance }}</span></label>
                            <label class="my-2">Charge : <span class="text-muted">₹
                                    {{ $readyto_work_charge }} / Day</span></label>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="ready_jobtitle_add">Job Title <span>*</span></label>
                            <select id="ready_jobtitle_add" name="job_titles[]" class="form-select border-0"
                                multiple="multiple" required autofocus>
                                @if (isset($jobTitles) && count($jobTitles) > 0)
                                    @foreach ($jobTitles as $id => $title)
                                        <option value="{{ $title }}">{{ $title }}</option>
                                    @endforeach
                                @else
                                    <option value="No Result Found">No Result Found</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="ready_worktype_add">Working Type <span class="text-danger">*</span></label>
                            <select id="ready_worktype_add" name="work_types[]" class="form-select border-0"
                                multiple="multiple" required>
                                <option value="On-Site">On-Site</option>
                                <option value="Remote">Remote</option>
                                <option value="Full-Time">Full-Time</option>
                                <option value="Part-Time">Part-Time</option>
                                <option value="Freelance">Freelance</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="ready_location_add">Location <span class="text-danger">*</span></label>
                            <select id="ready_location_add" name="locations[]" class="form-select border-0"
                                multiple="multiple" required>
                                @if (isset($locations) && count($locations) > 0)
                                    @foreach ($locations as $id => $location)
                                        <option value="{{ $id }}">{{ $location }}</option>
                                    @endforeach
                                @else
                                    <option value="No Result Found">No Result Found</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="ready_exp_add">Experience <span class="text-danger">*</span></label>
                            <select id="ready_exp_add" name="experience" class="form-select" required>
                                <option value="" selected disabled>Select Experience</option>
                                <option value="Fresher">Fresher</option>
                                <option value="1">1+ Years</option>
                                <option value="2">2+ Years</option>
                                <option value="3">3+ Years</option>
                                <option value="4">4+ Years</option>
                                <option value="5">More than 5 Years</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="ready_payment_add">Payment <span class="text-danger">*</span></label>
                            <select id="ready_payment_add" name="days" class="form-select border-0" required>
                                <option value="30">30 Days - ₹{{ $readyto_work_charge * 30 }}</option>
                                <option value="182">6 Months - ₹{{ $readyto_work_charge * 182 }}</option>
                                <option value="365">1 Year - ₹{{ $readyto_work_charge * 365 }}</option>
                                <option value="730">2 Years - ₹{{ $readyto_work_charge * 730 }}</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="ready_resume_add">Upload Resume (Max 15 MB) <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="ready_resume_add" name="resume"
                                accept=".pdf,.doc,.docx" onchange="validateFile(this)" required>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label>Notes <span>*</span></label>
                            <h6>Amount will be deducted from the wallet.</h6>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2 d-flex align-items-center column-gap-2">
                            <input type="checkbox" id="readyPay" name="readyPay" required>
                            <label class="mb-0" for="readyPay">Agree to Pay</label>
                        </div>
                        <small id="balanceError" class="text-danger mt-1" style="display: none;"></small>

                        <div class="d-flex justify-content-center align-items-center column-gap-2 mt-3">
                            <a href="{{ url('wallet') }}" target="_blank">
                                <button type="button" class="removebtn" id="readyToWork_recharge_button"
                                    style="display: none;">Recharge</button>
                            </a>
                            <button type="submit" class="formbtn">Save Details</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Ready to Work Modal -->
@if (isset($readyToWork) && $readyToWork)
    <div class="modal fade" id="editready" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editreadyLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Edit Ready to Work</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editReadyForm" action="{{ route('ready-to-work.update', $readyToWork->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="ready_jobtitle_edit">Job Title <span class="text-danger">*</span></label>
                                <select id="ready_jobtitle_edit" name="job_titles[]" class="form-select border-0"
                                    multiple="multiple" autofocus>
                                    @if (isset($jobTitles) && count($jobTitles) > 0)
                                        @foreach ($jobTitles as $id => $title)
                                            <option value="{{ $title }}" {{ in_array($title, $readyToWork->job_titles ?? []) ? 'selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    @else
                                        <option value="Software Developer" {{ in_array('Software Developer', $readyToWork->job_titles ?? []) ? 'selected' : '' }}>
                                            Software Developer</option>
                                        <option value="Web Designer" {{ in_array('Web Designer', $readyToWork->job_titles ?? []) ? 'selected' : '' }}>
                                            Web Designer</option>
                                        <option value="Project Manager" {{ in_array('Project Manager', $readyToWork->job_titles ?? []) ? 'selected' : '' }}>
                                            Project Manager</option>
                                        <option value="Data Analyst" {{ in_array('Data Analyst', $readyToWork->job_titles ?? []) ? 'selected' : '' }}>
                                            Data Analyst</option>
                                        <option value="Marketing Specialist" {{ in_array('Marketing Specialist', $readyToWork->job_titles ?? []) ? 'selected' : '' }}>
                                            Marketing Specialist</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="ready_worktype_edit">Working Type <span class="text-danger">*</span></label>
                                <select id="ready_worktype_edit" name="work_types[]" class="form-select border-0"
                                    multiple="multiple">
                                    <option value="On-Site" {{ in_array('On-Site', $readyToWork->work_types ?? []) ? 'selected' : '' }}>
                                        On-Site</option>
                                    <option value="Remote" {{ in_array('Remote', $readyToWork->work_types ?? []) ? 'selected' : '' }}>
                                        Remote</option>
                                    <option value="Full-Time" {{ in_array('Full-Time', $readyToWork->work_types ?? []) ? 'selected' : '' }}>
                                        Full-Time</option>
                                    <option value="Part-Time" {{ in_array('Part-Time', $readyToWork->work_types ?? []) ? 'selected' : '' }}>
                                        Part-Time</option>
                                    <option value="Freelance" {{ in_array('Freelance', $readyToWork->work_types ?? []) ? 'selected' : '' }}>
                                        Freelance</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="ready_location_edit">Location <span class="text-danger">*</span></label>
                                <select id="ready_location_edit" name="locations[]" class="form-select border-0"
                                    multiple="multiple">
                                    @if (isset($locations) && count($locations) > 0)
                                        @foreach ($locations as $id => $location)
                                            <option value="{{ $id }}" {{ in_array((string) $id, $readyToWork->locations ?? []) ? 'selected' : '' }}>
                                                {{ $location }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="Salem" {{ in_array('Salem', $readyToWork->locations ?? []) ? 'selected' : '' }}>Salem</option>
                                        <option value="Chennai" {{ in_array('Chennai', $readyToWork->locations ?? []) ? 'selected' : '' }}>Chennai</option>
                                        <option value="Coimbatore" {{ in_array('Coimbatore', $readyToWork->locations ?? []) ? 'selected' : '' }}>Coimbatore</option>
                                        <option value="Bangalore" {{ in_array('Bangalore', $readyToWork->locations ?? []) ? 'selected' : '' }}>Bangalore</option>
                                        <option value="Hyderabad" {{ in_array('Hyderabad', $readyToWork->locations ?? []) ? 'selected' : '' }}>Hyderabad</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="ready_exp_edit">Experience <span class="text-danger">*</span></label>
                                <select id="ready_exp_edit" name="experience" class="form-select">
                                    <option value="" disabled>Select Experience</option>
                                    <option value="Fresher" {{ $readyToWork->experience === 'Fresher' ? 'selected' : '' }}>
                                        Fresher</option>
                                    <option value="1" {{ $readyToWork->experience === '1' ? 'selected' : '' }}>1+
                                        Years
                                    </option>
                                    <option value="2" {{ $readyToWork->experience === '2' ? 'selected' : '' }}>2+
                                        Years
                                    </option>
                                    <option value="3" {{ $readyToWork->experience === '3' ? 'selected' : '' }}>3+
                                        Years
                                    </option>
                                    <option value="4" {{ $readyToWork->experience === '4' ? 'selected' : '' }}>4+
                                        Years
                                    </option>
                                    <option value="5" {{ $readyToWork->experience === '5' ? 'selected' : '' }}>
                                        More than 5
                                        Years</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="ready_payment_edit">Payment <span class="text-danger">*</span></label>
                                <select id="ready_payment_edit" name="payments" class="form-select border-0" disabled>

                                    <option value="30" {{ $readyToWork->days === 30 ? 'selected' : '' }}>30 Days -
                                        ₹{{ $readyto_work_charge * 30 }}</option>
                                    <option value="61" {{ $readyToWork->days === 61 ? 'selected' : '' }}>6 Months
                                        - ₹{{ $readyto_work_charge * 61 }}</option>
                                    <option value="365" {{ $readyToWork->days === 365 ? 'selected' : '' }}>1 Year -
                                        ₹{{ $readyto_work_charge * 365 }}</option>
                                    <option value="730" {{ $readyToWork->days === 730 ? 'selected' : '' }}>2 Years
                                        - ₹{{ $readyto_work_charge * 730 }}</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="ready_resume_edit">Upload Resume (Max 15 MB) <span>*</span></label>
                                @if ($readyToWork->resume_path)
                                    <div class="current-resume mb-2">
                                        <a href="{{ Storage::url($readyToWork->resume_path) }}" target="_blank"
                                            class="d-flex align-items-center text-decoration-none">
                                            <i class="fas fa-file-pdf me-2"></i>
                                            <span>{{ basename($readyToWork->resume_path) }}</span>
                                        </a>
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="ready_resume_edit" name="resume"
                                    accept=".pdf,.doc,.docx" onchange="validateFile(this)">
                            </div>

                            <div class="d-flex justify-content-center align-items-center mt-3">
                                <button type="submit" class="formbtn">Update Details</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select 2 -->
<script>
    $(document).ready(function () {
        const configs = [
            { parent: '#addready', fields: ['ready_jobtitle_add', 'ready_worktype_add', 'ready_location_add', 'ready_payment_add'] },
            { parent: '#editready', fields: ['ready_jobtitle_edit', 'ready_worktype_edit', 'ready_location_edit', 'ready_payment_edit'] }
        ];
        configs.forEach(config => {
            config.fields.forEach(field => {
                $(`#${field}`).select2({
                    width: "100%",
                    placeholder: "Select Options",
                    allowClear: true,
                    dropdownParent: $(config.parent)
                });
            });
        });
    });
</script>

<!-- Resume & Form Validation -->
<script>
    // Resume validation
    function validateFile(input) {
        if (input.files.length === 0) return true;
        const file = input.files[0];
        const allowedExtensions = ['pdf', 'doc', 'docx'];
        const ext = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(ext)) {
            showToast("Only PDF, DOC, or DOCX files are allowed.");
            input.value = "";
            return false;
        }
        if (file.size > 15 * 1024 * 1024) { // 5MB
            showToast("File size must be less than 15 MB.");
            input.value = "";
            return false;
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Resume input listeners
        const addResume = document.getElementById('ready_resume_add');
        if (addResume) addResume.addEventListener('change', () => validateFile(addResume));
        const editResume = document.getElementById('ready_resume_edit');
        if (editResume) editResume.addEventListener('change', () => validateFile(editResume));
        // Form validation & button loader
        ['addReadyForm', 'editReadyForm'].forEach(formId => {
            const form = document.getElementById(formId);
            if (!form) return;
            form.addEventListener('submit', function (e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.style.borderColor = '#dc3545';
                        isValid = false;
                    } else {
                        field.style.borderColor = '';
                    }
                });
                const resumeInput = form.querySelector('input[type="file"]');
                if (resumeInput && !validateFile(resumeInput)) {
                    isValid = false;
                }
                if (!isValid) {
                    e.preventDefault();
                    showToast("Please fill all required fields correctly.");
                    return false;
                }
                // Button loading state
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                }
            });
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkbox = document.getElementById("readyPay");
        const daysSelect = document.getElementById("ready_payment_add");
        const chargePerDay = {{ $readyto_work_charge }};
        const walletBalance = {{ auth()->user()->balance }};
        const form = document.getElementById("addReadyForm");
        const errorMsg = document.getElementById("balanceError");
        const rechargeButton = document.getElementById("readyToWork_recharge_button");
        function getTotalCost() {
            const selectedDays = parseInt(daysSelect.value) || 0;
            return selectedDays * chargePerDay;
        }
        function showError(message) {
            errorMsg.textContent = message;
            errorMsg.style.display = 'block';
        }
        function hideError() {
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
        }
        function toggleRechargeButton(show) {
            rechargeButton.style.display = show ? 'inline-block' : 'none';
        }
        function validateBalance() {
            const totalCost = getTotalCost();
            if (totalCost > walletBalance) {
                checkbox.checked = false;
                showError(`Insufficient balance. Required: ₹${totalCost}, Available: ₹${walletBalance}`);
                toggleRechargeButton(true);
                return false;
            }
            hideError();
            toggleRechargeButton(false);
            return true;
        }
        // Run once on load in case a duration is preselected
        validateBalance();
        // Real-time validation
        daysSelect.addEventListener("change", function () {
            if (checkbox.checked) {
                validateBalance();
            } else {
                validateBalance(); // even if not checked, we want to toggle button
            }
        });
        checkbox.addEventListener("change", function () {
            if (checkbox.checked) {
                validateBalance();
            }
        });
        form.addEventListener("submit", function (e) {
            if (!validateBalance()) {
                e.preventDefault();
            }
        });
    });
</script>