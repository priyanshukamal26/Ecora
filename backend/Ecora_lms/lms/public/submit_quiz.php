<?php
// public/submit_quiz.php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/helpers.php';
require_login();
require_role('student');

$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard_student.php');
    exit;
}

$quiz_id   = intval($_POST['quiz_id'] ?? 0);
$module_id = intval($_POST['module_id'] ?? 0);
$answers   = $_POST['answer'] ?? [];

if (!$quiz_id || !$module_id) die("Invalid submission.");

// --------------------------
// Fetch quiz questions
// --------------------------
$stmt = $mysqli->prepare("SELECT id, answer FROM quiz_questions WHERE quiz_id = ?");
$stmt->bind_param('i', $quiz_id);
$stmt->execute();
$res = $stmt->get_result();
$questions = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (!$questions) die("No quiz questions found.");

// --------------------------
// Calculate score
// --------------------------
$total = count($questions);
$correct = 0;
$wrong_answers = [];

foreach ($questions as $q) {
    $qid = $q['id'];
    $given = $answers[$qid] ?? '';
    if (strtolower(trim($given)) === strtolower(trim($q['answer']))) {
        $correct++;
    } else {
        $wrong_answers[] = [
            'question' => $qid,
            'your_answer' => $given,
            'correct_answer' => $q['answer']
        ];
    }
}

$score_percent = intval(($correct / $total) * 100);
$passed = $score_percent >= 50;
$points_earned = $correct * 2;

// --------------------------
// Fetch previous best_score & points
// --------------------------
$stmt = $mysqli->prepare("SELECT best_score, points_awarded FROM module_progress WHERE student_id=? AND module_id=?");
$stmt->bind_param('ii', $uid, $module_id);
$stmt->execute();
$res = $stmt->get_result();
$prev = $res->fetch_assoc();
$stmt->close();

$prev_best   = $prev ? intval($prev['best_score']) : 0;
$prev_points = $prev ? intval($prev['points_awarded']) : 0;

// --------------------------
// Update module_progress
// --------------------------
$new_best_score = max($score_percent, $prev_best);
$new_points_awarded = max($points_earned, $prev_points);

// Insert module_progress if not exists
$stmt = $mysqli->prepare("SELECT * FROM module_progress WHERE student_id=? AND module_id=?");
$stmt->bind_param('ii', $uid, $module_id);
$stmt->execute();
$exists = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($exists) {
    $stmt = $mysqli->prepare("UPDATE module_progress SET quiz_passed=?, status='completed', score=?, best_score=?, points_awarded=?, completed_at=NOW() WHERE student_id=? AND module_id=?");
    $stmt->bind_param('iiiiii', $passed, $score_percent, $new_best_score, $new_points_awarded, $uid, $module_id);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $mysqli->prepare("INSERT INTO module_progress (student_id, module_id, status, quiz_passed, score, best_score, points_awarded, completed_at) VALUES (?, ?, 'completed', ?, ?, ?, ?, NOW())");
    $stmt->bind_param('iiiiii', $uid, $module_id, $passed, $score_percent, $new_best_score, $new_points_awarded);
    $stmt->execute();
    $stmt->close();
}

// --------------------------
// Update user total points dynamically
// --------------------------
// Sum all points from module_progress
$stmt = $mysqli->prepare("SELECT COALESCE(SUM(points_awarded),0) AS quiz_points FROM module_progress WHERE student_id=?");
$stmt->bind_param('i', $uid);
$stmt->execute();
$quiz_points = $stmt->get_result()->fetch_assoc()['quiz_points'];
$stmt->close();

// Sum all points from approved challenge submissions
$stmt = $mysqli->prepare("SELECT COALESCE(SUM(points_awarded),0) AS challenge_points FROM challenge_submissions WHERE student_id=? AND status='approved'");
$stmt->bind_param('i', $uid);
$stmt->execute();
$challenge_points = $stmt->get_result()->fetch_assoc()['challenge_points'];
$stmt->close();

$total_points = intval($quiz_points + $challenge_points);

// Update users table
$stmt = $mysqli->prepare("UPDATE users SET points=? WHERE id=?");
$stmt->bind_param('ii', $total_points, $uid);
$stmt->execute();
$stmt->close();

// Update session points immediately
$_SESSION['points'] = $total_points;

// --------------------------
// Store quiz result in session
// --------------------------
$_SESSION['quiz_result'] = [
    'module_id' => $module_id,
    'score' => $score_percent,
    'total' => $total,
    'correct' => $correct,
    'wrong_answers' => $wrong_answers,
    'prev_best' => $prev_best
];

// Redirect to result page
header("Location: quiz_result.php");
exit;
