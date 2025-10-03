<?php
// index.php
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

// Get distinct Rasi list from DB
$rasiList = [];
$rRes = $conn->query("SELECT DISTINCT Rasi FROM ChandrashtamaDays2025 ORDER BY FIELD(Rasi, 'Mesha','Rishaba','Mithuna','Kataka','Simha','Kanya','Tula','Vrishchika','Dhanus','Makara','Kumbha','Meena'), Rasi");
if ($rRes) {
    while ($r = $rRes->fetch_assoc()) {
        $rasiList[] = $r['Rasi'];
    }
}

// Retrieve POST values safely
$selRasi = isset($_POST['rasi']) ? $_POST['rasi'] : '';
$selNakshatra = isset($_POST['nakshatra']) ? $_POST['nakshatra'] : '';
$selMonth = isset($_POST['month']) ? intval($_POST['month']) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Chandrashtama Days 2025</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
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
        <div class="card-header bg-primary text-white text-center py-3" style="background:linear-gradient(90deg,#0d6efd,#6610f2);">
            <h4 class="mb-0">Chandrashtama Days Calculator — 2025</h4>
        </div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Select Your Raasi</label>
                    <select class="form-select" id="rasi" name="rasi" required>
                        <option value="">-- Select Raasi --</option>
                        <?php foreach ($rasiList as $r):
                            $sel = ($selRasi === $r) ? 'selected' : '';
                            $tamil = $rasiTamil[$r] ?? $r;
                        ?>
                            <option value="<?php echo htmlspecialchars($r); ?>" <?php echo $sel; ?>>
                                <?php echo htmlspecialchars($r . ' (' . $tamil . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Select Nakshatra</label>
                    <select class="form-select" id="nakshatra" name="nakshatra" required disabled>
                        <option value="">-- Select Nakshatra --</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Select Month</label>
                    <select class="form-select" name="month" required>
                        <?php for ($m=1;$m<=12;$m++):
                            $monthName = date("F", mktime(0,0,0,$m,1));
                            $s = ($selMonth === $m) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $m; ?>" <?php echo $s; ?>>
                                <?php echo $monthName . " 2025"; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-12 text-center mt-2">
                    <button type="submit" class="btn btn-sm btn-success btn-lg px-4">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div id="results">
    <?php
    if ($selRasi !== '' && $selNakshatra !== '' && isset($_POST['month'])) {
        $sql = "SELECT Pada, Start_Date, Start_Time, End_Date, End_Time
                FROM ChandrashtamaDays2025
                WHERE Rasi = ? AND Nakshatra = ? AND MONTH(Start_Date) = ?
                ORDER BY Start_Date, Start_Time";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $selRasi, $selNakshatra, $selMonth);
        $stmt->execute();
        $res = $stmt->get_result();

        $displayRasi = $rasiTamil[$selRasi] ?? $selRasi;
        $displayNak = $nakshatraTamil[$selNakshatra] ?? $selNakshatra;

        echo '<div class="card shadow-sm">';
        echo '<div class="card-body">';
        echo '<h5 class="text-center text-primary mb-3">
                Chandrashtama Days for <span class="fw-bold">'.$selRasi.' ('.$displayRasi.') - '.$selNakshatra.' ('.$displayNak.')</span>
                ('.date("F", mktime(0,0,0,$selMonth,1)).' 2025)
              </h5>';

        if ($res->num_rows > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered table-hover text-center align-middle">';
            echo '<thead class="table-dark"><tr><th>Pada</th><th>Start Date</th><th>Start Time</th><th>End Date</th><th>End Time</th></tr></thead><tbody>';
            while ($row = $res->fetch_assoc()) {
                $p = htmlspecialchars($row['Pada']);
                $sd = date("d-m-Y", strtotime($row['Start_Date']));
                $st = date("h:i A", strtotime($row['Start_Time']));
                $ed = date("d-m-Y", strtotime($row['End_Date']));
                $et = date("h:i A", strtotime($row['End_Time']));
                echo "<tr>
                        <td class='fw-semibold'>{$p}</td>
                        <td>{$sd}</td>
                        <td>{$st}</td>
                        <td>{$ed}</td>
                        <td>{$et}</td>
                      </tr>";
            }
            echo '</tbody></table></div>';
        } else {
            echo '<div class="alert alert-warning text-center mb-0">No Chandrashtama days found for this selection.</div>';
        }

        echo '</div></div>';
        $stmt->close();
    }
    ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){
    const rasiSelect = document.getElementById('rasi');
    const nakSelect = document.getElementById('nakshatra');

    const preSelectedRasi = "<?php echo addslashes($selRasi); ?>";
    const preSelectedNak = "<?php echo addslashes($selNakshatra); ?>";

    const nakshatraTamil = <?php echo json_encode($nakshatraTamil); ?>;

    function loadNakshatras(rasi, restoreSelected = '') {
        nakSelect.innerHTML = '<option value="">Loading...</option>';
        nakSelect.disabled = true;

        if (!rasi) {
            nakSelect.innerHTML = '<option value="">-- Select Nakshatra --</option>';
            nakSelect.disabled = true;
            return;
        }

        fetch('get_nakshatra.php?rasi=' + encodeURIComponent(rasi))
            .then(resp => resp.json())
            .then(data => {
                nakSelect.innerHTML = '<option value="">-- Select Nakshatra --</option>';
                if (Array.isArray(data) && data.length) {
                    data.forEach(n => {
                        const opt = document.createElement('option');
                        opt.value = n;
                        opt.textContent = n + ' (' + (nakshatraTamil[n] || n) + ')';
                        if (restoreSelected && restoreSelected === n) opt.selected = true;
                        nakSelect.appendChild(opt);
                    });
                    nakSelect.disabled = false;
                } else {
                    nakSelect.innerHTML = '<option value="">No nakshatras found</option>';
                    nakSelect.disabled = true;
                }
            })
            .catch(err => {
                console.error(err);
                nakSelect.innerHTML = '<option value="">Error loading</option>';
                nakSelect.disabled = true;
            });
    }

    rasiSelect.addEventListener('change', function(){
        loadNakshatras(this.value, null);
    });

    window.addEventListener('load', function(){
        if (preSelectedRasi) {
            rasiSelect.value = preSelectedRasi;
            loadNakshatras(preSelectedRasi, preSelectedNak);
        }
    });
})();
</script>
</body>
</html>
