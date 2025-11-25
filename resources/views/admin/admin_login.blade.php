<!DOCTYPE html>
<html>

<head>
    <title>Login</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/admin/Icon.png') }}" sizes="32*32" type="image/png">

    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Font / Icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/admin/login.css">
    <link rel="stylesheet" href="assets/css/admin/common.css">

</head>

<body>

    <div class="wrapper">
        <span class="rotate-bg"></span>

        <div class="form-box login">
            <h2 class="title">Log In</h2>
            <form action="{{ route('admin-login') }}" method="POST">
                @csrf
                <div class="row mx-0">
                    <div class="input-box col-sm-12 col-md-12 mb-3">
                        <input type="text" name="name" id="empcode" autofocus required>
                        <label for="">User Name</label>
                        <i class="fa-regular fa-id-badge"></i>
                    </div>
                    <div class="input-box col-sm-12 col-md-12 mb-3">
                        <input type="password" class="ctct" name="password" id="password" required>
                        <label for="">Password</label>
                        <i class="fa-solid fa-eye-slash" id="passHide"
                            onclick="togglePasswordVisibility('password', 'passShow', 'passHide')"
                            style="display:none; cursor:pointer;"></i>
                        <i class="fa-solid fa-eye" id="passShow"
                            onclick="togglePasswordVisibility('password', 'passShow', 'passHide')"
                            style="cursor:pointer;"></i>
                    </div>
                    <div class="mt-4 col-sm-12 col-md-12">
                        <button type="submit" class="loginbtn">Log In</button>
                    </div>
                </div>
            </form>

        </div>

        <div class="info-text login">
            <img src="{{ asset('assets/images/Logo_Admin.png') }}" class="animation" style="--i:0; --j:20">
        </div>

    </div>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

<script>
    function togglePasswordVisibility(inputId, showId, hideId) {
        let $input = $('#' + inputId);
        let $passShow = $('#' + showId);
        let $passHide = $('#' + hideId);

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $passShow.hide();
            $passHide.show();
        } else {
            $input.attr('type', 'password');
            $passShow.show();
            $passHide.hide();
        }
    }
</script>

</html>