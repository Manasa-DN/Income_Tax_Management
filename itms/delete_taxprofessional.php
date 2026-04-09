<?php
// Include database connection
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the 'action' is set and handle it
    $action = $_POST['action'] ?? '';  // Default to empty if not set
    $professionalid = $_POST['professionalid'] ?? 0;  // Default to 0 if not set

    // Check if the action is 'Yes' (proceed with deletion)
    if ($action == 'Yes' && $professionalid > 0) {
        // Call the stored procedure to delete the tax professional
        $query = "CALL DeleteTaxProfessional(?)";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            // Check if prepare() failed
            echo "Error preparing the query: " . $conn->error;
        } else {
            $stmt->bind_param("i", $professionalid);

            if ($stmt->execute()) {
                echo "Tax Professional deleted successfully.";
            } else {
                echo "Error executing query: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Tax Professional</title>
   <link rel="stylesheet" href="Styles\delete_taxprofessional.css">
</head>
<body>
    <div class="container">
        <h1>Delete Tax Professional</h1>
        <h2>Select Tax Professional to Delete</h2>

        <!-- Display list of TaxProfessionals with user_id and Name -->
        <form method="POST" action="">
            <label for="professionalid">Select Professional to Delete:</label>
            <select name="professionalid" id="professionalid" required>
                <?php
                // Query to get user_id and Name of TaxProfessionals
                $result = $conn->query("SELECT user_id, Name FROM TaxProfessional");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['user_id']}'>{$row['Name']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="action" value="select">Select Professional</button>
        </form>

        <?php
        // If a professional is selected, show confirmation message
        if (isset($_POST['action']) && $_POST['action'] == 'select' && isset($_POST['professionalid'])) {
            $professionalid = $_POST['professionalid'];

            // Fetch the selected professional's details
            $result = $conn->query("SELECT Name FROM TaxProfessional WHERE user_id = $professionalid");
            $row = $result->fetch_assoc();
            $professional_name = $row['Name'];
        ?>
            <!-- Confirmation message -->
            <div class="confirmation">
                <h3>Are you sure you want to delete "<?php echo $professional_name; ?>"?</h3>
                <form method="POST" action="">
                    <input type="hidden" name="professionalid" value="<?php echo $professionalid; ?>" />
                    <button type="submit" name="action" value="Yes">Yes, Delete</button>
                    <button type="submit" name="action" value="No">No, Cancel</button>
                </form>
            </div>
        <?php
        }
        ?>

        <?php
        // Show a success message if a professional is deleted
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($stmt) && $stmt->execute() && $action == 'Yes') {
            echo "<div class='message'>Tax Professional deleted successfully.</div>";
        }
        ?>
    </div>
</body>  
</html>
<?php
include("footer.php");
?>