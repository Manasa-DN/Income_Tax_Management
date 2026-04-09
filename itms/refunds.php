<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die('Not logged in.');
}
$user_id = intval($_SESSION['user_id']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

$table_check = $conn->query("SHOW TABLES LIKE 'tax_refunds'");
if (!$table_check || $table_check->num_rows === 0) {
    echo "No tax_refunds table. Please import newone.sql.";
    exit;
}

if ($role === 'taxpayer') {
    $stmt = $conn->prepare("SELECT amount, status, requested_at FROM tax_refunds WHERE user_id = ? ORDER BY requested_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $rows = $stmt->get_result();
} elseif ($role === 'taxprofessional') {
    $stmt = $conn->prepare("SELECT TR.amount, TR.status, TR.requested_at, T.Name AS taxpayer_name FROM tax_refunds TR JOIN Taxpayer T ON T.user_id = TR.user_id WHERE T.tax_professional_id = ? ORDER BY TR.requested_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $rows = $stmt->get_result();
} elseif ($role === 'taxauthority') {
    $rows = $conn->query("SELECT TR.amount, TR.status, TR.requested_at, T.Name AS taxpayer_name FROM tax_refunds TR JOIN Taxpayer T ON T.user_id = TR.user_id ORDER BY TR.requested_at DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tax Refunds</title>
<link rel="stylesheet" href="Styles\refunds.css">
</head>
<body>
<div class="container">
<h2>Tax Refunds</h2>
<ul>
<?php while ($r = $rows->fetch_assoc()) { ?>
    <li>
        ₹<?php echo htmlspecialchars($r['amount']); ?> — <?php echo htmlspecialchars($r['status']); ?> — <?php echo htmlspecialchars($r['requested_at']); ?>
        <?php if (!empty($r['taxpayer_name'])) { ?> — <?php echo htmlspecialchars($r['taxpayer_name']); ?><?php } ?>
    </li>
<?php } ?>
</ul>
</div>
</body>
</html>
<?php include("button.php"); ?>
<?php
include("footer.php");
?>