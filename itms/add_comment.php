<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method.');
}

$professional_user_id = intval($_SESSION['user_id']);
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
$client_user_id = isset($_POST['client_id']) ? intval($_POST['client_id']) : 0;

if ($comment === '' || $client_user_id <= 0) {
    die('Missing comment or client.');
}

// Ensure the client exists and fetch their TaxpayerID
$client_stmt = $conn->prepare("SELECT TaxpayerID FROM Taxpayer WHERE user_id = ?");
if (!$client_stmt) {
    die('Error preparing client lookup: ' . $conn->error);
}
$client_stmt->bind_param("i", $client_user_id);
$client_stmt->execute();
$client_stmt->bind_result($taxpayer_id);
if (!$client_stmt->fetch()) {
    $client_stmt->close();
    die('Client not found.');
}
$client_stmt->close();

// Optional: Verify professional is assigned to this taxpayer
$assign_stmt = $conn->prepare("SELECT * FROM Taxpayer WHERE user_id = ? AND (tax_professional_id = ? OR tax_professional_id IS NULL)");
if ($assign_stmt) {
    $assign_stmt->bind_param("ii", $client_user_id, $professional_user_id);
    $assign_stmt->execute();
    $assign_stmt->store_result();
    if ($assign_stmt->num_rows === 0) {
        $assign_stmt->close();
        die('You are not assigned to this client.');
    }
    $assign_stmt->close();
}

// Insert comment as a notification for the taxpayer
$notification_query = "INSERT INTO notification (UserID, Message, Timestamp, Type, Status) VALUES (?, ?, NOW(), ?, ?)";
$notification_stmt = $conn->prepare($notification_query);
if (!$notification_stmt) {
    die('Error preparing the notification query: ' . $conn->error);
}

$message = "Comment added by professional: " . $comment;
$type = 'comment';
$status = 'Unread';

$notification_stmt->bind_param("isss", $taxpayer_id, $message, $type, $status);
if ($notification_stmt->execute()) {
    header('Location: view_client.php?client_id=' . $client_user_id);
    exit;
} else {
    echo 'Error inserting notification: ' . $notification_stmt->error;
}

$notification_stmt->close();
?>
<?php
include("footer.php");
?>