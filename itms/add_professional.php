<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $tin = $_POST['tin'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
    $certification_id = $_POST['certification_id'];

    $query = "CALL AddTaxProfessional(?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $name, $email, $phone, $tin, $password, $certification_id);

    if ($stmt->execute()) {
        echo "Tax Professional added successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tax Professional</title>

    <style>
    /* -------------------- ADD TAX PROFESSIONAL PAGE -------------------- */
    body {
        background: linear-gradient(135deg, #f8fafc, #e2eaf2);
        font-family: 'Segoe UI', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        animation: fadeIn 0.8s ease;
        margin: 0;
    }

    .container {
        background: #ffffff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        width: 100%;
        max-width: 480px;
        animation: slideUp 1s ease;
        text-align: center;
    }

    h1 {
        color: #0d3b66;
        text-align: center;
        margin-bottom: 20px;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    input {
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 6px;
        border: 1px solid #ccc;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    input:focus {
        border-color: #0d3b66;
        outline: none;
        box-shadow: 0 0 5px rgba(13, 59, 102, 0.3);
    }

    button {
        background: #0d3b66;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s ease, transform 0.3s ease;
    }

    button:hover {
        background: #faa916;
        color: #0d3b66;
        transform: scale(1.05);
    }

    .message {
        text-align: center;
        margin-top: 15px;
        font-weight: 500;
        color: #35b34a;
        background: #e9fbe9;
        padding: 10px;
        border-radius: 6px;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @media (max-width: 768px) {
        .container {
            padding: 25px;
            max-width: 90%;
        }
        h1 {
            font-size: 22px;
        }
        input, button {
            font-size: 14px;
        }
    }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Tax Professional</h1>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Name" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="text" name="phone" placeholder="Phone" required><br>
            <input type="text" name="tin" placeholder="TIN" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="text" name="certification_id" placeholder="Certification ID" required><br>
            <button type="submit">Add Professional</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($stmt) && $stmt->execute()) {
            echo "<div class='message'>Tax Professional added successfully.</div>";
        }
        ?>
    </div>
</body>
</html>
<?php
include("footer.php");
?>