<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die('Not logged in.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request.');
}

$taxpayer_user_id = intval($_SESSION['user_id']);
$professional_user_id = isset($_POST['professional_user_id']) ? intval($_POST['professional_user_id']) : 0;

if ($professional_user_id <= 0) {
    die('Invalid professional.');
}

// Ensure professional exists
$check = $conn->prepare("SELECT 1 FROM TaxProfessional WHERE user_id = ?");
$check->bind_param("i", $professional_user_id);
$check->execute();
$check->store_result();
if ($check->num_rows === 0) {
    $check->close();
    die('Professional not found.');
}
$check->close();

// Assign in taxpayer table
$upd = $conn->prepare("UPDATE Taxpayer SET tax_professional_id = ? WHERE user_id = ?");
$upd->bind_param("ii", $professional_user_id, $taxpayer_user_id);
if ($upd->execute()) {
    header('Location: taxpayer.php');
    exit;
}
echo 'Failed to assign professional: ' . $upd->error;
?>
<?php
include("footer.php");
?>

