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
    <script src="./js/loginSignupNav.js"></script>

    <?php
        // Checks if user has logged in. Display login page if not.
        if (!isset($_SESSION['userUUID'])) {
    ?>

    <!-- Signup Page -->
    <body class="login-page">
        <main class="signup">
            <div class="login-background">
                <div class="container login-container p-4 mt-4" data-bs-theme="dark">
                    <div class="grid-container">
                        <div class="grid-item item1">
                            <img src="contents/images/login.png" alt="Login image">
                        </div>
                        <div class="grid-item item2">
                            <h2 class="login-title signup">SIGN UP</h2>
                            <form method="POST" id="signup-form">
                                <div class="name-row">
                                        <input type="text" name="txtFirstName" class="form-control login-form-control" id="firstName" placeholder="First Name" required>
                                        <input type="text" name="txtLastName" class="form-control login-form-control" id="lastName" placeholder="Last Name" required>
                                </div>
                                <div>
                                    <input type="email" name="txtEmail" class="form-control login-form-control" id="email" placeholder="Email Address" required>
                                </div>
                                <div>
                                    <input type="password" name="txtPass" class="form-control login-form-control" id="password" placeholder="Password" required>
                                </div>
                                <div>
                                    <input type="password" name="txtPassConfirm" class="form-control login-form-control" id="passwordConfirm" placeholder="Confirm Password" required>
                                </div>
                                <div class="g-recaptcha" data-sitekey="PLACEHOLDER" data-callback="enableSubmitBtn"></div>
                                <!-- <div class="g-recaptcha" data-sitekey="<?= $_ENV['RECAPTCHA_SITE_KEY'] ?>" data-callback="enableSubmitBtn"></div>-->
                                <button type="submit" id="submitBtn" class="btn btn-danger btn-signin">SIGN UP</button>
                                <div class="signup-divider">
                                    <p class="login-help" id="sign-up-help"><a href="#" id="loginLink">Already have an account? Sign in</a></p>
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
        require_once ("./js/createAccount.js");
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