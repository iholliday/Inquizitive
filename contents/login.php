<!DOCTYPE html>
<html lang="en">

    <!-- temp -->
    <link rel="stylesheet" href="./css/global.css" />
    <link rel="stylesheet" href="./css/colours.css" />
    <link rel="stylesheet" href="./css/login.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Inquizitive</title>

    <!-- Required scripts -->
    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        // Function to enable submit button once reCAPTCHA has been completed.
        function enableSubmitBtn(){
            document.getElementById("submitBtn").disabled = false;
        }
    </script>

    <?php
        // Checks if user has logged in. Display login page if not.
        if (!isset($_SESSION['userUUID'])) {
    ?>

    <!-- Login Page -->
    <body class="login-page">
        <main>
            <div class="login-background">
                <div class="container login-container p-4 mt-4" data-bs-theme="dark">
                    <div class="grid-container">
                        <div class="grid-item item1">
                            <img src="contents/images/login.png" alt="Login image">
                        </div>
                        <div class="grid-item item2">
                            <h2 class="login-title">Sign in</h2>
                            <form method="POST" id="login-form">
                                <div class="mb-3">
                                    <input type="text" name="txtEmail" class="form-control login-form-control" id="email" placeholder="Email Address" required>
                                </div>
                                <div class="mb-3">
                                    <input type="password" name="txtPass" id="password" class="form-control login-form-control" placeholder="Password" aria-describedby="passwordHelpBlock" required>
                                </div>
                                <p class="login-help" id="forgot-password"><a href="/">Forgot password?</a></p>
                                <div class="g-recaptcha" data-sitekey="PLACEHOLDER" data-callback="enableSubmitBtn"></div>
                                <!-- <div class="g-recaptcha" data-sitekey="<?= $_ENV['RECAPTCHA_SITE_KEY'] ?>" data-callback="enableSubmitBtn"></div>-->
                                <button type="submit" id="submitBtn" class="btn btn-danger btn-signin" >SIGN IN</button> <!-- add disabled -->
                                <div class="signup-divider">
                                    <p class="login-help" id="sign-up-help"><a href="/">Don't have an account?</a></p>
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
        require_once ("./js/login.js");
        ?>
    </script>

    <?php
        } else 
        {
    ?>
            <script>
                <?php
                // If user has already signed in, link to dashboard page.
                require_once("./js/loadDashboard.js");
                ?>
            </script>
    <?php
        }
    ?>
</html>