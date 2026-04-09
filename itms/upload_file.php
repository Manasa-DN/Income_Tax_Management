<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['document'])) {
        $file = $_FILES['document'];

        // Check if the file was uploaded successfully
        if ($file['error'] == 0) {
            // Get the file details
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileType = $file['type'];

            // Define the allowed file types (example: only PDFs)
            $allowedTypes = ['application/pdf'];

            if (!in_array($fileType, $allowedTypes)) {
                echo "Only PDF files are allowed!";
                exit;
            }

            // Save the file on the server (define the directory)
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filePath = $uploadDir . basename($fileName);

            if (move_uploaded_file($fileTmpName, $filePath)) {
                // Insert file details into the database
                $query = "INSERT INTO Documents (user_id, file_path) VALUES (?, ?)";
                $stmt = $conn->prepare($query);

                if (!$stmt) {
                    die("Query preparation failed: " . $conn->error);
                }

                $user_id = $_SESSION['user_id'];
                $stmt->bind_param("is", $user_id, $filePath);
                $stmt->execute();

                echo "Document uploaded successfully!";
                header("Location: taxpayer.php");
                
            } else {
                echo "Failed to upload the document.";
            }
        } else {
            echo "Error uploading file!";
        }
    }
}
?>
<?php
include("footer.php");
?>