<?php
    // If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
    require_once ("./php/blockDirectAccess.php");

    // Require the connection files.
    require_once ("_connect.php");
    require __DIR__ . "/../vendor/autoload.php";

    // Required for sending confirmation email.
    require_once ("confirmationEmail.php");
    require_once ("checkLocalHost.php");
    
    // Set response to JSON.
    header('Content-Type: application/json');

    // Check to see if session has started, if not, start one.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Initialize signupSpamChecker if not set.
    if (!isset($_SESSION['signupSpamChecker'])){ $_SESSION['signupSpamChecker'] = 0;}
    if ($_SESSION['signupSpamChecker'] >= 4){$_SESSION['signupSpamChecker'] = 0;}

    // Perform server-side reCAPTCHA verification only after 3 failed login attempts to minimise unneeded prompts for legitimate users.
    if ($_SESSION["signupSpamChecker"] >= 3) 
    {
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
                echo json_encode(['status' => 'error', 'message' => 'reCAPTCHA verification failed, please refresh and try again.']);
                exit;
            }
        }
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
        $passwordConfirm = $_POST['txtPassConfirm']; 
        $firstName = trim($_POST['txtFirstName']);
        $lastName = trim($_POST['txtLastName']);
        $accessLevel = "USER";
        $isDisabled = 1;

        // Validate email format, send error if invalid.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            $_SESSION['signupSpamChecker']++;
            echo json_encode(['status' => 'error', 'message' => "Invalid email format."]);
            exit;
        }

        // Server-side check if passwords match.
        if ($password !== $passwordConfirm) 
        {
            $_SESSION['signupSpamChecker']++;
            echo json_encode(['status' => 'error','message' => 'Passwords do not match.']);
            exit;
        }

        // Defining password rules.
        $minLength   = 8;
        $uppercase   = preg_match('@[A-Z]@', $password);
        $lowercase   = preg_match('@[a-z]@', $password);
        $number      = preg_match('@[0-9]@', $password);
        $specialChar = preg_match('@[^\w]@', $password);

        // If password is illegal, send error.
        if(strlen($password) < $minLength || !$uppercase || !$lowercase || !$number || !$specialChar) 
        {
            $_SESSION['signupSpamChecker']++;
            echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character.']);
            exit;
        }

        // Hash password.
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Create DB instance.
        $db = new inquizitiveDB();

        // Checks if user's email is already existing using preexisting database routine.
        $stmt = $db->Query("CALL GetUserByEmail(?)", [$email]);
        $user = $stmt->fetch_assoc(); 
        
        // Ensure query succeeded before using the result resource.
        if ($stmt === false) 
        {
            error_log("Prepare failed: " . mysqli_error($db->connect));
        }

        // If email is already in use.
        if ($user)
        {
                $_SESSION['signupSpamChecker']++;
                echo json_encode(['status' => 'error', 'message' => 'Email already in use.']);
                exit;
        }

        // Query prepares internally, binds the parameters, and executes them. 
        $stmt = $db->Query("CALL CreateAccount(?, ?, ?, ?, ?, ?)", [$email, $firstName, $lastName, $passwordHash, $accessLevel, $isDisabled]);
        
        // Ensure query succeeded before using the result resource.
        if ($stmt === false) 
        {
            error_log("Prepare failed: " . mysqli_error($db->connect));
        }

        // Send signup email, disabled for localhost development.
        if (!isLocalHost()) 
        {
            // Sanitizing data to prevent XSS.
            $sanitisedFirstName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
            $sanitisedLastName  = htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8');
                
            // Create the email subject and body for the confirmation message.
            $emailSubject = "Welcome to Inquizitive!";
            $emailBody = "Hello $sanitisedFirstName $sanitisedLastName,\n\n"
                       . "Your account has been successfully created!\n"
                       . "Student created accounts require lecturer approval prior to accessing the platform.\n\n"
                       . "This is an automated message — please do not reply.";
            sendEmail($email, $sanitisedFirstName, $sanitisedLastName, $emailSubject, $emailBody);
        }

        // Reset spam check if successful.
        if (isset($_SESSION['signupSpamChecker'])) 
        {
            unset($_SESSION['signupSpamChecker']);
        }

        // Success response.
        echo json_encode(['status' => 'success', 'message' => 'Account created successfully.']);
    } 
    else 
    {
        // Missing required fields.
        $_SESSION['signupSpamChecker']++;
        echo json_encode(['status' => 'error', 'message' => 'Please complete all required fields.']);
    }

    // Exit script.
    exit;
?>