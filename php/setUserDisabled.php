<?php

    require_once __DIR__ . "/_connect.php";
    header('Content-Type: application/json');

    // Start session if session is not already active
    if(session_status() === PHP_SESSION_NONE) session_start();

    // Checking to make sure that the user is admin, as only the admin can modify account status (At the moment)
    if (!isset($_SESSION["accessLevel"]) || $_SESSION["accessLevel"] !== "ADMIN") {
    echo json_encode(["ok" => false, "message" => "Unauthorized."]);
    exit;
    }
    
    // Retrieve post parameters safely
    $userUUID = trim($_POST["userUUID"] ?? "");
    $isDisabled = $_POST["isDisabled"] ?? null;

    // Validate required parameters
    if ($userUUID === "" || $isDisabled === null) {
    echo json_encode(["ok" => false, "message" => "Missing parameters."]);
    exit;
    }

    // Ensure disabled flag is strictly 0 or 1
    $isDisabled = (int)$isDisabled;
    if ($isDisabled !== 0 && $isDisabled !== 1) {
    echo json_encode(["ok" => false, "message" => "Invalid isDisabled value."]);
    exit;
    }

    // Establish database connection
    $db = new inquizitiveDB();
    $conn = $db->connect;

    // Stores current logged-in user UUID
    $currentUUID = $_SESSION["userUUID"] ?? "";

    // Parepare stored procedure call to update user status
    $stmt = $conn->prepare("CALL SetUserDisabled(?, ?)");
    $stmt->bind_param("si", $userUUID, $isDisabled);

    try{
        // Execute the stored procedure
        $stmt->execute();
    } catch (mysqli_sql_exception $ex) {
        echo json_encode(["ok" => false, "message" => $ex->getMessage()]);
        $stmt->close();
        exit;
    }

    $stmt->close();

    // Return success response
    echo json_encode(["ok" => true, "message" => $isDisabled ? "User disabled." : "User enabled."]);
    exit;
?>