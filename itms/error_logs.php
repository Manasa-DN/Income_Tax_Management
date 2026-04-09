<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { die('Not logged in.'); }
$user_id = intval($_SESSION['user_id']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

$table_check = $conn->query("SHOW TABLES LIKE 'error_logs'");
if (!$table_check || $table_check->num_rows === 0) { echo "No error logs table."; exit; }

if ($role === 'taxauthority') {
    $rows = $conn->query("SELECT user_id, role, message, created_at FROM error_logs ORDER BY created_at DESC");
} else {
    $stmt = $conn->prepare("SELECT role, message, created_at FROM error_logs WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $rows = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Error Logs</title>
<link rel="stylesheet" href="Styles\error_logs.css">
</head>
<body>
<div class="container">
<h2>Error Logs</h2>
<ul>
<?php while ($r = $rows->fetch_assoc()) { ?>
<li>
<?php if (!empty($r['user_id'])) { ?>User: <?php echo (int)$r['user_id']; ?> — <?php } ?>
<?php echo htmlspecialchars($r['role'] ?? ''); ?> — <?php echo htmlspecialchars($r['message']); ?> — <?php echo htmlspecialchars($r['created_at']); ?>
</li>
<?php } ?>
</ul>
</div>
</body>
</html>
<?php include("button.php"); ?>

<?php include("footer.php"); ?>
<?php
