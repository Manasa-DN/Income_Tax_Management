<?php
include 'db.php';

if (isset($_GET['id'])) {
    $tax_professional_id = intval($_GET['id']);

    // Fetch details of the selected professional by user_id
    $query = "SELECT * FROM TaxProfessional WHERE user_id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("i", $tax_professional_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $professional = $result->fetch_assoc();

    if ($professional) {
        echo "<h3>Details of Professional</h3>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($professional['Name']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($professional['Email']) . "</p>";
        echo "<p><strong>Phone:</strong> " . htmlspecialchars($professional['Phone']) . "</p>";
    } else {
        echo "No details found for this professional.";
    }
}
?>
