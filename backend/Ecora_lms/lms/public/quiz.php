<?php
// public/quiz.php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_login();
require_role('student');

$uid = $_SESSION['user_id'];
$module_id = intval($_GET['module_id'] ?? 0);

// Fetch module info
$stmt = $mysqli->prepare("SELECT title FROM modules WHERE id = ?");
$stmt->bind_param('i', $module_id);
$stmt->execute();
$mod = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$mod) die("Module not found.");

// Fetch module quiz
$stmt = $mysqli->prepare("SELECT id FROM quizzes WHERE module_id = ?");
$stmt->bind_param('i', $module_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$quiz) die("Quiz not found.");

// Fetch questions
$stmt = $mysqli->prepare("SELECT id, question, option_a, option_b, option_c, option_d FROM quiz_questions WHERE quiz_id = ?");
$stmt->bind_param('i', $quiz['id']);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($mod['title']) ?> - Quiz</title>
<style>
body { font-family: Arial, sans-serif; margin:20px; background:#f9f9f9; }
h2 { margin-bottom: 10px; }
.question { margin-bottom:15px; padding:10px; background:#fff; border:1px solid #ccc; border-radius:5px; }
input[type=submit] { padding:6px 12px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer; }
input[type=submit]:hover { background:#0056b3; }
</style>
</head>
<body>
<h2><?= htmlspecialchars($mod['title']) ?> - Quiz</h2>
<form method="post" action="submit_quiz.php">
<input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
<input type="hidden" name="module_id" value="<?= $module_id ?>">

<?php foreach($questions as $i => $q): ?>
<div class="question">
<p><strong>Q<?= $i+1 ?>: <?= htmlspecialchars($q['question']) ?></strong></p>
<label><input type="radio" name="answer[<?= $q['id'] ?>]" value="a"> <?= htmlspecialchars($q['option_a']) ?></label><br>
<label><input type="radio" name="answer[<?= $q['id'] ?>]" value="b"> <?= htmlspecialchars($q['option_b']) ?></label><br>
<label><input type="radio" name="answer[<?= $q['id'] ?>]" value="c"> <?= htmlspecialchars($q['option_c']) ?></label><br>
<label><input type="radio" name="answer[<?= $q['id'] ?>]" value="d"> <?= htmlspecialchars($q['option_d']) ?></label>
</div>
<?php endforeach; ?>

<input type="submit" value="Submit Quiz">
</form>
<p><a href="module_view.php?id=<?= $module_id ?>">Back to Module</a></p>
</body>
</html>
