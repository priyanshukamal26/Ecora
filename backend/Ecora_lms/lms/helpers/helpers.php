<?php
// helpers/helpers.php
function flash($name, $message = null) {
    if ($message === null) {
        if (isset($_SESSION[$name])) {
            $m = $_SESSION[$name];
            unset($_SESSION[$name]);
            return $m;
        }
        return null;
    } else {
        $_SESSION[$name] = $message;
    }
}

function redirect_to_dashboard() {
    if ($_SESSION['role'] === 'teacher') {
        header('Location: /lms/public/dashboard_teacher.php');
    } else {
        header('Location: /lms/public/dashboard_student.php');
    }
    exit;
}
