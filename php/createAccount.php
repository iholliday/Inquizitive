<?php
    // If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
    require_once ("./php/blockDirectAccess.php");

    // Require the database connection file.
    require_once ("_connect.php");

    // Set response to JSON.
    header('Content-Type: application/json');

    // Check to see if session has started, if not, start one.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Ensure required fields are provided via POST.
    if (
        isset($_POST['txtEmail']) &&
        isset($_POST['txtPass']) &&
        isset($_POST['txtFirstName']) &&
        isset($_POST['txtLastName'])
    ) 
    {
        // Set variables.
        $email = strtolower(trim($_POST['txtEmail']));
        $password = $_POST['txtPass'];
        $firstName = trim($_POST['txtFirstName']);
        $lastName = trim($_POST['txtLastName']);
        $accessLevel = "USER";
        $isDisabled = 1;

        // Validate email format, send error if invalid.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            echo json_encode(['status' => 'error', 'message' => "Invalid email format."]);
            exit;
        }

        // Hash password.
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Create DB instance.
        $db = new inquizitiveDB();

        // Query prepares internally, binds the parameters, and executes them. 
        $stmt = $db->Query(
            "CALL CreateAccount(?, ?, ?, ?, ?, ?)",
            [$email, $firstName, $lastName, $passwordHash, $accessLevel, $isDisabled]
        );

        // Ensure query succeeded before using the result resource.
        if ($stmt === false) {
            error_log("Prepare failed: " . mysqli_error($db->connect));
        }
        
        // Success response.
        echo json_encode(['status' => 'success', 'message' => 'Account created successfully.']);

    } 
    else 
    {
        // Missing required fields.
        echo json_encode(['status' => 'error', 'message' => 'Please complete all required fields.']);
    }

    // Exit script.
    exit;
?>