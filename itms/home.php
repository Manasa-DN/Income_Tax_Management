
<?php session_start(); ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITMS - Income Tax Management System</title>
    <link rel="stylesheet" href="Styles\style_home.css"> <!-- Link to an external CSS file -->
</head>
<body>
<style>
    body {
        background-image: url("C:/xampp/htdocs/income/income.jpeg");
        background-size: cover;
    }
    
    .login-btn {
  display: inline-block;
  padding: 12px 28px;
  background: orange;
  color: #ffffff;
  font-size: 16px;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-decoration: none;
  border: none;
  border-radius: 50px;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0, 114, 255, 0.25);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

/* Hover Effect */
.login-btn:hover {
  background: linear-gradient(135deg, #00c6ff, #0072ff);
  box-shadow: 0 6px 20px rgba(0, 114, 255, 0.4);
  transform: translateY(-2px);
}

/* Ripple Effect */
.login-btn::after {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  transition: width 0.4s ease, height 0.4s ease;
}

.login-btn:active::after {
  width: 200px;
  height: 200px;
  opacity: 0;
  transition: 0s;
}

/* Optional icon spacing */
.login-btn i {
  margin-right: 8px;
}

.logout-btn {
  display: inline-block;
  padding: 12px 28px;
  background: linear-gradient(135deg, #ff4b2b, #d80738ff);
  color: #ffffff;
  font-size: 16px;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-decoration: none;
  border: none;
  border-radius: 50px;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(255, 65, 108, 0.25);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

/* Hover Effect */
.logout-btn:hover {
  background: linear-gradient(135deg, #ff416c, #ff4b2b);
  box-shadow: 0 6px 20px rgba(255, 65, 108, 0.5);
  transform: translateY(-2px);
}

/* Ripple Effect */
.logout-btn::after {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  transition: width 0.4s ease, height 0.4s ease;
}

.logout-btn:active::after {
  width: 200px;
  height: 200px;
  opacity: 0;
  transition: 0s;
}

/* Optional icon spacing */
.logout-btn i {
  margin-right: 8px;
}

</style>

<!-- Header -->
<header>
    <div class="header-left">
        <a href="login.php" class="login-btn">Login / Signup</a>
    </div>
    <div class="header-right">
        <h1>ITMS - Income Tax Management System</h1>
    </div>
    <a href="logout.php" class="logout-btn" style="color:aqua; text-decoration:none; margin-right:15px;">Logout</a>
</header>

<!-- Main Content -->
<main>
    <?php if (isset($_SESSION['user_id'])) { $role = isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>
        <?php if ($role === 'taxpayer') { ?>
        <section class="compartment">
            <h2>Tax Payer</h2>
            <ul>
                <li><a href="reports.php">View Tax Reports</a></li>
                <li><a href="taxpayer.php">Upload Documents</a></li>
                <li><a href="taxpayer.php#select-professional">Select Tax Professional</a></li>
                <li><a href="taxpayer.php#notifications">Receive Notifications</a></li>
            </ul>
        </section>
        <?php } elseif ($role === 'taxprofessional') { ?>
        <section class="compartment">
            <h2>Tax Professional</h2>
            <ul>
                <li><a href="taxprofessional.php">Manage Client Documents</a></li>
                <li><a href="payments.php">View Payment Details</a></li>
                <li><a href="error_logs.php">Access Error Logs</a></li>
                <li><a href="taxprofessional.php">Send Notifications to Tax Payers</a></li>
            </ul>
        </section>
        <?php } elseif ($role === 'taxauthority') { ?>
        <section class="compartment">
            <h2>Tax Authority</h2>
            <ul>
                <li><a href="reports.php">View Tax Professionals and Clients</a></li>
                <li><a href="payments.php">Monitor Payment Status</a></li>
                <li><a href="reports.php">Generate Tax Reports</a></li>
                <li><a href="refunds.php">Handle Tax Refunds and Audits</a></li>
            </ul>
        </section>
        <?php } ?>
    <?php } else { ?>
        <section class="welcome">
            <h2>Welcome to Income Tax Management System</h2>
            <?php include ("robo.html");?>
        </section>
    <?php } ?>
</main>

</body>
</html>
<?php
include("footer.php");
?>