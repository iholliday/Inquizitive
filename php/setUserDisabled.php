<?php

    require_once __DIR__ . "/_connect.php";
    header('Content-Type: application/json');

    if(session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION["accessLevel"]) || $_SESSION["accessLevel"] !== "ADMIN") {
    echo json_encode(["ok" => false, "message" => "Unauthorized."]);
    exit;

    }
    
    $userUUID = trim($_POST["userUUID"] ?? "");
    $isDisabled = $_POST["isDisabled"] ?? null;

    if ($userUUID === "" || $isDisabled === null) {
    echo json_encode(["ok" => false, "message" => "Missing parameters."]);
    exit;
    }

    $isDisabled = (int)$isDisabled;
    if ($isDisabled !== 0 && $isDisabled !== 1) {
    echo json_encode(["ok" => false, "message" => "Invalid isDisabled value."]);
    exit;
    }

    $db = new inquizitiveDB();
    $conn = $db->connect;

    $currentUUID = $_SESSION["userUUID"] ?? "";

    $stmt = $conn->prepare("CALL SetUserDisabled(?, ?)");
    $stmt->bind_param("si", $userUUID, $isDisabled);

    try{
        $stmt->execute();
    } catch (mysqli_sql_exception $ex) {
        echo json_encode(["ok" => false, "message" => $ex->getMessage()]);
        $stmt->close();
        exit;
    }

    $stmt->close();

    echo json_encode(["ok" => true, "message" => $isDisabled ? "User disabled." : "User enabled."]);
    exit;
?>