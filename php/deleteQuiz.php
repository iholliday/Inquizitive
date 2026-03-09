<?php
require_once __DIR__ . "/_connect.php";

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_POST["quizUUID"])) {
    echo json_encode(["ok" => false, "message" => "Missing quiz UUID."]);
    exit;
}

$quizUUID = trim($_POST["quizUUID"]);

$db = new inquizitiveDB();
$conn = $db->connect;

try {

    $stmt = $conn->prepare("CALL DeleteQuiz(?)");
    $stmt->bind_param("s", $quizUUID);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $stmt->close();

    echo json_encode([
        "ok" => true,
        "message" => "Quiz deleted successfully."
    ]);

} catch (Throwable $e) {

    echo json_encode([
        "ok" => false,
        "message" => $e->getMessage()
    ]);
}

exit;
?>