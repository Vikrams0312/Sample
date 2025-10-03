<?php
include 'db.php';

// Rasi and Nakshatra mapping (English => Tamil)
$rasiTamil = [
    'Mesha'=>'மேஷம்','Rishaba'=>'ரிஷபம்','Mithuna'=>'மிதுனம்',
    'Kataka'=>'கடகம்','Simha'=>'சிம்மம்','Kanya'=>'கன்னி',
    'Tula'=>'துலாம்','Vrishchika'=>'விருச்சிகம்','Dhanus'=>'தனுசு',
    'Makara'=>'மகரம்','Kumbha'=>'கும்பம்','Meena'=>'மீனம்'
];

$nakshatraTamil = [
    'Ashwini'=>'அசுவினி','Bharani'=>'பரணி','Karthikai'=>'கார்த்திகை','Rohini'=>'ரோகிணி',
    'Mrigasira'=>'மிருகசிரம்','Thiruvathirai'=>'திருவாதிரை','Punarpoosam'=>'புனர்பூசம்','Pusam'=>'பூசம்',
    'Ayilyam'=>'ஆயில்யம்','Makam'=>'மகம்','Puram'=>'புரம்','Uthram'=>'உத்திரம்',
    'Hastham'=>'ஹஸ்தம்','Chitra'=>'சித்திரை','Swati'=>'சுவாதி','Visakam'=>'விசாகம்',
    'Anusham'=>'அனுஷம்','Kettai'=>'கேட்டை','Moolam'=>'மூலம்','Pooradam'=>'பூராடம்',
    'Uthradam'=>'உத்திராடம்','Thiruvonam'=>'திருவோணம்','Avittam'=>'அவிட்டம்','Sadayam'=>'சடயம்',
    'Poorattathi'=>'பூரட்டாதி','Uthirattathi'=>'உத்திரட்டாதி','Revati'=>'ரேவதி'
];

// Get date from POST
$selDate = isset($_POST['date']) ? $_POST['date'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chandrashtama Search by Date</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<style>
body { background: #f6f9fc; }
.card { border-radius: 12px; margin-bottom: 20px; }
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
    <div class="card shadow-sm">
        <div class="card-body">
        <div class="card-header bg-primary text-white text-center py-3" style="background:linear-gradient(90deg,#0d6efd,#6610f2);">
            <h4 class="mb-0">Chandrashtama Days Calculator — 2025</h4>
        </div>
            <form method="POST" class="row g-3 justify-content-center">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Select Date</label>
                    <input type="text" id="displayDate" class="form-control" placeholder="Click here to select a date">
                    <!-- Hidden field to send correct format to PHP -->
                    <input type="hidden" name="date" id="submitDate" value="<?php echo htmlspecialchars($selDate); ?>">
                </div>

                <div class="col-md-2 align-self-end">
                    <button type="submit" class="btn btn-sm btn-success w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    if ($selDate) {
        // Query for all Rasi-Nakshatra for selected date
        $sql = "SELECT Rasi, Nakshatra, Pada, Start_Date, Start_Time, End_Date, End_Time 
            FROM ChandrashtamaDays2025
            WHERE DATE(Start_Date) = ?
            ORDER BY Rasi, Nakshatra, Start_Date, Start_Time";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $selDate);
        $stmt->execute();
        $res = $stmt->get_result();

        $data = [];
        while ($row = $res->fetch_assoc()) {
            $key = $row['Rasi'].'-'.$row['Nakshatra'];
            $data[$key][] = $row;
        }

        if ($data) {
            foreach ($data as $rasiNak => $rows) {
                list($rasi, $nakshatra) = explode('-', $rasiNak);
                $rTamil = $rasiTamil[$rasi] ?? $rasi;
                $nTamil = $nakshatraTamil[$nakshatra] ?? $nakshatra;

                echo '<div class="card shadow-sm">';
                echo '<div class="card-body">';
                echo '<h5 class="text-primary mb-3">'.$rasi.' ('.$rTamil.') - '.$nakshatra.' ('.$nTamil.')</h5>';
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered table-hover text-center align-middle">';
                echo '<thead class="table-dark"><tr><th>Pada</th><th>Start Date</th><th>Start Time</th><th>End Date</th><th>End Time</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    echo '<tr>
                            <td>'.$row['Pada'].'</td>
                            <td>'.date("d-m-Y", strtotime($row['Start_Date'])).'</td>
                            <td>'.date("h:i A", strtotime($row['Start_Time'])).'</td>
                            <td>'.date("d-m-Y", strtotime($row['End_Date'])).'</td>
                            <td>'.date("h:i A", strtotime($row['End_Time'])).'</td>
                          </tr>';
                }
                echo '</tbody></table></div></div></div>';
            }
        } else {
            echo '<div class="alert alert-warning text-center mt-3">No Chandrashtama found for '.$selDate.'</div>';
        }

        $stmt->close();
    }
    ?>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
$(document).ready(function() {
    var today = new Date();
    var oneYearAgo = new Date();
    oneYearAgo.setFullYear(today.getFullYear() - 1);
    var oneYearLater = new Date();
    oneYearLater.setFullYear(today.getFullYear() + 1);

    // Initialize datepicker
    $("#displayDate").datepicker({
        dateFormat: 'dd-mm-yy', // Display format
        altField: "#submitDate", // Hidden field to submit in yyyy-mm-dd format
        altFormat: "yy-mm-dd",   // Format submitted to PHP
        minDate: oneYearAgo,
        maxDate: oneYearLater,
        changeMonth: true,
        changeYear: true
    });

    // Form validation
    $("form").submit(function(e) {
        var selectedDate = $("#displayDate").val();
        if (!selectedDate) {
            alert("Please select a date");
            e.preventDefault(); // Stop form submission
        }
    });
});
</script>

</body>
</html>
