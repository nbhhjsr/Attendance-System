<?php
session_start();
$studentId   = $_GET['student_id'];
$classCode   = $_SESSION['CLASS_CODE'];
$subjectCode = $_SESSION['SUBJECT_CODE'];
$date        = $_SESSION['DATE'];

// Path to leave file
$filename    = "leave_" . $studentId . "_" . $subjectCode . ".pdf";
$leaveFile   = __DIR__ . "/leave_files/" . $filename;

if (file_exists($leaveFile)) {
    $publicPath  = "leave_files/" . $filename;
    echo "<iframe src='$publicPath'></iframe>";
} else {
    echo "<p><strong>No leave file found at $leaveFile.</strong></p>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Leave Request - <?php echo $studentId; ?></title>
  <style>
    body { font-family: sans-serif; padding: 20px; }
    iframe { width: 100%; height: 600px; border: 1px solid #ccc; }
    form { margin-top: 20px; }
    button { padding: 10px 20px; margin-top: 10px; }
  </style>
</head>
<body>
  <h2>Leave Request: <?= htmlspecialchars($studentId) ?></h2>

	<?php if (file_exists($leaveFile)): ?>
	  <iframe src="<?= htmlspecialchars($publicPath) ?>"></iframe>
	<?php else: ?>
	  <p><strong>No leave file found for <?= htmlspecialchars($filename) ?>.</strong></p>
	<?php endif; ?>

  <form method="POST" action="processLeave.php">
    <input type="hidden" name="STUDENT_ID" value="<?php echo $studentId; ?>">
    <input type="hidden" name="CLASS_CODE" value="<?php echo $classCode; ?>">
    <input type="hidden" name="SUBJECT_CODE" value="<?php echo $subjectCode; ?>">
    <input type="hidden" name="DATE" value="<?php echo $date; ?>">

    <label><input type="radio" name="decision" value="Approved" required> ✅ Approve</label><br>
    <label><input type="radio" name="decision" value="Rejected" required> ❌ Reject</label><br>
    <button type="submit">Submit Decision</button>
  </form>
</body>
</html>
