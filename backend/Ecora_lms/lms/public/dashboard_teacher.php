<?php
// public/dashboard_teacher.php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_login();
require_role('teacher');

$tid = $_SESSION['user_id'];

// quick counts
$challenges_count = $mysqli->query("SELECT COUNT(*) AS c FROM challenges WHERE teacher_id = $tid")->fetch_assoc()['c'];
$submissions_count = $mysqli->query("SELECT COUNT(*) AS c FROM challenge_submissions cs JOIN challenges c on cs.challenge_id = c.id WHERE c.teacher_id = $tid")->fetch_assoc()['c'];

?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Teacher Dashboard</title></head>
<body>
  <h2>Welcome, <?=htmlspecialchars($_SESSION['name'])?> (Teacher)</h2>
  <p><a href="logout.php">Logout</a></p>

  <p>Challenges you created: <?= $challenges_count ?></p>
  <p>Total submissions to review: <?= $submissions_count ?></p>

  <ul>
    <li><a href="teacher_add_challenge.php">Add Challenge</a></li>
    <li><a href="view_submissions.php">View & Review Submissions</a></li>
    <li><a href="teacher_view_progress.php">View Class Progress (TODO)</a></li>
  </ul>
</body>
</html>
