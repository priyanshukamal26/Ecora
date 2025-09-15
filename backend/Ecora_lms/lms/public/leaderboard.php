<?php
// public/leaderboard.php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_login();

$res = $mysqli->query("SELECT id,name,points FROM users WHERE role='student' ORDER BY points DESC LIMIT 20");
$rows = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Leaderboard</title></head>
<body>
  <h2>Leaderboard</h2>
  <ol>
  <?php foreach ($rows as $r): ?>
    <li><?=htmlspecialchars($r['name'])?> — <?=htmlspecialchars($r['points'])?> pts</li>
  <?php endforeach; ?>
  </ol>
  <p><a href="<?=(($_SESSION['role']??'')==='teacher') ? 'dashboard_teacher.php' : 'dashboard_student.php'?>">Back</a></p>
</body>
</html>
