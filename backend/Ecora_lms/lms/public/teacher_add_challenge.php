<?php
// public/teacher_add_challenge.php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_login();
require_role('teacher');

$tid = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');

    if (!$title) {
        $error = "Title required.";
    } else {
        // Insert challenge
        $stmt_challenge = $mysqli->prepare(
            "INSERT INTO challenges (teacher_id, title, description) VALUES (?, ?, ?)"
        );
        $stmt_challenge->bind_param('iss', $tid, $title, $desc);

        if ($stmt_challenge->execute()) {
            $success = "Challenge created successfully.";

            // Insert ONE global notification (visible to all students)
            $stmt_notif = $mysqli->prepare(
                "INSERT INTO notifications (user_id, title, message) VALUES (NULL, ?, ?)"
            );
            $stmt_notif->bind_param("ss", $title, $desc);
            $stmt_notif->execute();
            $stmt_notif->close();

        } else {
            $error = "Failed to create challenge: " . $mysqli->error;
        }

        $stmt_challenge->close();
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add Challenge</title>
</head>
<body>
    <h2>Add Challenge (Teacher)</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Title</label><br>
        <input name="title" type="text" required><br><br>

        <label>Description</label><br>
        <textarea name="description" rows="6" placeholder="Enter full challenge details"></textarea><br><br>

        <button type="submit">Create Challenge</button>
    </form>

    <p><a href="dashboard_teacher.php">Back to Dashboard</a></p>
</body>
</html>




