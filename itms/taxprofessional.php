<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'taxprofessional') {
    die("Access denied.");
}

$professional_id = $_SESSION['user_id']; // Assuming the professional logs in

// Fetch tax professional's info
$professional_query = "SELECT * FROM TaxProfessional WHERE user_id = ?";
$professional_stmt = $conn->prepare($professional_query);
$professional_stmt->bind_param("i", $professional_id);
$professional_stmt->execute();
$professional_result = $professional_stmt->get_result();

if ($professional_result->num_rows > 0) {
    $professional = $professional_result->fetch_assoc();
} else {
    // Handle case where no professional data is found
    $professional = null;
}

// Fetch clients assigned to the professional
$clients_query = "SELECT * FROM Taxpayer WHERE tax_professional_id = ?";
$clients_stmt = $conn->prepare($clients_query);
$clients_stmt->bind_param("i", $professional_id);
$clients_stmt->execute();
$clients = $clients_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Professional Dashboard</title>
    <link rel="stylesheet" href="Styles\taxprofessional.css">
</head>
<body>

<header>
    <div class="profile-icon" onclick="toggleProfileCard()">👤</div>
    <div style="flex-grow:1; text-align:center;"><h1>Tax Professional Dashboard</h1></div>
    <div>
        <a href="home.php" style="color:#35F374; text-decoration:none; margin-right:15px;">Home</a>
    </div>
</header>

<!-- Profile Card Dropdown -->
<div class="profile-card" id="profileCard">
    <h4><?php echo htmlspecialchars($professional['Name']); ?></h4>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($professional['Email'] ?? 'N/A'); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($professional['Phone'] ?? 'N/A'); ?></p>
    <p><a href="logout.php">Logout</a></p>
</div>

<div class="dashboard-content">
    <h1>Your Clients</h1>
    <?php while ($client = $clients->fetch_assoc()) { ?>
        <div class="client-card">
            <a href="view_client.php?client_id=<?php echo $client['user_id']; ?>">
                <?php echo htmlspecialchars($client['Name']); ?>
            </a>
        </div>
    <?php } ?>
</div>

<script>
function toggleProfileCard() {
    const card = document.getElementById('profileCard');
    card.style.display = (card.style.display === 'block') ? 'none' : 'block';
}
</script>

</body>
</html>
<?php include("button.php"); ?>
<?php
include("footer.php");
?>
