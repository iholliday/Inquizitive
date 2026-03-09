<?php
require_once ("./php/blockDirectAccess.php");
require_once ("./php/_connect.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new InquizitiveDB();

// UUID for currency
$currencyUUID = 'ec0ad14f-12c5-11f1-98eb-bc2411ac3867';

// Get top 10 users by quantity
$stmt = $db->Query("CALL GetTopUsersByCurrency(?)", [$currencyUUID]);

$topIQ = [];
while ($row = $stmt->fetch_assoc()) 
{
    $topIQ[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Top 10 Scores</h2>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($topIQ) === 0): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted">No users found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($topIQ as $index => $user): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($user['firstName']) ?></td>
                        <td><?= htmlspecialchars($user['lastName']) ?></td>
                        <td><?= htmlspecialchars($user['score']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>