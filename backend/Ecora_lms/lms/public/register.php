<?php
// public/register.php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role = ($_POST['role'] === 'teacher') ? 'teacher' : 'student';

    if (!$name || !$email || !$password) {
        $error = "All fields are required.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email.";
    } else {
        // check email exists
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Email already registered. Please login.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $mysqli->prepare("INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,?)");
            $ins->bind_param('ssss', $name, $email, $password_hash, $role);
            if ($ins->execute()) {
                // initialize module_progress for students
                if ($role === 'student') {
                    $student_id = $ins->insert_id;
                    $res = $mysqli->query("SELECT id,sequence FROM modules ORDER BY sequence ASC");
                    while ($row = $res->fetch_assoc()) {
                        $status = ($row['sequence'] == 1) ? 'unlocked' : 'locked';
                        $prep = $mysqli->prepare("INSERT INTO module_progress (user_id,module_id,status) VALUES (?,?,?)");
                        $prep->bind_param('iis', $student_id, $row['id'], $status);
                        $prep->execute();
                        $prep->close();
                    }
                }
                $_SESSION['success'] = "Registered successfully. Please login.";
                header('Location: login.php');
                exit;
            } else {
                $error = "Registration failed: " . $mysqli->error;
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Encora</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="main.js"></script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <a href="index.html" class="logo">Encora</a>
        <nav>
            <ul class="nav-links" id="navLinks">
                <li class="navlink"><a href="index.html#home">Home</a></li>
                <li class="navlink"><a href="login.php">Login</a></li>
                <li class="navlink"><a href="register.php">Register</a></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <!-- Register Section -->
    <section class="section intro-section">
        <div class="intro-content" style="max-width:400px; margin:auto; text-align:left;">
            <h2>Create an Account</h2>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <p style="color:red; font-weight:bold;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <!-- Register Form -->
            <form method="post" action="">
                <label>Name</label><br>
                <input name="name" required class="form-input"><br>
                <label>Email</label><br>
                <input name="email" type="email" required class="form-input"><br>
                <label>Password</label><br>
                <input name="password" type="password" required class="form-input"><br>
                <label>Role</label><br>
                <select name="role" class="form-input">
                    <option value="student" selected>Student</option>
                    <option value="teacher">Teacher</option>
                </select><br><br>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <p>Already registered? <a href="login.php">Login</a></p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer section1">
        <div class="footer-content">
            <div class="footer-links">
                <h3>Quick Links</h3>
                <p>
                    <a href="index.html#home">Home</a>
                    <a href="lessons.html">Lessons</a>
                    <a href="quiz.html">Quizzes</a>
                    <a href="tasks.html">Tasks</a>
                    <a href="leaderboard.html">Leaderboard</a>
                    <a href="contact.html">Contact</a>
                </p>
            </div>
            <div class="footer-contact">
                <h3>Contact Info</h3>
                <p>Helpline: +91-9876543210</p>
                <p>Email: support@encora.org</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Made with ❤️ by Encora Team, 2025</p>
        </div>
    </footer>
</body>
</html>

