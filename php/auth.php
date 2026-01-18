<?php
    // If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
    require_once ("./php/blockDirectAccess.php");

    // Require the database connection file.
    require ("_connect.php");

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
        $email = $_POST['txtEmail'];
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
        $stmt = $db->Query("CALL GetUserByEmail()?", [$email]);

        // If prepared statement fails, provide error message.
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'message' => 'Database query failed.']);
            mysqli_stmt_close($stmt);
            mysqli_next_result($connect);
            exit;
        }


        // Gets result from statement.
        $result = mysqli_stmt_get_result($stmt);

        /*
        // Prepare the CALL statement.
        $stmt = mysqli_prepare($connect, "CALL GetUserByEmail(?)");

        // If prepared statement fails, provide error message.
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'message' => 'Database query failed.']);
            exit;
        }

        // Bind parameters, execute query and gather results.
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        */

        // Check if exactly one user is found.
        if (mysqli_num_rows($result) == 1) 
        {
            // Fetch user data with associated account.
            $user = mysqli_fetch_assoc($result);

            // Verify password.
            if (password_verify($password, $user['password'])) 
            {
                // Set session variables.
                
                /*
                TBC
                
                $_SESSION['userUUID'] = $user['userUUID'];
                $_SESSION['firstName'] = $user['firstName'];
                $_SESSION['lastName'] = $user['lastName'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['accessLevel'] = $user['accessLevel'];*/

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

    // Close statement.
    mysqli_stmt_close($stmt);
    mysqli_next_result($connect);

    // Exit script.
    exit;
?>
