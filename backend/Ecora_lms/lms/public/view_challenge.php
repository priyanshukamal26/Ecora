<?php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_login();
require_role('student');

$uid = $_SESSION['user_id'];
$cid = intval($_GET['id'] ?? 0);

// Fetch challenge
$stmt = $mysqli->prepare("SELECT id, title, description, created_at FROM challenges WHERE id=?");
$stmt->bind_param("i", $cid);
$stmt->execute();
$challenge = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$challenge) die("Challenge not found.");

// Fetch existing submission if any
$stmt = $mysqli->prepare("SELECT * FROM challenge_submissions WHERE challenge_id=? AND student_id=?");
$stmt->bind_param("ii", $cid, $uid);
$stmt->execute();
$submission = $stmt->get_result()->fetch_assoc();
$stmt->close();

$error = '';
$success = '';

// Handle submission (insert or update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = trim($_POST['text_submission'] ?? '');
    $file_path = $submission['file_submission'] ?? null;

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
        if ($submission) {
            // Update existing submission
            $upd = $mysqli->prepare("UPDATE challenge_submissions SET text_submission=?, file_submission=?, submitted_at=NOW(), status='completed' WHERE challenge_id=? AND student_id=?");
            $upd->bind_param("ssii", $text, $file_path, $cid, $uid);
            $upd->execute();
            $upd->close();
        } else {
            // Insert new submission
            $ins = $mysqli->prepare("INSERT INTO challenge_submissions (challenge_id, student_id, text_submission, file_submission, status, submitted_at) VALUES (?, ?, ?, ?, 'completed', NOW())");
            $ins->bind_param("iiss", $cid, $uid, $text, $file_path);
            $ins->execute();
            $ins->close();
        }
        $success = "✅ Your submission has been recorded.";
        $submission['text_submission'] = $text;
        $submission['file_submission'] = $file_path;
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>View Challenge</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background:#f9f9f9; }
        textarea { width: 100%; max-width: 600px; }
        .btn { padding: 8px 16px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .submission-box { padding: 15px; background: #fff; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 20px; }
        .file-preview { margin-top: 10px; }
        .file-preview iframe, .file-preview img { max-width: 100%; max-height: 400px; }
    </style>
</head>
<body>
    <h2><?= htmlspecialchars($challenge['title']) ?></h2>
    <p><em>Created at: <?= htmlspecialchars($challenge['created_at']) ?></em></p>
    <p><?= nl2br(htmlspecialchars($challenge['description'])) ?></p>

    <h3>Your Submission</h3>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Your Submission:</label><br>
        <textarea name="text_submission" rows="8" placeholder="Write your answer here..."><?= htmlspecialchars($submission['text_submission'] ?? '') ?></textarea><br><br>

        <label>Attach File (optional):</label><br>
        <input type="file" name="file_submission" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.gif"><br><br>

        <?php if (!empty($submission['file_submission'])): ?>
            <div class="file-preview">
                <strong>Previously Submitted File:</strong><br>
                <?php 
                $ext = strtolower(pathinfo($submission['file_submission'], PATHINFO_EXTENSION));
                if ($ext === 'pdf'): ?>
                    <iframe src="<?= htmlspecialchars($submission['file_submission']) ?>" width="100%" height="400px"></iframe>
                <?php elseif (in_array($ext, ['png','jpg','jpeg','gif'])): ?>
                    <img src="<?= htmlspecialchars($submission['file_submission']) ?>" alt="Submitted File">
                <?php else: ?>
                    <a href="<?= htmlspecialchars($submission['file_submission']) ?>" target="_blank">Download/View File</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn"><?= $submission ? 'Update Submission' : 'Submit Challenge' ?></button>
    </form>

    <p><a href="dashboard_student.php">⬅ Back to Dashboard</a></p>
</body>
</html>
