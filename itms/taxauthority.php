<?php
session_start();
include 'db.php';

// Ensure that only logged-in authorities can view the page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'taxauthority') {
    // Redirect to login page if the user is not logged in as authority
    header("Location: login.php");
    exit();
}

// Fetch Taxpayers Assigned to Professionals
function fetchTaxpayers($conn) {
    $query = "SELECT p.user_id AS professional_id, p.name AS professional_name,
                     t.user_id AS taxpayer_id, t.name AS taxpayer_name
              FROM TaxProfessional p
              LEFT JOIN Taxpayer t ON p.user_id = t.tax_professional_id";
    return $conn->query($query);
}

$taxpayers = fetchTaxpayers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Authority</title>
    <link rel="stylesheet" href="Styles\taxauthority.css">
    <script>// -------------------- DASHBOARD UI INTERACTIONS --------------------
document.addEventListener("DOMContentLoaded", () => {
  const profileCard = document.getElementById("profileCard");
  const profileIcon = document.querySelector(".profile-icon");

  if (profileIcon && profileCard) {
    profileIcon.addEventListener("click", () => {
      if (profileCard.style.display === "block") {
        profileCard.style.display = "none";
      } else {
        profileCard.style.display = "block";
        profileCard.style.animation = "slideDown 0.4s ease";
      }
    });
  }
});
</script>
</head>
<body>
<div class="container">
    <h1>Tax Authority</h1>

   <div style="text-align:center;">
        <button onclick="window.location.href='add_professional.php'">Add TaxProfessional</button>
        <button onclick="window.location.href='delete_taxprofessional.php'">Delete TaxProfessional</button>
        <button onclick="window.location.href='home.php'">Home</button>
    </div>
    <h2>Taxpayers Assigned to Professionals</h2>
    <table>
        <thead>
            <tr>
                <th>TaxProfessional ID</th>
                <th>TaxProfessional Name</th>
                <th>Taxpayer ID</th>
                <th>Taxpayer Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $taxpayers->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['professional_id']); ?></td>
                <td><?php echo htmlspecialchars($row['professional_name']); ?></td>
                <td><?php echo htmlspecialchars($row['taxpayer_id']); ?></td>
                <td><?php echo htmlspecialchars($row['taxpayer_name']); ?></td>
                <td><a href="fetch_preofessional_details.php?id=<?php echo (int)$row['professional_id']; ?>" style="color:#35F374;">View Details</a></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php
include("footer.php");
?>