<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'];
    $revenue = $_POST['revenue'];

    // New Tax Regime Slabs (2023–24)
    $tax = 0;
    
    if ($revenue <= 300000) {
        $tax = 0;
    } elseif ($revenue <= 600000) {
        $tax = ($revenue - 300000) * 0.05;
    } elseif ($revenue <= 900000) {
        $tax = 15000 + ($revenue - 600000) * 0.10;
    } elseif ($revenue <= 1200000) {
        $tax = 45000 + ($revenue - 900000) * 0.15;
    } elseif ($revenue <= 1500000) {
        $tax = 90000 + ($revenue - 1200000) * 0.20;
    } else {
        $tax = 150000 + ($revenue - 1500000) * 0.30;
    }

    // Add 4% Health & Education Cess
    $cess = $tax * 0.04;
    $total_tax = $tax + $cess;

    // Redirect with result
    header("Location: taxprofessional.php?calculated_tax=" . urlencode($total_tax) . "&client_id=" . urlencode($client_id));
    exit();
}
?>
<?php include("footer.php"); ?>
