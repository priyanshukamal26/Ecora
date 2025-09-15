<?php
// public/login.php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/helpers.php';

if (is_logged_in()) {
    redirect_to_dashboard();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = "Provide email and password.";
    } else {
        $stmt = $mysqli->prepare("SELECT id,name,password_hash,role,points FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($id, $name, $password_hash, $role, $points);
        if ($stmt->fetch()) {
            if (password_verify($password, $password_hash)) {
                // login success
                session_regenerate_id(true);
                $_SESSION['user_id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                $_SESSION['points'] = $points;
                redirect_to_dashboard();
            } else {
                $error = "Invalid credentials.";
            }
        } else {
            $error = "Invalid credentials.";
        }
        $stmt->close();
    }
}
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Encora</title>
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

    <!-- Login Section -->
    <section class="section intro-section">
        <div class="intro-content" style="max-width:400px; margin:auto; text-align:left;">
            <h2>Login to Encora</h2>

            <!-- Success/Error Messages -->
            <?php if ($success): ?>
                <p style="color:green; font-weight:bold;"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <p style="color:red; font-weight:bold;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="post" action="">
                <label>Email</label><br>
                <input name="email" type="email" required class="form-input"><br>
                <label>Password</label><br>
                <input name="password" type="password" required class="form-input"><br><br>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p>Not registered? <a href="register.php">Register here</a></p>
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
