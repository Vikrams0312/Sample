<?php
// get_nakshatra.php
header('Content-Type: application/json; charset=utf-8');
include 'db.php';

if (!isset($_GET['rasi']) || trim($_GET['rasi']) === '') {
    echo json_encode([]);
    exit;
}

$rasi = $_GET['rasi'];
// Use prepared statement to be safe
$sql = "SELECT DISTINCT Nakshatra FROM ChandrashtamaDays2025 WHERE Rasi = ? ORDER BY Nakshatra";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $rasi);
$stmt->execute();
$res = $stmt->get_result();

$nakshatras = [];
while ($row = $res->fetch_assoc()) {
    $nakshatras[] = $row['Nakshatra'];
}

echo json_encode($nakshatras);
exit;
?>
