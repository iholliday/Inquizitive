<!DOCTYPE html>
<html lang="en">
    
    <!-- Required scripts -->
    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <body class="loginPage">
        <main>
            <div class="loginBackground">
                <div class="container loginContainer p-4 mt-4" data-bs-theme="dark">
                    <div class="grid-container">
                        <div class="grid-item item1">
                            <!--<img src="" alt="Login image">-->
                        </div>
                        <div class="grid-item item2">
                            <h2 class="loginTitle">Sign in</h2>
                            <form method="POST" id="login-form" class="loginForm">
                                <div class="mb-3">
                                    <input type="text" name="txtEmail" class="form-control loginFormControl" id="email" placeholder="Email Address" required>
                                </div>
                                <div class="mb-3">
                                    <input type="password" name="txtPass" id="password" class="form-control loginFormControl" placeholder="Password" aria-describedby="passwordHelpBlock" required>
                                </div>
                                <!--<div class="g-recaptcha" data-sitekey=" TBC " data-callback="enableSubmitBtn"></div>-->
                                <button type="submit" id="submitBtn" class="btn btn-danger btn-signin">Sign in</button> <!--Add disabled once CAPTCHA added-->
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