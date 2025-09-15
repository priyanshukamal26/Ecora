<?php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_login();
require_role('student');

$data = $_SESSION['quiz_result'] ?? null;
if (!$data) {
    header('Location: dashboard_student.php');
    exit;
}
unset($_SESSION['quiz_result']);
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Quiz Result</title>
<style>
body { font-family: Arial, sans-serif; margin:20px; background:#f9f9f9; }
h2 { margin-bottom: 10px; }
table { border-collapse: collapse; width: 100%; margin-top: 10px; background:#fff; }
th, td { border:1px solid #ccc; padding: 8px; }
th { background: #eee; }
.correct { color:green; font-weight:bold; }
.wrong { color:red; font-weight:bold; }
.btn { display:inline-block; padding:6px 12px; background:#007bff; color:#fff; text-decoration:none; border-radius:4px; margin-top:10px; }
.btn:hover { background:#0056b3; }
</style>
</head>
<body>
<h2>Quiz Result</h2>
<p>Your Score: <strong><?= $data['score'] ?>%</strong> (<?= $data['correct'] ?>/<?= $data['total'] ?> correct)</p>
<?php if($data['prev_best'] > 0): ?>
<p>Previous Best Score: <strong><?= $data['prev_best'] ?>%</strong></p>
<?php endif; ?>

<?php if(count($data['wrong_answers']) > 0): ?>
<h3>Review Wrong Answers</h3>
<table>
<tr><th>Question</th><th>Your Answer</th><th>Correct Answer</th></tr>
<?php foreach($data['wrong_answers'] as $wa): ?>
<tr>
<td><?= htmlspecialchars($wa['question']) ?></td>
<td class="wrong"><?= htmlspecialchars($wa['your_answer'] ?: 'Not Answered') ?></td>
<td class="correct"><?= htmlspecialchars($wa['correct_answer']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p class="correct">🎉 All answers correct! Great job!</p>
<?php endif; ?>

<a href="dashboard_student.php" class="btn">Back to Dashboard</a>
</body>
</html>
