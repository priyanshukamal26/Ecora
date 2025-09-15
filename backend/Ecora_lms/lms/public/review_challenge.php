<?php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';

require_login();
require_role('teacher');

$submission_id = intval($_GET['submission_id'] ?? 0);
$challenge_id  = intval($_GET['challenge_id'] ?? 0);

if (!$submission_id) {
    echo "Submission not found!";
    exit;
}

// Fetch submission details including file_submission
$sql = "SELECT cs.id, cs.student_id, u.name AS student_name, c.title, cs.text_submission, cs.file_submission, cs.status, 
               cs.points_awarded, cs.teacher_remark 
        FROM challenge_submissions cs
        JOIN users u ON cs.student_id = u.id
        JOIN challenges c ON cs.challenge_id = c.id
        WHERE cs.id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $submission_id);
$stmt->execute();
$submission = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$submission) {
    echo "Submission not found!";
    exit;
}

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $status = $_POST['status'];
    $points = intval($_POST['points']);
    $remark = trim($_POST['remark']);

    $update = $mysqli->prepare("UPDATE challenge_submissions 
                                SET status = ?, points_awarded = ?, teacher_remark = ?, reviewed_at = NOW() 
                                WHERE id = ?");
    $update->bind_param("sisi", $status, $points, $remark, $submission_id);

    if ($update->execute()) {
        $update->close();

        // Award points if approved and not already approved
        if ($status === 'approved' && $points > 0 && $submission['status'] !== 'approved') {
            $stmt2 = $mysqli->prepare("UPDATE users SET points = points + ? WHERE id = ?");
            $stmt2->bind_param("ii", $points, $submission['student_id']);
            $stmt2->execute();
            $stmt2->close();
        }

        header("Location: view_submissions.php" . ($challenge_id ? "?challenge_id=$challenge_id" : ""));
        exit;
    } else {
        echo "Error updating review!";
    }
}

// Prepare correct file URL for browser
$filePath = !empty($submission['file_submission']) ? '/lms/' . ltrim($submission['file_submission'], '/') : '';
$ext = strtolower(pathinfo($submission['file_submission'] ?? '', PATHINFO_EXTENSION));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .file-preview { margin: 15px 0; padding: 10px; border: 1px solid #ddd; background:#f8f9fa; text-align:center; }
        .file-preview iframe, .file-preview img { max-width: 100%; max-height:500px; }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-3">📖 Review Submission</h2>

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            Challenge: <?= htmlspecialchars($submission['title']) ?>
        </div>
        <div class="card-body">
            <p><strong>Student:</strong> <?= htmlspecialchars($submission['student_name']) ?></p>

            <?php if (!empty($filePath) && file_exists(__DIR__ . '/../' . $submission['file_submission'])): ?>
                <div class="file-preview">
                    <strong>File Submission:</strong><br>
                    <?php if (in_array($ext, ['pdf'])): ?>
                        <iframe src="<?= $filePath ?>" width="100%" height="500px"></iframe>
                    <?php elseif (in_array($ext, ['png','jpg','jpeg','gif'])): ?>
                        <img src="<?= $filePath ?>" alt="Submitted File">
                    <?php else: ?>
                        <a href="<?= $filePath ?>" target="_blank">Download/View File</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <p><strong>Text Submission:</strong></p>
            <div class="p-3 bg-light border rounded">
                <?= nl2br(htmlspecialchars($submission['text_submission'])) ?>
            </div>
        </div>
    </div>

    <form method="POST" class="card p-3 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="approved" <?= $submission['status'] === 'approved' ? 'selected' : '' ?>>✅ Approve</option>
                <option value="rejected" <?= $submission['status'] === 'rejected' ? 'selected' : '' ?>>❌ Reject</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Points</label>
            <input type="number" name="points" class="form-control" 
                   value="<?= htmlspecialchars($submission['points_awarded'] ?? 0) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Teacher Remark</label>
            <textarea name="remark" class="form-control" rows="3"><?= htmlspecialchars($submission['teacher_remark'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">💾 Save Review</button>
        <a href="view_submissions.php<?= $challenge_id ? '?challenge_id='.$challenge_id : '' ?>" class="btn btn-secondary">⬅ Back</a>
    </form>
</div>
</body>
</html>


