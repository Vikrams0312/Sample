<?php
// search_rasi_month.php
include 'db.php';

// Rasi mapping (English => Tamil)
$rasiTamil = [
    'Mesha' => 'மேஷம்', 'Rishaba' => 'ரிஷபம்', 'Mithuna' => 'மிதுனம்',
    'Kataka' => 'கடகம்', 'Simha' => 'சிம்மம்', 'Kanya' => 'கன்னி',
    'Tula' => 'துலாம்', 'Vrishchika' => 'விருச்சிகம்', 'Dhanus' => 'தனுசு',
    'Makara' => 'மகரம்', 'Kumbha' => 'கும்பம்', 'Meena' => 'மீனம்'
];

// Nakshatra mapping (English => Tamil)
$nakshatraTamil = [
    'Ashwini'=>'அசுவினி','Bharani'=>'பரணி','Karthikai'=>'கார்த்திகை','Rohini'=>'ரோகிணி',
    'Mrigasira'=>'மிருகசிரம்','Thiruvathirai'=>'திருவாதிரை','Punarpoosam'=>'புனர்பூசம்','Pusam'=>'பூசம்',
    'Ayilyam'=>'ஆயில்யம்','Makam'=>'மகம்','Puram'=>'புரம்','Uthram'=>'உத்திரம்',
    'Hastham'=>'ஹஸ்தம்','Chitra'=>'சித்திரை','Swati'=>'சுவாதி','Visakam'=>'விசாகம்',
    'Anusham'=>'அனுஷம்','Kettai'=>'கேட்டை','Moolam'=>'மூலம்','Pooradam'=>'பூராடம்',
    'Uthradam'=>'உத்திராடம்','Thiruvonam'=>'திருவோணம்','Avittam'=>'அவிட்டம்','Sadayam'=>'சடயம்',
    'Poorattathi'=>'பூரட்டாதி','Uthirattathi'=>'உத்திரட்டாதி','Revati'=>'ரேவதி'
];

// Get distinct Rasi list
$rasiList = [];
$rRes = $conn->query("SELECT DISTINCT Rasi FROM ChandrashtamaDays2025 ORDER BY FIELD(Rasi, 'Mesha','Rishaba','Mithuna','Kataka','Simha','Kanya','Tula','Vrishchika','Dhanus','Makara','Kumbha','Meena')");
while ($r = $rRes->fetch_assoc()) {
    $rasiList[] = $r['Rasi'];
}

// Handle POST
$selRasi = isset($_POST['rasi']) ? $_POST['rasi'] : '';

// Determine current month based on today
$today = date('Y-m-d');
$currentMonth = date('m', strtotime($today));
$currentYear = date('Y', strtotime($today));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chandrashtama Days - Rasi Search</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f6f9fc; }
.card { border-radius: 12px; }
</style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Chandrashtama Days 2025</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='index.php') echo 'active'; ?>" href="index.php">
            Raasi & Nakshatra Search
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='search_by_date.php') echo 'active'; ?>" href="search_by_date.php">
            Search by Date
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='search_rasi_month.php') echo 'active'; ?>" href="search_rasi_month.php">
            Raasi Current Month
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white text-center py-3">
            <h4 class="mb-0">Chandrashtama Days In This Month</h4>
        </div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Select Your Raasi</label>
                    <select class="form-select" name="rasi" required>
                        <option value="">-- Select Raasi --</option>
                        <?php foreach ($rasiList as $r): 
                            $sel = ($selRasi === $r) ? 'selected' : '';
                            $tamil = $rasiTamil[$r] ?? $r;
                        ?>
                            <option value="<?php echo htmlspecialchars($r); ?>" <?php echo $sel; ?>>
                                <?php echo htmlspecialchars($r.' ('.$tamil.')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-success btn-lg px-4">Search</button>
                </div>
            </form>
        </div>
    </div>

<?php
if ($selRasi !== '') {
    // Fetch all Nakshatra entries for this Rasi in current month
    $sql = "SELECT Nakshatra, Pada, Start_Date, Start_Time, End_Date, End_Time
            FROM ChandrashtamaDays2025
            WHERE Rasi = ? AND MONTH(Start_Date) = ? AND YEAR(Start_Date) = ?
            ORDER BY Nakshatra, Start_Date, Start_Time";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $selRasi, $currentMonth, $currentYear);
    $stmt->execute();
    $res = $stmt->get_result();

    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[$row['Nakshatra']][] = $row;
    }

    $displayRasi = $rasiTamil[$selRasi] ?? $selRasi;

    foreach ($data as $nak => $entries):
        $displayNak = $nakshatraTamil[$nak] ?? $nak;
        echo '<div class="card shadow-sm mb-3">';
        echo '<div class="card-body">';
        echo '<h5 class="text-primary mb-3">'.$selRasi.' ('.$displayRasi.') - '.$nak.' ('.$displayNak.')</h5>';
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered table-hover text-center align-middle">';
        echo '<thead class="table-dark"><tr><th>Pada</th><th>Start Date</th><th>Start Time</th><th>End Date</th><th>End Time</th></tr></thead><tbody>';
        foreach ($entries as $row) {
            echo '<tr>
                    <td>'.$row['Pada'].'</td>
                    <td>'.date("d-m-Y", strtotime($row['Start_Date'])).'</td>
                    <td>'.date("h:i A", strtotime($row['Start_Time'])).'</td>
                    <td>'.date("d-m-Y", strtotime($row['End_Date'])).'</td>
                    <td>'.date("h:i A", strtotime($row['End_Time'])).'</td>
                  </tr>';
        }
        echo '</tbody></table></div></div></div>';
    endforeach;

    if (empty($data)) {
        echo '<div class="alert alert-warning text-center">No Chandrashtama days found for '.$selRasi.' in this month.</div>';
    }

    $stmt->close();
}
?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

