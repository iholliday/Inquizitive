<?php
    // If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
    require_once ("./php/blockDirectAccess.php");

    // Require the database connection file.
    require_once ("_connect.php");

    // Set response to JSON.
    header('Content-Type: application/json');

    // Unset and destroy session, loggin any users out.
    session_unset();
    session_destroy(); 

    // Check to see if session has started, if not, start one.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Ensure both email and password have been provided via POST.
    if (isset($_POST['txtEmail']) && isset($_POST['txtPass'])) 
    {
        // Set variables.
        $email = $email = strtolower(trim($_POST['txtEmail']));
        $password = $_POST['txtPass'];

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
                // Set session variables.             
                $_SESSION['userUUID'] = $user['userUUID'];
                $_SESSION['firstName'] = $user['firstName'];
                $_SESSION['lastName'] = $user['lastName'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['accessLevel'] = $user['accessLevel'];

                // Send success response.
                echo json_encode(['status' => 'success', 'message' => 'Login successful']);
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
        echo json_encode(['status' => 'error', 'message' => 'Please enter a email or password.']);
    }

    // Exit script.
    exit;
?>
