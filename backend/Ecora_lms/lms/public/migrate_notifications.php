<?php
require_once __DIR__ . '/../config/db_config.php';

// Convert all old notifications to global
$sql = "UPDATE notifications SET user_id = NULL WHERE user_id IS NOT NULL";
if ($mysqli->query($sql)) {
    echo "All old notifications are now global. New students will see them automatically.";
} else {
    echo "Error: " . $mysqli->error;
}
?>
