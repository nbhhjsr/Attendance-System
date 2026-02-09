<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get form data
$studentId   = $_POST['STUDENT_ID'];
$classCode   = $_POST['CLASS_CODE'];
$subjectCode = $_POST['SUBJECT_CODE'];
$date        = $_POST['DATE'];
$decision    = $_POST['decision'];

// Save decision in new column
$update = $conn->prepare("UPDATE ATTENDANCE SET MARK_STATUS = ? WHERE STUDENT_ID = ? AND CLASS_CODE = ? AND SUBJECT_CODE = ? AND DATE = ?");
$update->bind_param("sssss", $decision, $studentId, $classCode, $subjectCode, $date);
$update->execute();

echo "<script>alert('Decision saved successfully!'); window.close();</script>";
exit();
?>
