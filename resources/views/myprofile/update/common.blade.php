<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label for="name">Name <span>*</span></label>
    <input type="text" class="form-control" name="name" id="name" minlength="3" maxlength="20"
        value="{{ auth()->user()->name }}" pattern="^[A-Za-z\s]+$" title="Only letters and spaces are allowed" required>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label for="username">Username <span>*</span></label>
    <input type="text" class="form-control text-lowercase" name="user_name" id="username" minlength="3" maxlength="15"
        pattern="^(?!.*\.\.)(?!\.)(?!.*\.$)[a-z0-9_.]+$" oninput="checkUser(this)"
        title="Only lowercase letters, numbers, underscore (_) and dot (.) are allowed. Consecutive dots (..) not allowed"
        value="{{ auth()->user()->user_name }}" required>
    <small id="usernameFeedback" class="text-danger"></small>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label for="bio">Bio</label>
    <input type="text" class="form-control" name="bio" id="bio" value="{{ auth()->user()->bio }}">
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label for="gender">Gender <span>*</span></label>
    <select class="form-select" name="gender" id="gender" required>
        <option value="" selected disabled>Select Gender</option>
        <option value="Male" {{ auth()->user()->gender == 'Male' ? 'selected' : '' }}>Male</option>
        <option value="Female" {{ auth()->user()->gender == 'Female' ? 'selected' : '' }}>Female</option>
        <option value="Other" {{ auth()->user()->gender == 'Other' ? 'selected' : '' }}>Others</option>
    </select>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label for="contact">Contact Number <span>*</span></label>
    <input type="number" class="form-control" name="number" id="contact" value="{{ auth()->user()->number }}" readonly>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label for="email">Email ID</label>
    <input type="email" class="form-control" name="email" id="email" value="{{ auth()->user()->email }}">
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label for="location">Location <span>*</span></label>
    <select class="form-select" name="location" id="location" required>
        <option value="" selected disabled>Select Location</option>
        @foreach ($locations as $loc)
            <option value="{{ $loc->id }}" {{ auth()->user()->location == $loc->id ? 'selected' : '' }}>{{ $loc->value }}
            </option>
        @endforeach
    </select>
</div>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#username').on('change', function () {
            let username = $(this).val().trim();
            let feedback = $('#usernameFeedback');
            checkUser(username);
            $.ajax({
                url: "{{ route('check.username') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    username: username
                },
                success: function (response) {
                    if (response.exists) {
                        feedback.text('Username already exisits.');
                        $('.updatebtn').prop('disabled', true);
                    } else {
                        feedback.text('');
                        $('.updatebtn').prop('disabled', false);
                    }
                },
                error: function () {
                    feedback.text('Error checking username. Please try again.');
                }
            });
        });
    });
</script>

<script>
    function checkUser(input) {
        input.value = input.value.replace(/\s+/g, '');
        const regex = /^[a-z._]*$/;
        const errorEl = document.getElementById('usernameFeedback');
        if (!regex.test(input.value)) {
            errorEl.textContent = "Only lowercase letters, underscore (_) and dot (.) are allowed";
        } else {
            errorEl.textContent = "";
        }
    }
</script>