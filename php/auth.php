<?php
    // If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
    require_once ("./php/blockDirectAccess.php");

    // Require the connection files.
    require_once ("_connect.php");
    require_once __DIR__ . "/../vendor/autoload.php";

    // Set response to JSON.
    header('Content-Type: application/json');

    // Unset and destroy session, loggin any users out.
    session_unset();
    session_destroy(); 

    // Check to see if session has started, if not, start one.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Server-side reCAPTCHA verification.
    if (empty($_POST['g-recaptcha-response']))
    {
        echo json_encode(['status' => 'error', 'message' => "reCAPTCHA not complete, please try again."]);
        exit;
    }
    else
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
        $dotenv->load();
        $secret = $_ENV['RECAPTCHA_SECRET_KEY'];
        $verify = file_get_contents( "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $_POST['g-recaptcha-response']);
        $response = json_decode($verify);

        if (!$response || !$response->success)
        {
            echo json_encode(['status' => 'error', 'message' => 'reCAPTCHA verification failed, please try again.']);
            exit;
        }
    }

    // Ensure both email and password have been provided via POST.
    if (isset($_POST['txtEmail']) && isset($_POST['txtPass'])) 
    {
        // Set variables.
        $email = $email = strtolower(trim($_POST['txtEmail']));
        $password = $_POST['txtPass'];

        // Check for blank entries.
        if (empty($email) || empty($password))
        {
            echo json_encode(['status' => 'error', 'message' => "Missing entries."]);
            exit;
        }

        // Validate email format, send error if invalid.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            echo json_encode(['status' => 'error', 'message' => "Invalid email format."]);
            exit;
        }

        // Create DB instance.
        $db = new inquizitiveDB();

        // Query prepares internally, binds the parameters, and executes them. 
        $stmt = $db->Query("CALL GetUserByEmail(?)", [$email]);

        // Ensure query succeeded before using the result resource.
        if ($stmt === false) {
            error_log('DB query failed: ' . mysqli_error($db->connect));
            echo json_encode(['status' => 'error', 'message' => 'Your email or password is invalid.']);
            exit;
        }

        // Check if exactly one user is found.
        if (mysqli_num_rows($stmt) == 1) 
        {
            // Fetch user data with associated account.
            $user = mysqli_fetch_assoc($stmt);

            // Verify password.
            if (password_verify($password, $user['password'])) 
            {

                // Checks to see if account has been approved.
                if ($user['isDisabled'] == 1)
                {
                    echo json_encode(['status' => 'error', 'message' => 'Your account has yet to be approved by a lecturer.']);
                    exit;
                }
            
                // Check if MFA is enabled.
                if ($user['mfaEnabled'] == 1) 
                {
                    // Store temporary session for MFA verification.
                    $_SESSION['mfaUser'] = $user['userUUID'];

                    echo json_encode(['status' => 'mfaEnabled', 'message' => 'Multi-factor authentication required.']);
                    exit;
                }

                // Set session variables.             
                $_SESSION['userUUID'] = $user['userUUID'];
                $_SESSION['firstName'] = $user['firstName'];
                $_SESSION['lastName'] = $user['lastName'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['accessLevel'] = $user['accessLevel'];

                // Update last login on database.
                $currentTime = date("Y-m-d H:i:s");
                $stmt = $db->Query("CALL UpdateLastLogin(?, ?)", [$_SESSION['userUUID'], $currentTime]);

                // Send success response.
                echo json_encode(['status' => 'success', 'message' => 'Welcome to the dashboard!']);
            } 
            else 
            {
                // Invalid password.
                echo json_encode(['status' => 'error', 'message' => 'Your email or password is invalid.']);
            }
        } 
        else 
        {
            // No user found with that email.
            echo json_encode(['status' => 'error', 'message' => 'Your email or password is invalid.']);
        }
    } 
    else 
    {
        // Missing email or password.
        echo json_encode(['status' => 'error', 'message' => 'Please enter an email or password.']);
    }

    // Exit script.
    exit;
?>
