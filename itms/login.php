<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"]; 
    $password = $_POST["password"]; 

    // Admin override (no DB lookup required)
    if ($name === 'admin' && $password === 'admin@123') {
        session_start();
        $_SESSION['user_id'] = 0;
        $_SESSION['role'] = 'admin';
        header("Location: admin.php");
        exit();
    }

    // Fetch user from the database
    $query = "SELECT * FROM User WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if user exists and password matches
    if ($user && password_verify($password, $user['password'])) {
        // Start session and store user info
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        

        // Redirect based on role
        if ($user['role'] == 'taxpayer') {
            header("Location: home.php");
        } elseif ($user['role'] == 'taxprofessional') {
            header("Location: home.php");
        } elseif ($user['role'] == 'taxauthority') {
            header("Location: home.php");
        }

        exit();
    } else {
        echo "<script>alert('Invalid username or password'); window.location.href='login.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="Styles\login.css">
    <script>
        // -------------------- FORM PAGE ANIMATIONS --------------------
document.addEventListener("DOMContentLoaded", () => {
  const container = document.querySelector(".container");
  container.style.opacity = "0";
  container.style.transform = "translateY(40px)";
  container.style.transition = "all 0.8s ease";

  setTimeout(() => {
    container.style.opacity = "1";
    container.style.transform = "translateY(0)";
  }, 150);
});

    </script>
</head>
<body>
   <div class="container">
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <input type="text" name="name" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <div class="redirect-link">
        Don't have an account? <a href="signup.php">Sign up here</a>
    </div>
</div>
</body>
</html>
