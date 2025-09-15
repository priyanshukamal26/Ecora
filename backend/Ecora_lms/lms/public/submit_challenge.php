<?php
// public/submit_challenge.php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_login();
require_role('student');

$uid = $_SESSION['user_id'];
$challenge_id = intval($_GET['challenge_id'] ?? 0);

// Fetch challenge
$stmt = $mysqli->prepare("SELECT id, title, description FROM challenges WHERE id=?");
$stmt->bind_param("i", $challenge_id);
$stmt->execute();
$challenge = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$challenge) {
    die("Challenge not found.");
}

$error = '';
$success = '';

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = trim($_POST['text_submission'] ?? '');
    $file_path = null;

    // Handle file upload
    if (isset($_FILES['file_submission']) && $_FILES['file_submission']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed_ext = ['pdf','doc','docx','png','jpg','jpeg','gif'];
        $file_name = $_FILES['file_submission']['name'];
        $file_tmp  = $_FILES['file_submission']['tmp_name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            $error = "Invalid file type. Allowed: PDF, DOC/DOCX, PNG, JPG, JPEG, GIF.";
        } else {
            $upload_dir = __DIR__ . '/../uploads/challenges/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $new_name = uniqid('challenge_') . '.' . $ext;
            $file_path = 'uploads/challenges/' . $new_name;
            if (!move_uploaded_file($file_tmp, __DIR__ . '/../' . $file_path)) {
                $error = "Failed to upload file.";
            }
        }
    }

    if (!$error && $text === '' && !$file_path) {
        $error = "Submission cannot be empty.";
    }

    if (!$error) {
        // Check if already submitted
        $check = $mysqli->prepare("SELECT id FROM challenge_submissions WHERE challenge_id=? AND student_id=?");
        $check->bind_param("ii", $challenge_id, $uid);
        $check->execute();
        $already = $check->get_result()->fetch_assoc();
        $check->close();

        if ($already) {
            $error = "You have already submitted this challenge.";
        } else {
            // Insert submission
            $status = 'completed';
            $ins = $mysqli->prepare("INSERT INTO challenge_submissions 
                (challenge_id, student_id, text_submission, file_submission, status, submitted_at) 
                VALUES (?, ?, ?, ?, ?, NOW())");
            $ins->bind_param("iisss", $challenge_id, $uid, $text, $file_path, $status);

            if ($ins->execute()) {
                $ins->close();
                header("Location: dashboard_student.php?submitted=1");
                exit;
            } else {
                $error = "Submission failed: " . $ins->error;
                $ins->close();
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Submit Challenge</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background:#f9f9f9; }
        textarea { width: 100%; max-width: 600px; }
        .btn { padding: 8px 16px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h2>Submit: <?= htmlspecialchars($challenge['title']) ?></h2>
    <p><?= nl2br(htmlspecialchars($challenge['description'])) ?></p>

    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Your Submission:</label><br>
        <textarea name="text_submission" rows="8" placeholder="Write your answer here..."></textarea><br><br>

        <label>Attach File (optional):</label><br>
        <input type="file" name="file_submission" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.gif"><br><br>

        <button type="submit" class="btn">Submit Challenge</button>
    </form>

    <p><a href="dashboard_student.php">⬅ Back to Dashboard</a></p>
</body>
</html>
