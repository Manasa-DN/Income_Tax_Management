<?php
include 'db.php';

session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];
$revenue = isset($_POST['revenue']) ? floatval($_POST['revenue']) : 0;
if ($revenue <= 0) {
    die("Invalid revenue amount.");
}

// Fetch tax_professional_id for the current taxpayer
$professional_query = "SELECT tax_professional_id FROM Taxpayer WHERE user_id = ?";
$professional_stmt = $conn->prepare($professional_query);

if (!$professional_stmt) {
    die("Professional query preparation failed: " . $conn->error);
}

$professional_stmt->bind_param("i", $user_id);
$professional_stmt->execute();
$professional_result = $professional_stmt->get_result();
$taxpayer = $professional_result->fetch_assoc();

if (!$taxpayer) {
    die("No tax professional associated with this taxpayer.");
}

$tax_professional_id = $taxpayer['tax_professional_id'];

// Insert revenue with user_id and tax_professional_id
$query = "INSERT INTO tax_revenues (user_id, amount, tax_professional_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Insert query preparation failed: " . $conn->error);
}

$stmt->bind_param("idi", $user_id, $revenue, $tax_professional_id);
$stmt->execute();

// Redirect to the taxpayer dashboard
header("Location: taxpayer.php");
exit;
?>

