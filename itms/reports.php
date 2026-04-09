<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die('Not logged in.');
}
$user_id = intval($_SESSION['user_id']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// Build scoped reports
if ($role === 'taxpayer') {
    // Revenues and documents for this taxpayer
    $rev_stmt = $conn->prepare("SELECT revenue_id, amount, created_at FROM tax_revenues WHERE user_id = ? ORDER BY created_at DESC");
    $rev_stmt->bind_param("i", $user_id);
    $rev_stmt->execute();
    $revenues = $rev_stmt->get_result();

    $doc_stmt = $conn->prepare("SELECT file_path, upload_date FROM Documents WHERE user_id = ? ORDER BY upload_date DESC");
    $doc_stmt->bind_param("i", $user_id);
    $doc_stmt->execute();
    $documents = $doc_stmt->get_result();
} elseif ($role === 'taxprofessional') {
    // Clients assigned to this professional and their latest revenue
    $clients = $conn->prepare("SELECT T.Name, T.user_id, (SELECT SUM(amount) FROM tax_revenues WHERE user_id = T.user_id) AS total_revenue FROM Taxpayer T WHERE T.tax_professional_id = ? ORDER BY T.Name");
    $clients->bind_param("i", $user_id);
    $clients->execute();
    $clients_res = $clients->get_result();
} elseif ($role === 'taxauthority') {
    // Overview counts
    $counts = [
        'taxpayers' => $conn->query("SELECT COUNT(*) c FROM Taxpayer")->fetch_assoc()['c'] ?? 0,
        'professionals' => $conn->query("SELECT COUNT(*) c FROM TaxProfessional")->fetch_assoc()['c'] ?? 0,
        'revenues' => $conn->query("SELECT IFNULL(SUM(amount),0) c FROM tax_revenues")->fetch_assoc()['c'] ?? 0,
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reports</title>
    <link rel="stylesheet" href="Styles\report.css">
    <script>// -------------------- UI ANIMATIONS --------------------
document.addEventListener("DOMContentLoaded", () => {
  // Add subtle fade-up effect to all visible sections
  const sections = document.querySelectorAll(".client-details, .comment-form, ul li");
  sections.forEach((sec, index) => {
    sec.style.opacity = "0";
    sec.style.transform = "translateY(20px)";
    sec.style.transition = `all 0.6s ease ${index * 0.1}s`;
    setTimeout(() => {
      sec.style.opacity = "1";
      sec.style.transform = "translateY(0)";
    }, 100);
  });
});</script>

</head>
<body>
<div class="container">
<?php if ($role === 'taxpayer') { ?>
    <h2>Your Revenues</h2>
    <ul>
        <?php while ($r = $revenues->fetch_assoc()) { ?>
            <li>₹<?php echo htmlspecialchars($r['amount']); ?> — <?php echo htmlspecialchars($r['created_at']); ?></li>
        <?php } ?>
    </ul>
    <h2>Your Documents</h2>
    <ul>
        <?php while ($d = $documents->fetch_assoc()) { ?>
            <li><a href="<?php echo htmlspecialchars($d['file_path']); ?>">Document</a> — <?php echo htmlspecialchars($d['upload_date']); ?></li>
        <?php } ?>
    </ul>
<?php } elseif ($role === 'taxprofessional') { ?>
    <h2>Your Clients</h2>
    <ul>
        <?php while ($c = $clients_res->fetch_assoc()) { ?>
            <li><?php echo htmlspecialchars($c['Name']); ?> — Total Revenue: ₹<?php echo htmlspecialchars($c['total_revenue'] ?? 0); ?></li>
        <?php } ?>
    </ul>
<?php } elseif ($role === 'taxauthority') { ?>
    <h2>Overview</h2>
    <ul>
        <li>Taxpayers: <?php echo (int)$counts['taxpayers']; ?></li>
        <li>Professionals: <?php echo (int)$counts['professionals']; ?></li>
        <li>Total Revenue: ₹<?php echo htmlspecialchars($counts['revenues']); ?></li>
    </ul>
<?php } else { ?>
    <p>Unknown role.</p>
<?php } ?>
</div>
</body>
</html>
<?php include("button.php"); ?>
<?php
include("footer.php");
?>
