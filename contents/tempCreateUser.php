<!DOCTYPE html>
<html lang="en">

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sign Up | Inquizitive</title>

        <!-- Required scripts -->
        <script
            src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
            crossorigin="anonymous">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    </head>

    <body>

        <main>
            <h2>SIGN UP</h2>

            <form method="POST" id="signup-form">

                <div>
                    <input type="text" name="txtFirstName" id="firstName" placeholder="First Name" required>
                </div>

                <div>
                    <input type="text" name="txtLastName" id="lastName" placeholder="Last Name" required>
                </div>

                <div>
                    <input type="email" name="txtEmail" id="email" placeholder="Email Address" required>
                </div>

                <div>
                    <input type="password" name="txtPass" id="password" placeholder="Password" required>
                </div>

                <div>
                    <input type="password" name="txtPassConfirm" id="passwordConfirm" placeholder="Confirm Password" required>
                </div>

                <button type="submit" id="submitBtn">SIGN UP</button>

                <p>
                    <a href="/login">Already have an account? Sign in</a>
                </p>

            </form>
        </main>

    <script>
        <?php
        // Required JS.
        require_once ("./js/createAccount.js");
        ?>
    </script>