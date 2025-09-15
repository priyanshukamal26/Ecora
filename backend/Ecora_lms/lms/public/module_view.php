<?php
// public/module_view.php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/helpers.php';
require_login();
require_role('student');

$uid = $_SESSION['user_id'];
$module_id = intval($_GET['id'] ?? 0);
if (!$module_id) die("Invalid module.");

// --------------------------
// Fetch module contents
// --------------------------
$sql = "SELECT * FROM module_contents WHERE module_id = ? ORDER BY sequence ASC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $module_id);
$stmt->execute();
$contents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
if (count($contents) === 0) die("No content available for this module.");

// --------------------------
// Track current content
// --------------------------
$current_index = intval($_GET['content'] ?? 0);
if ($current_index < 0 || $current_index >= count($contents)) $current_index = 0;
$current_content = $contents[$current_index];

// --------------------------
// Fetch or create module progress
// --------------------------
$mp_sql = "SELECT * FROM module_progress WHERE student_id = ? AND module_id = ?";
$stmt = $mysqli->prepare($mp_sql);
$stmt->bind_param('ii', $uid, $module_id);
$stmt->execute();
$progress = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$progress) {
    $stmt = $mysqli->prepare("
        INSERT INTO module_progress (student_id, module_id, status, quiz_passed, current_step)
        VALUES (?, ?, 'in_progress', 0, 1)
    ");
    $stmt->bind_param('ii', $uid, $module_id);
    $stmt->execute();
    $stmt->close();
    $progress = ['status' => 'in_progress', 'quiz_passed' => 0, 'current_step' => 1];
}

// Update current step if student progressed further
if ($current_index + 1 > $progress['current_step']) {
    $stmt = $mysqli->prepare("UPDATE module_progress SET current_step = ? WHERE student_id=? AND module_id=?");
    $step = $current_index + 1;
    $stmt->bind_param('iii', $step, $uid, $module_id);
    $stmt->execute();
    $stmt->close();
}

// --------------------------
// Quiz handling
// --------------------------
$quiz_questions = [];
$quiz_id = 0;
if ($current_content['type'] === 'quiz') {
    $quiz_stmt = $mysqli->prepare("SELECT id, passing_score FROM quizzes WHERE module_id = ?");
    $quiz_stmt->bind_param('i', $module_id);
    $quiz_stmt->execute();
    $quiz = $quiz_stmt->get_result()->fetch_assoc();
    $quiz_stmt->close();

    if ($quiz) {
        $quiz_id = $quiz['id'];
        $qq_stmt = $mysqli->prepare("SELECT * FROM quiz_questions WHERE quiz_id=? ORDER BY id ASC");
        $qq_stmt->bind_param('i', $quiz_id);
        $qq_stmt->execute();
        $quiz_questions = $qq_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $qq_stmt->close();
    }
}

// --------------------------
// Navigation URLs
// --------------------------
$prev_url = $current_index > 0 ? "?id=$module_id&content=" . ($current_index - 1) : null;
$next_url = $current_index < count($contents) - 1 ? "?id=$module_id&content=" . ($current_index + 1) : null;

// --------------------------
// Score/result message
// --------------------------
$score_percent = $progress['quiz_passed'] ? 100 : 0;
$quiz_result_msg = '';
if (isset($_GET['quiz_result'])) {
    if ($_GET['quiz_result'] === 'passed') {
        $quiz_result_msg = '<p style="color:green; font-weight:bold;">You passed the quiz! Next module unlocked.</p>';
    } elseif ($_GET['quiz_result'] === 'failed') {
        $quiz_result_msg = '<p style="color:red; font-weight:bold;">You did not pass. Please retry the quiz.</p>';
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($current_content['title']) ?></title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; background:#f9f9f9; }
.btn { display:inline-block; padding:6px 12px; background:#007bff; color:#fff; text-decoration:none; border-radius:4px; margin:5px 2px; }
.btn:hover { background:#0056b3; }
video { max-width:100%; margin-bottom:15px; }
textarea { width:100%; height:100px; }
</style>
</head>
<body>
<h2><?= htmlspecialchars($current_content['title']) ?></h2>
<?= $quiz_result_msg ?>

<?php if ($current_content['type'] === 'video'): ?>
    <video controls>
        <source src="<?= htmlspecialchars($current_content['content']) ?>" type="video/mp4">
        Your browser does not support the video tag.
    </video>

<?php elseif ($current_content['type'] === 'text'): ?>
    <p><?= nl2br(htmlspecialchars($current_content['content'])) ?></p>

<?php elseif ($current_content['type'] === 'quiz'): ?>
    <?php if ($quiz_id === 0 || count($quiz_questions) === 0): ?>
        <p>No quiz questions available.</p>
    <?php else: ?>
        <form method="post" action="submit_quiz.php">
            <?php foreach ($quiz_questions as $q): ?>
                <p><strong><?= htmlspecialchars($q['question']) ?></strong></p>
                <input type="radio" name="answer[<?= $q['id'] ?>]" value="a"> <?= htmlspecialchars($q['option_a']) ?><br>
                <input type="radio" name="answer[<?= $q['id'] ?>]" value="b"> <?= htmlspecialchars($q['option_b']) ?><br>
                <input type="radio" name="answer[<?= $q['id'] ?>]" value="c"> <?= htmlspecialchars($q['option_c']) ?><br>
                <input type="radio" name="answer[<?= $q['id'] ?>]" value="d"> <?= htmlspecialchars($q['option_d']) ?><br>
            <?php endforeach; ?>
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
            <input type="hidden" name="module_id" value="<?= $module_id ?>">
            <input type="hidden" name="current_index" value="<?= $current_index ?>">
            <button type="submit" class="btn"><?= $progress['quiz_passed'] ? 'Retake Quiz' : 'Submit Quiz' ?></button>
        </form>
    <?php endif; ?>

<?php elseif ($current_content['type'] === 'result'): ?>
    <p><strong>Module Completed!</strong> Your score: <?= $score_percent ?>%</p>
    <a href="dashboard_student.php" class="btn">Back to Dashboard</a>
<?php endif; ?>

<div>
    <?php if ($prev_url): ?>
        <a href="<?= $prev_url ?>" class="btn">Previous</a>
    <?php endif; ?>
    <?php if ($next_url): ?>
        <a href="<?= $next_url ?>" class="btn">Next</a>
    <?php endif; ?>
    <a href="dashboard_student.php" class="btn">Back to Dashboard</a>
</div>
</body>
</html>
