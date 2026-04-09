<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { die('Not logged in.'); }
$user_id = intval($_SESSION['user_id']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

if ($role === 'taxpayer') {
    $stmt = $conn->prepare("SELECT amount, created_at FROM tax_revenues WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $rows = $stmt->get_result();
} elseif ($role === 'taxprofessional') {
    $stmt = $conn->prepare("SELECT TR.amount, TR.created_at, T.Name AS taxpayer_name FROM tax_revenues TR JOIN Taxpayer T ON T.user_id = TR.user_id WHERE TR.tax_professional_id = ? ORDER BY TR.created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $rows = $stmt->get_result();
} elseif ($role === 'taxauthority') {
    $rows = $conn->query("SELECT TR.amount, TR.created_at, T.Name AS taxpayer_name FROM tax_revenues TR JOIN Taxpayer T ON T.user_id = TR.user_id ORDER BY TR.created_at DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments</title>
<link rel="stylesheet" href="Styles\payment.css">
</head>
<body>
<div class="container">
<h2>Payments</h2>
<ul>
<?php while ($r = $rows->fetch_assoc()) { ?>
    <li>
        ₹<?php echo htmlspecialchars($r['amount']); ?> — <?php echo htmlspecialchars($r['created_at']); ?>
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