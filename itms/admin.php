<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Handle CRUD operations
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_taxpayer') {
        $name = $_POST['name']; $email = $_POST['email']; $phone = $_POST['phone'];
        $address = $_POST['address'] ?? '';
        $pwd = password_hash('password123', PASSWORD_DEFAULT);
        $tin = 'TIN' . rand(1000,9999);
        $conn->begin_transaction();
        try {
            $u = $conn->prepare("INSERT INTO User(name,password,role,email,phone) VALUES(?,?,?,?,?)");
            $role_user = 'taxpayer';
            $u->bind_param('sssss', $name, $pwd, $role_user, $email, $phone);
            $u->execute(); $uid = $conn->insert_id;
            $t = $conn->prepare("INSERT INTO Taxpayer(user_id, name, tin, address, email, phone, RegistrationDate, Password) VALUES(?,?,?,?,?,?,NOW(),?)");
            $t->bind_param('issssss', $uid, $name, $tin, $address, $email, $phone, $pwd);
            $t->execute();
            $conn->commit();
            $message = 'Taxpayer added';
        } catch (Exception $e) { $conn->rollback(); $message = 'Error: '.$e->getMessage(); }
    } elseif ($action === 'add_professional') {
        $name = $_POST['name']; $email = $_POST['email']; $phone = $_POST['phone'];
        $pwd = password_hash('password123', PASSWORD_DEFAULT);
        $tin = 'TIN' . rand(1000,9999); $cert = 'CERT' . rand(1000,9999);
        $conn->begin_transaction();
        try {
            $u = $conn->prepare("INSERT INTO User(name,password,role,email,phone) VALUES(?,?,?,?,?)");
            $role_user = 'taxprofessional';
            $u->bind_param('sssss', $name, $pwd, $role_user, $email, $phone);
            $u->execute(); $uid = $conn->insert_id;
            $t = $conn->prepare("INSERT INTO TaxProfessional(user_id, name, tin, Certification_ID, email, phone, RegistrationDate, Password) VALUES(?,?,?,?,?,?,NOW(),?)");
            $t->bind_param('issssss', $uid, $name, $tin, $cert, $email, $phone, $pwd);
            $t->execute();
            $conn->commit();
            $message = 'Professional added';
        } catch (Exception $e) { $conn->rollback(); $message = 'Error: '.$e->getMessage(); }
    } elseif ($action === 'add_authority') {
        $name = $_POST['name']; $email = $_POST['email']; $phone = $_POST['phone'];
        $pwd = password_hash('password123', PASSWORD_DEFAULT);
        $conn->begin_transaction();
        try {
            $u = $conn->prepare("INSERT INTO User(name,password,role,email,phone) VALUES(?,?,?,?,?)");
            $role_user = 'taxauthority';
            $u->bind_param('sssss', $name, $pwd, $role_user, $email, $phone);
            $u->execute(); $uid = $conn->insert_id;
            $t = $conn->prepare("INSERT INTO TaxAuthority(user_id, Name, Email, Phone, Designation, Department) VALUES(?,?,?,?, 'Officer', 'Income Dept')");
            $t->bind_param('isss', $uid, $name, $email, $phone);
            $t->execute();
            $conn->commit();
            $message = 'Authority added';
        } catch (Exception $e) { $conn->rollback(); $message = 'Error: '.$e->getMessage(); }
    } elseif ($action === 'delete_user') {
        $uid = intval($_POST['user_id']);
        $conn->begin_transaction();
        try {
            // If deleting a professional, detach assigned taxpayers
            $upd = $conn->prepare("UPDATE Taxpayer SET tax_professional_id = NULL WHERE tax_professional_id = ?");
            $upd->bind_param('i', $uid);
            $upd->execute();

            // Delete dependent rows for taxpayer entity mapping to this user
            // Resolve TaxpayerID first (may not exist if not a taxpayer)
            $tx = $conn->prepare("SELECT TaxpayerID FROM Taxpayer WHERE user_id = ?");
            $tx->bind_param('i', $uid);
            $tx->execute();
            $tx->bind_result($taxpayerId);
            $taxpayerIdVal = null;
            if ($tx->fetch()) { $taxpayerIdVal = $taxpayerId; }
            $tx->close();

            if (!is_null($taxpayerIdVal)) {
                $delNotif = $conn->prepare("DELETE FROM Notification WHERE UserID = ?");
                $delNotif->bind_param('i', $taxpayerIdVal);
                $delNotif->execute();
            }

            // Remove per-user artifacts
            $del = $conn->prepare("DELETE FROM tax_revenues WHERE user_id = ?");
            $del->bind_param('i', $uid);
            $del->execute();

            $del = $conn->prepare("DELETE FROM Documents WHERE user_id = ?");
            $del->bind_param('i', $uid);
            $del->execute();

            $del = $conn->prepare("DELETE FROM tax_refunds WHERE user_id = ?");
            $del->bind_param('i', $uid);
            $del->execute();

            // Delete role rows (whichever exist)
            $del = $conn->prepare("DELETE FROM Taxpayer WHERE user_id = ?");
            $del->bind_param('i', $uid);
            $del->execute();

            $del = $conn->prepare("DELETE FROM TaxProfessional WHERE user_id = ?");
            $del->bind_param('i', $uid);
            $del->execute();

            $del = $conn->prepare("DELETE FROM TaxAuthority WHERE user_id = ?");
            $del->bind_param('i', $uid);
            $del->execute();

            // Finally delete the base user
            $del = $conn->prepare("DELETE FROM User WHERE id = ?");
            $del->bind_param('i', $uid);
            $del->execute();

            $conn->commit();
            $message = 'User deleted';
        } catch (Exception $e) {
            $conn->rollback();
            $message = 'Delete failed: ' . $e->getMessage();
        }
    }
}

// Fetch lists
$taxpayers = $conn->query("SELECT user_id, Name, Email FROM Taxpayer ORDER BY Name");
$pros = $conn->query("SELECT user_id, Name, Email FROM TaxProfessional ORDER BY Name");
$auths = $conn->query("SELECT user_id, Name, Email FROM TaxAuthority ORDER BY Name");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="Styles\admin.css">
    <script>
        function confirmDelete(uid){ if(confirm('Delete this user?')){ document.getElementById('del-user-id').value=uid; document.getElementById('del-form').submit(); } }
    </script>
</head>
<body>
<header>
    <h1>Admin Panel</h1>
    <div>
        <a class="link" href="admin.php">Home</a>
        <a class="link" href="logout.php">Logout</a>
    </div>
</header>
<?php if ($message) { echo '<p class="msg">'.htmlspecialchars($message).'</p>'; } ?>
<div class="wrap">
    <div class="card">
        <h2>Add Taxpayer</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_taxpayer" />
            <input name="name" placeholder="Name" required />
            <input name="email" placeholder="Email" required />
            <input name="phone" placeholder="Phone" required />
            <input name="address" placeholder="Address" />
            <button type="submit">Add</button>
        </form>
        <h2>Taxpayers</h2>
        <table>
            <tr><th>Name</th><th>Email</th><th>Action</th></tr>
            <?php while($r=$taxpayers->fetch_assoc()){ ?>
            <tr>
                <td><?php echo htmlspecialchars($r['Name']); ?></td>
                <td><?php echo htmlspecialchars($r['Email']); ?></td>
                <td><button onclick="confirmDelete(<?php echo (int)$r['user_id']; ?>)">Delete</button></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <div class="card">
        <h2>Add Professional</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_professional" />
            <input name="name" placeholder="Name" required />
            <input name="email" placeholder="Email" required />
            <input name="phone" placeholder="Phone" required />
            <button type="submit">Add</button>
        </form>
        <h2>Professionals</h2>
        <table>
            <tr><th>Name</th><th>Email</th><th>Action</th></tr>
            <?php while($r=$pros->fetch_assoc()){ ?>
            <tr>
                <td><?php echo htmlspecialchars($r['Name']); ?></td>
                <td><?php echo htmlspecialchars($r['Email']); ?></td>
                <td><button onclick="confirmDelete(<?php echo (int)$r['user_id']; ?>)">Delete</button></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <div class="card">
        <h2>Add Authority</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_authority" />
            <input name="name" placeholder="Name" required />
            <input name="email" placeholder="Email" required />
            <input name="phone" placeholder="Phone" required />
            <button type="submit">Add</button>
        </form>
        <h2>Authorities</h2>
        <table>
            <tr><th>Name</th><th>Email</th><th>Action</th></tr>
            <?php while($r=$auths->fetch_assoc()){ ?>
            <tr>
                <td><?php echo htmlspecialchars($r['Name']); ?></td>
                <td><?php echo htmlspecialchars($r['Email']); ?></td>
                <td><button onclick="confirmDelete(<?php echo (int)$r['user_id']; ?>)">Delete</button></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>
<form id="del-form" method="POST" style="display:none;">
    <input type="hidden" name="action" value="delete_user" />
    <input type="hidden" id="del-user-id" name="user_id" value="" />
    </form>
</body>
</html>

<?php
include("footer.php");
?>
