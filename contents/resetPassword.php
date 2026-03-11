<!DOCTYPE html>
<html lang="en">

    <!-- Separate CSS files faster loading as password-reset doesn't require platform-wide styling  -->
    <link rel="shortcut icon" href="./../favicon.ico" type="image/x-icon">
    <link rel="icon" href="./../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/global.css" />
    <link rel="stylesheet" href="../css/colours.css" />
    <link rel="stylesheet" href="../css/login.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | Inquizitive</title>

    <!-- Required scripts -->
    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Password Reset Page -->
    <body class="login-page password-reset">
        <main class="password-reset">
            <div class="login-background">
                <div class="container password-reset login-container p-4 mt-4" data-bs-theme="dark">
                    <div class="grid-container">
                        <img src="../contents/images/logo.png" alt="Logo" class="login-site-logo">
                        <div class="grid-item item2">
                            <h2 class="login-title">RESET<br>PASSWORD</h2>
                            <form id="reset-form" method="POST" novalidate>
                                <div class="signup-password">
                                    <input type="password" name="txtPass" class="form-control login-form-control" id="password" placeholder="New Password" required>
                                    <span id="password-indicator" ></span>
                                </div>
                                <div class="mb-3">
                                    <input type="password" name="txtConfirmPassword" id="confirmPassword" class="form-control login-form-control" placeholder="Confirm Password" required>
                                </div>
                                <button type="submit" class="btn btn-danger btn-signin">RESET</button>
                            </form>
                            <div class="signup-divider">
                                <p class="login-help"><a href="#" id="loginLink">Back to login page</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
        
    <script>
        <?php
        // Required JS.
        require_once ("../js/resetPassword.js");
        require_once ("../js/createAccount.js");
        ?>
    </script> 
</html>