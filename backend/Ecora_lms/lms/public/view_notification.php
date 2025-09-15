<?php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_login();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $mysqli->prepare("SELECT * FROM notifications WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $n = $res->fetch_assoc();
    } else {
        die("Notification not found.");
    }


} else {
    die("Invalid request.");
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Notification Details</title>
</head>
<body>
  <h2><?= htmlspecialchars($n['title']) ?></h2>
  <p><strong>Date:</strong> <?= $n['created_at'] ?></p>
  <p><?= nl2br(htmlspecialchars($n['message'])) ?></p> <!-- full description here -->
  <p><a href="dashboard_student.php">Back to Dashboard</a></p>
</body>
</html>

