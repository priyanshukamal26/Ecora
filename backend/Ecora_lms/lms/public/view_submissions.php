<?php
// public/view_submissions.php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_login();
require_role('teacher');

$teacher_id = $_SESSION['user_id'];

// Fetch challenges created by this teacher
$challenges_stmt = $mysqli->prepare("SELECT id, title FROM challenges WHERE teacher_id = ? ORDER BY created_at DESC");
$challenges_stmt->bind_param('i', $teacher_id);
$challenges_stmt->execute();
$challenges_res = $challenges_stmt->get_result();
$challenges = $challenges_res->fetch_all(MYSQLI_ASSOC);
$challenges_res->close();

// Fetch students
$students = $mysqli->query("SELECT id, name FROM users WHERE role='student' ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch all submissions for this teacher’s challenges
$all_submissions = [];
$sub_stmt = $mysqli->prepare("SELECT cs.* 
                              FROM challenge_submissions cs 
                              JOIN challenges c ON cs.challenge_id = c.id 
                              WHERE c.teacher_id = ?");
$sub_stmt->bind_param('i', $teacher_id);
$sub_stmt->execute();
$res = $sub_stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $all_submissions[$row['challenge_id']][$row['student_id']] = $row;
}
$sub_stmt->close();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Teacher Overview & Review</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; background:#f9f9f9; }
h2, h3 { color: #333; }
table { border-collapse: collapse; width: 100%; margin-bottom: 30px; background:#fff; box-shadow:0 0 8px rgba(0,0,0,0.1); }
th, td { border:1px solid #ccc; padding:10px; text-align:center; }
th { background:#007bff; color:#fff; }
.badge { padding:4px 8px; border-radius:4px; color:#fff; font-weight:bold; display:inline-block; }
.submitted { background:#17a2b8; }   /* Blue */
.approved { background:#28a745; }    /* Green */
.rejected { background:#dc3545; }    /* Red */
.not-submitted { background:#6c757d; }
.btn { padding:5px 10px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer; margin:2px; text-decoration:none; display:inline-block; }
.btn:hover { background:#0056b3; }
</style>
</head>
<body>

<h2>Teacher Overview & Review</h2>

<?php if (count($challenges) === 0): ?>
    <p>No challenges found.</p>
<?php else: ?>
    <?php foreach ($challenges as $c): ?>
        <h3>Challenge: <?= htmlspecialchars($c['title']) ?></h3>
        <table>
            <tr>
                <th>Student Name</th>
                <th>Status</th>
                <th>Points</th>
                <th>Remark</th>
                <th>Action</th>
            </tr>
            <?php foreach ($students as $s):
                $sub = $all_submissions[$c['id']][$s['id']] ?? null;
                $statusBadge = '<span class="badge not-submitted">Not Submitted</span>';

                if ($sub) {
                    if ($sub['status'] === 'completed') {
                        $statusBadge = '<span class="badge submitted">Submitted</span>';
                    } elseif ($sub['status'] === 'approved') {
                        $statusBadge = '<span class="badge approved">Approved</span>';
                    } elseif ($sub['status'] === 'rejected') {
                        $statusBadge = '<span class="badge rejected">Rejected</span>';
                    }
                }
            ?>
            <tr>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><?= $statusBadge ?></td>
                <td><?= $sub ? intval($sub['points_awarded']) : '-' ?></td>
                <td><?= $sub ? htmlspecialchars($sub['teacher_remark'] ?? '-') : '-' ?></td>
                <td>
                    <?php if ($sub): ?>
                        <!-- Single Review button -->
                        <a href="review_challenge.php?submission_id=<?= intval($sub['id']) ?>&challenge_id=<?= intval($c['id']) ?>" class="btn btn-sm btn-info">🔍 Review</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>
<?php endif; ?>

<p><a href="dashboard_teacher.php">⬅ Back to Dashboard</a></p>
</body>
</html>



