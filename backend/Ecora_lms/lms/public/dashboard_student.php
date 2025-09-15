<?php
// public/dashboard_student.php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/helpers.php';
require_login();
require_role('student');

$uid = $_SESSION['user_id'];

// --------------------------
// Fetch modules + progress
// --------------------------
$sql = "SELECT m.id, m.title, m.sequence, 
               COALESCE(mp.status, 'locked') AS status, 
               COALESCE(mp.quiz_passed, 0) AS quiz_passed,
               COALESCE(mp.points_awarded, 0) AS module_points
        FROM modules m
        LEFT JOIN module_progress mp 
          ON mp.module_id = m.id AND mp.student_id = ?
        ORDER BY m.sequence ASC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $uid);
$stmt->execute();
$modules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Sequential unlock logic
$previous_completed = true;
foreach ($modules as &$m) {
    $is_completed = $m['quiz_passed'] == 1;

    if ($m['sequence'] == 1) {
        $m['status'] = $is_completed ? 'completed' : 'unlocked';
    } else {
        $m['status'] = $previous_completed ? ($is_completed ? 'completed' : 'unlocked') : 'locked';
    }

    $previous_completed = $is_completed;
}
unset($m);

// --------------------------
// Fetch notifications
// --------------------------
$nt = $mysqli->query("
    SELECT id, title, message, created_at
    FROM notifications
    WHERE user_id IS NULL OR user_id = $uid
    ORDER BY created_at DESC
    LIMIT 10
");
$notes = $nt->fetch_all(MYSQLI_ASSOC);

// --------------------------
// Fetch challenge submissions
// --------------------------
$sub = $mysqli->prepare("
    SELECT cs.id, c.title, cs.text_submission, 
           COALESCE(cs.status, 'pending') AS status,
           cs.teacher_remark, cs.points_awarded, cs.submitted_at
    FROM challenge_submissions cs
    JOIN challenges c ON c.id = cs.challenge_id
    WHERE cs.student_id = ?
    ORDER BY cs.submitted_at DESC
");
$sub->bind_param('i', $uid);
$sub->execute();
$subs = $sub->get_result()->fetch_all(MYSQLI_ASSOC);
$sub->close();

// --------------------------
// Fetch challenges
// --------------------------
$ch = $mysqli->query("SELECT id, title, description, created_at FROM challenges ORDER BY created_at DESC");
$challenges = $ch->fetch_all(MYSQLI_ASSOC);

// --------------------------
// Calculate total points
// --------------------------
$total_quiz_points = array_sum(array_column($modules, 'module_points'));
$total_challenge_points = array_sum(array_column($subs, 'points_awarded'));
$total_points = $total_quiz_points + $total_challenge_points;

// Update session points so header always shows correct value
$_SESSION['points'] = $total_points;

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background:#f9f9f9; }
        ul { list-style-type: none; padding-left: 0; }
        li { margin-bottom: 15px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background:#fff; }
        li a { text-decoration: none; color: inherit; display: block; }
        li a:hover { background-color: #f0f0f0; }
        small { color: #555; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; background:#fff; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        .btn { display: inline-block; padding: 6px 12px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; }
        .btn:hover { background: #0056b3; }
        .badge { padding:4px 8px; border-radius:4px; font-weight:bold; }
        .badge.pending { background:#ffc107; color:#000; }
        .badge.submitted { background:#17a2b8; color:#fff; }
        .badge.approved { background:#28a745; color:#fff; }
        .badge.rejected { background:#dc3545; color:#fff; }
    </style>
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?> (Student)</h2>
    <p>Points: <?= htmlspecialchars($total_points) ?> | <a href="logout.php">Logout</a></p>

    <!-- Notifications -->
    <h3>Notifications</h3>
    <?php if (count($notes) === 0): ?>
        <p>No notifications</p>
    <?php else: ?>
        <ul>
            <?php foreach ($notes as $n): ?>
                <li>
                    <a href="view_notification.php?id=<?= $n['id'] ?>">
                        <strong><?= htmlspecialchars($n['title']) ?></strong>
                        <small>(<?= $n['created_at'] ?>)</small>
                        <p><?= htmlspecialchars(strlen($n['message']) > 70 ? substr($n['message'], 0, 70) . '...' : $n['message']) ?></p>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Challenges -->
    <h3>Challenges</h3>
    <?php if (count($challenges) === 0): ?>
        <p>No challenges available yet.</p>
    <?php else: ?>
        <?php foreach ($challenges as $c): ?>
            <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px; background:#fff;">
                <h4><?= htmlspecialchars($c['title']) ?></h4>
                <p><?= nl2br(htmlspecialchars($c['description'])) ?></p>
                <small>Posted: <?= htmlspecialchars($c['created_at']) ?></small><br><br>
                <a href="view_challenge.php?id=<?= $c['id'] ?>" class="btn">Submit</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Modules -->
    <h3>Modules</h3>
    <table>
        <tr>
            <th>Seq</th>
            <th>Title</th>
            <th>Status</th>
            <th>Points</th>
            <th>Action</th>
        </tr>
        <?php foreach ($modules as $m): ?>
        <tr>
            <td><?= htmlspecialchars($m['sequence']) ?></td>
            <td><?= htmlspecialchars($m['title']) ?></td>
            <td><?= ucfirst($m['status']) ?></td>
            <td><?= htmlspecialchars($m['module_points']) ?></td>
            <td>
                <?php if ($m['status'] === 'unlocked' || $m['status'] === 'completed'): ?>
                    <a href="module_view.php?id=<?= $m['id'] ?>" class="btn"><?= $m['status'] === 'completed' ? 'Revisit' : 'Open' ?></a>
                <?php else: ?>
                    Locked
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

<!-- Submissions -->
<h3>My Submissions</h3>
<?php if (count($subs) === 0): ?>
    <p>No submissions yet.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Challenge</th>
            <th>Submission</th>
            <th>File</th> <!-- NEW COLUMN -->
            <th>Status</th>
            <th>Remark</th>
            <th>Points</th>
            <th>Submitted At</th>
        </tr>
        <?php foreach ($subs as $s): 
            $status = strtolower($s['status']);
            $statusClass = match($status) {
                'pending' => 'pending',
                'completed' => 'submitted',
                'approved' => 'approved',
                'rejected' => 'rejected',
                default => 'pending'
            };
            $displayStatus = match($status) {
                'pending' => 'Pending Review',
                'completed' => 'Submitted',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                default => ucfirst($status)
            };
        ?>
            <tr>
                <td><?= htmlspecialchars($s['title']) ?></td>
                <td><?= htmlspecialchars(strlen($s['text_submission']) > 50 ? substr($s['text_submission'], 0, 50) . '...' : $s['text_submission']) ?></td>
                
                <!-- FILE COLUMN -->
                <td>
                    <?php if (!empty($s['file_submission'])): 
                        $ext = strtolower(pathinfo($s['file_submission'], PATHINFO_EXTENSION));
                        if ($ext === 'pdf'): ?>
                            <a href="<?= htmlspecialchars($s['file_submission']) ?>" target="_blank">📄 View PDF</a>
                        <?php elseif (in_array($ext, ['png','jpg','jpeg','gif'])): ?>
                            <a href="<?= htmlspecialchars($s['file_submission']) ?>" target="_blank">
                                <img src="<?= htmlspecialchars($s['file_submission']) ?>" style="max-width:100px; max-height:80px;">
                            </a>
                        <?php else: ?>
                            <a href="<?= htmlspecialchars($s['file_submission']) ?>" target="_blank">Download File</a>
                        <?php endif; ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>

                <td><span class="badge <?= $statusClass ?>"><?= $displayStatus ?></span></td>
                <td><?= htmlspecialchars($s['teacher_remark'] ?? '-') ?></td>
                <td><?= htmlspecialchars($s['points_awarded'] ?? 0) ?></td>
                <td><?= htmlspecialchars($s['submitted_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>



    <p><a href="leaderboard.php">View Leaderboard</a></p>
</body>
</html>
