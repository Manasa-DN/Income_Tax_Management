<?php
session_start();
include 'db.php';

if (!isset($_GET['client_id']) || empty($_GET['client_id'])) {
    die('Error: Client ID is missing.');
}

$client_id = intval($_GET['client_id']);  // Sanitize client_id
$view_option = isset($_GET['view_option']) ? $_GET['view_option'] : 'recent'; // Default to 'recent'

// Fetch client details
$client_query = "SELECT * FROM Taxpayer WHERE user_id = ?";
$client_stmt = $conn->prepare($client_query);
$client_stmt->bind_param("i", $client_id);
$client_stmt->execute();
$client = $client_stmt->get_result()->fetch_assoc();

if (!$client) {
    die("No client data found for this ID.");
}

// Fetch client revenue
$revenue_query = "SELECT amount FROM tax_revenues WHERE user_id = ?";
$revenue_stmt = $conn->prepare($revenue_query);
$revenue_stmt->bind_param("i", $client_id);
$revenue_stmt->execute();
$revenues = $revenue_stmt->get_result();

// Fetch client files based on view option
if ($view_option == 'recent') {
    $file_query = "SELECT file_path, upload_date FROM Documents WHERE user_id = ? ORDER BY upload_date DESC LIMIT 1";
} else {
    $file_query = "SELECT file_path, upload_date FROM Documents WHERE user_id = ? ORDER BY upload_date DESC";
}

$file_stmt = $conn->prepare($file_query);
$file_stmt->bind_param("i", $client_id);
$file_stmt->execute();
$files = $file_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Details</title>
   <link rel="stylesheet" href="Styles\view_client.css">
</head>
<body>
<div class="container">
    <h1>Client: <?php echo htmlspecialchars($client['Name']); ?></h1>

    <div class="client-details">
        <h2>Revenues</h2>
        <ul>
            <?php while ($revenue = $revenues->fetch_assoc()) { ?>
                <li>Amount: ₹<?php echo htmlspecialchars($revenue['amount']); ?></li>
            <?php } ?>
        </ul>
    </div>

    <div class="client-details">
        <h2>Files</h2>
        <div class="file-options">
            <a href="?client_id=<?php echo $client_id; ?>&view_option=recent" class="btn">View Recent File</a>
            <a href="?client_id=<?php echo $client_id; ?>&view_option=all" class="btn">View All Files</a>
        </div>
        <ul>
            <?php while ($file = $files->fetch_assoc()) { ?>
                <li><a href="<?php echo htmlspecialchars($file['file_path']); ?>">View File (Uploaded on <?php echo htmlspecialchars($file['upload_date']); ?>)</a></li>
            <?php } ?>
        </ul>
    </div>

    <div class="comment-form">
        <form action="add_comment.php" method="POST">
            <h3>TAX-Calculation Status</h3>
            <textarea name="comment" required></textarea>
            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
            <button type="submit">Notify Payer</button>
        </form>
    </div>
</div>
</body>
</html>

<?php
include("button.php");?>
<?php
include("footer.php");
?>