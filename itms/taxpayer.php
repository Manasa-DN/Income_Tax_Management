<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Session user_id not set!";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch taxpayer details using the user_id (assuming user_id corresponds to TaxpayerID in the Taxpayer table)
$query = "SELECT * FROM `Taxpayer` WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$taxpayer = $stmt->get_result()->fetch_assoc();

if (!$taxpayer) {
    echo "No taxpayer data found for this user.";
    exit;
}

// Now that we have the taxpayer, get the TaxpayerID (which corresponds to UserID in Notification table)
$taxpayer_id = $taxpayer['TaxpayerID'];  // TaxpayerID is the actual ID we use for notifications

// Fetch assigned professional's name based on taxpayer's tax_professional_id
$professional_query = "SELECT name, email, phone FROM TaxProfessional WHERE user_id = ?";
$professional_stmt = $conn->prepare($professional_query);
$professional_stmt->bind_param("i", $taxpayer['tax_professional_id']);
$professional_stmt->execute();
$professional = $professional_stmt->get_result()->fetch_assoc();

// Fetch most recent notification for taxpayer using the TaxpayerID
$recent_notification_query = "SELECT * FROM notification WHERE UserID = ? ORDER BY Timestamp DESC LIMIT 1";
$recent_notification_stmt = $conn->prepare($recent_notification_query);
$recent_notification_stmt->bind_param("i", $taxpayer_id);  // Use taxpayer_id here
$recent_notification_stmt->execute();
$recent_notification = $recent_notification_stmt->get_result()->fetch_assoc();

// Fetch all notifications for taxpayer (excluding the most recent one)
$notifications_query = "SELECT * FROM notification WHERE UserID = ? AND NotificationID != ? ORDER BY Timestamp DESC";
$notifications_stmt = $conn->prepare($notifications_query);
$notifications_stmt->bind_param("ii", $taxpayer_id, $recent_notification['NotificationID']);
$notifications_stmt->execute();
$notifications = $notifications_stmt->get_result();

// Fetch total revenue for estimated tax calculation
$revenue_query = "SELECT SUM(amount) as total_revenue FROM tax_revenues WHERE user_id = ?";
$revenue_stmt = $conn->prepare($revenue_query);
$revenue_stmt->bind_param("i", $user_id);
$revenue_stmt->execute();
$revenue_result = $revenue_stmt->get_result()->fetch_assoc();
$total_revenue = $revenue_result['total_revenue'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taxpayer Dashboard</title>
   <link rel="stylesheet" href="Styles\taxpayer.css">
</head>
<body>

<header>
    <h1>Taxpayer Dashboard</h1>
    <div>
        <a href="home.php" style="color:#35F374; text-decoration:none; margin-right:15px;">Home</a>
    </div>
    <div class="profile-icon" onclick="toggleProfileCard()">
        👤
    </div>
    <div class="profile-card" id="profile-card">
        <h4><?php echo htmlspecialchars($taxpayer['Name']); ?></h4>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($taxpayer['Email'] ?? 'N/A'); ?></p>
        <p><strong>Phone No:</strong> <?php echo htmlspecialchars($taxpayer['Phone'] ?? 'N/A'); ?></p>
        <p><strong>Professional:</strong> <?php echo htmlspecialchars($professional['name'] ?? 'N/A'); ?></p>
        <p><a href="logout.php" style="color:#35F374; text-decoration:none;">Logout</a></p>
    </div>
</header>

<div class="content">
    <h2>Hi, <?php echo htmlspecialchars($taxpayer['Name']); ?>!</h2>

    <div class="form-container" id="select-professional">
        <h3>Upload Document</h3>
        <form action="upload_file.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="document" required>
            <button type="submit">Upload Document</button>
        </form>

        <h3>Add Revenue</h3>
        <form action="add_revenue.php" method="POST">
            <input type="number" name="revenue" placeholder="Enter Revenue" required>
            <button type="submit">Add Revenue</button>
        </form>

        <h3>Select Tax Professional</h3>
        <?php
        // Fetch list of professionals for selection
        $pros_stmt = $conn->prepare("SELECT TP.user_id, TP.Name FROM TaxProfessional TP ORDER BY TP.Name ASC");
        $pros_stmt->execute();
        $pros_res = $pros_stmt->get_result();
        ?>
        <form action="assign_professional.php" method="POST">
            <select name="professional_user_id" required>
                <option value="">-- Select Professional --</option>
                <?php while ($pro = $pros_res->fetch_assoc()) { ?>
                    <option value="<?php echo (int)$pro['user_id']; ?>" <?php echo ($taxpayer['tax_professional_id'] == $pro['user_id'] ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($pro['Name']); ?>
                    </option>
                <?php } ?>
            </select>
            <button type="submit">Assign</button>
        </form>
    </div>

    <!-- Estimated Tax Calculator -->
    <div class="tax-calculator">
        <h3>📊 Estimated Tax Calculator</h3>
        
        <div class="current-revenue">
            <strong>Your Current Total Revenue: ₹<?php echo number_format($total_revenue, 2); ?></strong>
        </div>

        <div class="tax-input-group">
            <input type="number" id="annual-income" placeholder="Annual Income (₹)" value="<?php echo $total_revenue; ?>">
            <input type="number" id="deductions" placeholder="Total Deductions (₹)" value="50000">
        </div>

        <div class="tax-input-group">
            <input type="number" id="previous-tax" placeholder="Previous Tax Paid (₹)" value="0">
        </div>

        <button class="calculate-btn" onclick="calculateEstimatedTax()">Calculate Estimated Tax</button>

        <div class="tax-result" id="tax-result" style="display: none;">
            <h4>Tax Calculation Results</h4>
            <div class="tax-breakdown">
                <div class="tax-item">
                    <div>Taxable Income</div>
                    <div><strong id="taxable-income">₹0</strong></div>
                </div>
                <div class="tax-item">
                    <div>Tax Rate</div>
                    <div><strong id="tax-rate">0%</strong></div>
                </div>
                <div class="tax-item">
                    <div>Total Tax</div>
                    <div><strong id="total-tax">₹0</strong></div>
                </div>
                <div class="tax-item">
                    <div>Estimated Tax Due</div>
                    <div><strong id="tax-due">₹0</strong></div>
                </div>
            </div>
            <div style="margin-top: 15px; padding: 10px; background-color: #333; border-radius: 5px; text-align: center;">
                <strong style="color: #35F374;">Final Estimated Tax: <span id="final-tax">₹0</span></strong>
            </div>
        </div>
    </div>

    <div class="notification-box" id="notifications">
        <h3>Recent Notification</h3>
        <?php if ($recent_notification && isset($recent_notification['Message'])): ?>
            <p><?php echo htmlspecialchars($recent_notification['Message']); ?>
            <?php if (isset($recent_notification['Type']) && $recent_notification['Type'] === 'comment') { ?>
                <span>(Comment from Professional)</span>
            <?php } ?>
            </p>
        <?php else: ?>
            <p>No recent notifications.</p>
        <?php endif; ?>

        <h3>All Notifications</h3>
        <ul>
            <?php while ($notification = $notifications->fetch_assoc()) { ?>
                <li>
                    <?php if (isset($notification['Message'])) {
                        echo htmlspecialchars($notification['Message']);
                    } ?>
                    <?php if (isset($notification['Type']) && $notification['Type'] === 'comment') { ?>
                        <span>(Comment from Professional)</span>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>

<script>
    function toggleProfileCard() {
        var card = document.getElementById('profile-card');
        card.style.display = (card.style.display === 'block') ? 'none' : 'block';
    }

    function calculateEstimatedTax() {
        const annualIncome = parseFloat(document.getElementById('annual-income').value) || 0;
        const deductions = parseFloat(document.getElementById('deductions').value) || 0;
        const previousTax = parseFloat(document.getElementById('previous-tax').value) || 0;

        // Calculate taxable income
        const taxableIncome = Math.max(0, annualIncome - deductions);

        // Indian Tax Slabs (New Regime - FY 2023–24)
        let tax = 0;
        let taxRate = 0;
        let slabText = "";
        let slabColor = "";

        if (taxableIncome <= 300000) {
            tax = 0;
            taxRate = 0;
            slabText = "You fall under the 0% tax slab (Up to ₹3,00,000 is exempt).";
            slabColor = "#35F374"; // green
        } else if (taxableIncome <= 600000) {
            tax = (taxableIncome - 300000) * 0.05;
            taxRate = 5;
            slabText = "You fall under the 5% tax slab (₹3L – ₹6L).";
            slabColor = "#9BE23D"; // light green
        } else if (taxableIncome <= 900000) {
            tax = 15000 + (taxableIncome - 600000) * 0.10;
            taxRate = 10;
            slabText = "You fall under the 10% tax slab (₹6L – ₹9L).";
            slabColor = "#FFD93D"; // yellow
        } else if (taxableIncome <= 1200000) {
            tax = 45000 + (taxableIncome - 900000) * 0.15;
            taxRate = 15;
            slabText = "You fall under the 15% tax slab (₹9L – ₹12L).";
            slabColor = "#FFC300"; // orange
        } else if (taxableIncome <= 1500000) {
            tax = 90000 + (taxableIncome - 1200000) * 0.20;
            taxRate = 20;
            slabText = "You fall under the 20% tax slab (₹12L – ₹15L).";
            slabColor = "#FF5733"; // dark orange
        } else {
            tax = 150000 + (taxableIncome - 1500000) * 0.30;
            taxRate = 30;
            slabText = "You fall under the 30% tax slab (Above ₹15L).";
            slabColor = "#C70039"; // red
        }

        // Add 4% cess
        const cess = tax * 0.04;
        const totalTax = tax + cess;

        // Calculate final due tax
        const taxDue = Math.max(0, totalTax - previousTax);

        // Display results
        document.getElementById('taxable-income').textContent = '₹' + taxableIncome.toLocaleString('en-IN');
        document.getElementById('tax-rate').textContent = taxRate + '%';
        document.getElementById('total-tax').textContent = '₹' + totalTax.toLocaleString('en-IN');
        document.getElementById('tax-due').textContent = '₹' + taxDue.toLocaleString('en-IN');
        document.getElementById('final-tax').textContent = '₹' + taxDue.toLocaleString('en-IN');

        // Add slab info text
        let slabInfo = document.getElementById('slab-info');
        if (!slabInfo) {
            slabInfo = document.createElement('div');
            slabInfo.id = 'slab-info';
            slabInfo.style.marginTop = '10px';
            slabInfo.style.fontWeight = 'bold';
            slabInfo.style.textAlign = 'center';
            document.getElementById('tax-result').appendChild(slabInfo);
        }
        slabInfo.textContent = slabText;
        slabInfo.style.color = slabColor;

        // Add slab indicator bar
        let slabBar = document.getElementById('slab-bar');
        if (!slabBar) {
            slabBar = document.createElement('div');
            slabBar.id = 'slab-bar';
            slabBar.style.height = '12px';
            slabBar.style.borderRadius = '10px';
            slabBar.style.marginTop = '8px';
            slabBar.style.transition = 'background-color 0.3s ease';
            document.getElementById('tax-result').appendChild(slabBar);
        }
        slabBar.style.backgroundColor = slabColor;

        // Show result box
        document.getElementById('tax-result').style.display = 'block';
    }
</script>



</body>
</html>
<?php include("button.php"); ?>
<?php
include("footer.php");
?>