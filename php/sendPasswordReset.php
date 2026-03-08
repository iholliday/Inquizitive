<?php
    // REQUIRES EMAIL SERVER TO WORK!!!

    // If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
    require_once ("./php/blockDirectAccess.php");

    // Require the connection file.
    require_once ("_connect.php");

    // Required for sending email.
    require_once ("confirmationEmail.php");
    require_once ("checkLocalHost.php");

    header('Content-Type: application/json');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Get email from POST and check it's valid.
    $email = strtolower(trim($_POST['email'] ?? ''));
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) 
    {
        echo json_encode(['status' => 'error', 'message' => "Invalid email address."]);
        exit;
    }

    $db = new inquizitiveDB();

    // Get user data.
    $stmt = $db->Query("CALL GetUserByEmail(?)", [$email]);
    $user = mysqli_fetch_assoc($stmt);

    // Always return success message to prevent email sniffing.
    if (!$user) 
    {
        echo json_encode(['status' => 'success','message' => 'A reset link has been sent if account exists.']);
        exit;
    }

    // Define user UUID.
    $userUUID = $user['userUUID'];

    // Generate reset token.
    $resetToken = bin2hex(random_bytes(32));
    $hashedToken = password_hash($resetToken, PASSWORD_BCRYPT);

    // Insert into passwordReset table
    $db->Query("CALL CreatePasswordReset(?, ?)", [$userUUID, $hashedToken]);

    // Direct link to file to avoid jRoute interferance.
    $resetLink = "https://inquisitive.remote.ac/resetPassword.php?token=$resetToken";

    // Send email unless localhost.
    if (!isLocalHost()) {
        $firstName = htmlspecialchars($user['firstName'], ENT_QUOTES, 'UTF-8');
        $lastName  = htmlspecialchars($user['lastName'], ENT_QUOTES, 'UTF-8');

        $subject = "Reset your password";
        $body = "Hello $firstName $lastName,\n\n";
        $body .= "Click this link to reset your password:\n$resetLink\n\n";
        $body .= "This link will expire in 1 hour.\nIf you didn't request this, please ignore this email.";

        sendEmail($email, $firstName, $lastName, $subject, $body);
    }

    echo json_encode(['status' => 'success', 'message' => 'A reset link has been sent if account exists.']);
    exit;
?>
