<?php
// require_once __DIR__ . "/blockDirectAccess.php";
require_once __DIR__ . "/_connect.php";

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION["accessLevel"]) || $_SESSION["accessLevel"] !== "ADMIN") {
    echo json_encode(["ok" => false, "message" => "Unauthorized."]);
    exit;
}

if (empty($_POST["userUUID"])) {
    echo json_encode(["ok" => false, "message" => "Missing user UUID."]);
    exit;
}

$userUUID = trim($_POST["userUUID"]);

// Prevent deleting yourself
if ($_SESSION["userUUID"] === $userUUID) {
    echo json_encode(["ok" => false, "message" => "You cannot delete your own account."]);
    exit;
}

$db = new inquizitiveDB();
$conn = $db->connect;

// Optional: double-check admin protection
$check = $conn->prepare("SELECT accessLevel FROM `user` WHERE userUUID = ?");
$check->bind_param("s", $userUUID);
$check->execute();
$result = $check->get_result();
$user = $result->fetch_assoc();
$check->close();

if (!$user) {
    echo json_encode(["ok" => false, "message" => "User not found."]);
    exit;
}

if ($user["accessLevel"] === "ADMIN") {
    echo json_encode(["ok" => false, "message" => "Admin accounts cannot be deleted."]);
    exit;
}

$currentUUID = $_SESSION["userUUID"];

$stmt = $conn->prepare("CALL DeleteUser(?, ?)");
$stmt->bind_param("ss", $userUUID, $currentUUID);


if (!$stmt->execute()) {
    echo json_encode(["ok" => false, "message" => "Database error."]);
    $stmt->close();
    exit;
}

$stmt->close();

echo json_encode(["ok" => true, "message" => "User deleted successfully."]);
exit;

?>