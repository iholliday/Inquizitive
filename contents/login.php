<!DOCTYPE html>
<html lang="en">

    <!-- Separate CSS files faster loading as login doesn't require platform-wide styling  -->
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
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
    <?php
        // Loading reCAPTCHA keys from env file.    
        require __DIR__ . "/../vendor/autoload.php";
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
        $dotenv->load();
    ?>
    <script>
        // Function to enable submit button once reCAPTCHA has been completed.
        function enableSubmitBtn(){
            document.getElementById("submitBtn").disabled = false;
        }
    </script>
    <script src="./js/loginSignupNav.js"></script>

    <?php
        // Checks if user has logged in. Display login page if not.
        if (!isset($_SESSION['userUUID'])) {
    ?>

    <!-- Login Page -->
    <body class="login-page">
        <main class="login">
            <div class="login-background">
                <div class="container login-container p-4 mt-4" data-bs-theme="dark">
                    <div class="grid-container">
                        <div class="grid-item item1">
                            <img src="contents/images/login.png" alt="Login image">
                        </div>
                        <img src="contents/images/logo.png" alt="Logo" class="login-site-logo">
                        <div class="grid-item item2">
                            <h2 class="login-title">SIGN IN</h2>
                            <form method="POST" id="login-form" novalidate>
                                <div class="mb-3">
                                    <input type="text" name="txtEmail" class="form-control login-form-control" id="email" placeholder="Email Address" required>
                                </div>
                                <div class="mb-3">
                                    <input type="password" name="txtPass" id="password" class="form-control login-form-control" placeholder="Password" aria-describedby="passwordHelpBlock" required>
                                </div>
                                <p class="login-help" id="forgot-password"><a href="#" id="forgotPassword">Forgot password?</a></p>
                                <div class="g-recaptcha" data-sitekey="<?= $_ENV['RECAPTCHA_SITE_KEY'] ?>" data-callback="enableSubmitBtn"></div>
                                <button type="submit" id="submitBtn" class="btn btn-danger btn-signin" disabled>SIGN IN</button>
                                <div class="signup-divider">
                                    <p class="login-help" id="sign-up-help"><a href="#" id="signupLink">Don't have an account?</a></p>
                                </div>
                            </form>
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
            <a href="#" id="dashboardLink" style="display:none;">Dashboard</a>
            <script>
                
                $(document).ready(function() {
                    $("#dashboardLink").click();
                });
            </script>
    <?php
        }
    ?>
</html>