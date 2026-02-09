<?php
require_once('tcpdf/tcpdf/tcpdf.php'); // Adjust path if needed

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = null;
$subjectCode = '';
$classCode = '';
$date = '';
$subjectName = '';
$className = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST['DATE'];
    $subjectCode = $_POST['SUBJECT_CODE'];
    $classCode = $_POST['CLASS_CODE'];
    $likeDate = $date . "%";

    // Since there's no class name or subject name, we use the codes
    $subjectName = $subjectCode;
    $className = $classCode;

    // Get Attendance Data
    $stmt = $conn->prepare("SELECT * FROM ATTENDANCE WHERE DATE LIKE ? AND SUBJECT_CODE = ? AND CLASS_CODE = ? ORDER BY DATE DESC");
    $stmt->bind_param("sss", $likeDate, $subjectCode, $classCode);
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate PDF if requested
    if (isset($_POST['download_pdf'])) {
        $pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Attendance System');
        $pdf->SetTitle('Attendance Report');
        $pdf->SetHeaderData('', 0, 'Attendance Report', "Subject: $subjectCode\nClass: $classCode\nDate: $date");
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(TRUE, 25);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();

        $html = '
        <h3 style="text-align:center;">Attendance List</h3>
        <table border="1" cellpadding="5">
          <thead>
            <tr style="background-color:#f2f2f2;">
              <th><b>Student ID</b></th>
              <th><b>Class Code</b></th>
              <th><b>Subject Code</b></th>
              <th><b>Date & Time</b></th>
              <th><b>Status</b></th>
            </tr>
          </thead>
          <tbody>';
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $html .= '<tr>
                    <td>' . $row['STUDENT_ID'] . '</td>
                    <td>' . $row['CLASS_CODE'] . '</td>
                    <td>' . $row['SUBJECT_CODE'] . '</td>
                    <td>' . $row['DATE'] . '</td>
                    <td>' . $row['MARK_STATUS'] . '</td>
                  </tr>';
            }
        } else {
            $html .= '<tr><td colspan="5">No records found.</td></tr>';
        }

        $html .= '</tbody></table>';
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Attendance_Report.pdf', 'D');
        exit;
    }
}
?>

<!-- HTML Display -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Attendance</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header class="top-header">
    <div class="header-content">
      <p>📊 Attendance Results</p>
    </div>
  </header>

  <div class="container">
    <main class="main-content">
      <?php if ($result !== null && $result->num_rows > 0): ?>
        <div class="record-form">
			<h2 class="record-title" style="font-size: 20px; margin-bottom: 25px;">
			  📘 Subject: <?= htmlspecialchars($subjectCode) ?><br>
			  🏫 Class: <?= htmlspecialchars($classCode) ?><br>
			  📅 Date: <?= htmlspecialchars($date) ?>
			</h2>
          <table>
            <thead>
              <tr>
                <th>Student ID</th>
                <th>Class Code</th>
                <th>Subject Code</th>
                <th>Date & Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['STUDENT_ID']) ?></td>
                  <td><?= htmlspecialchars($row['CLASS_CODE']) ?></td>
                  <td><?= htmlspecialchars($row['SUBJECT_CODE']) ?></td>
                  <td><?= htmlspecialchars($row['DATE']) ?></td>
                  <td>
                    <?php
                      $status = $row['MARK_STATUS'];
                      if ($status === "Present") echo "✅ Present";
                      elseif ($status === "Absent with Permission") echo "📝 Absent with Permission";
                      else echo "❌ Absent";
                    ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>

          <form method="post" action="searchAttLect.php" style="text-align: center; margin-top: 20px;">
            <input type="hidden" name="DATE" value="<?= htmlspecialchars($date) ?>">
            <input type="hidden" name="SUBJECT_CODE" value="<?= htmlspecialchars($subjectCode) ?>">
            <input type="hidden" name="CLASS_CODE" value="<?= htmlspecialchars($classCode) ?>">
            <input type="hidden" name="download_pdf" value="1">
            <button type="submit" class="btn pdf-btn">📥 Download PDF</button>
          </form>
        </div>
      <?php elseif ($result !== null): ?>
        <div class="record-form">
          <h2 class="record-title">📊 Attendance Not Found</h2>
          <p style="text-align:center;">No attendance records found for the selected filters.</p>
        </div>
      <?php endif; ?>
	  
	 <div style="text-align: center; margin: 20px 0;">
	  <button onclick="history.back()" class="btn">🔙 Go Back</button>
	</div>

    </main>
  </div>
</body>
</html>


<?php $conn->close(); ?>
