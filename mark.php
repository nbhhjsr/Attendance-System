<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$date        = $_POST['DATE'];         // Make sure HTML input name is 'DATE'
$classCode   = $_POST['CLASS_CODE'];
$subjectCode = $_POST['SUBJECT_CODE'];

$_SESSION['DATE'] = $date;
$_SESSION['CLASS_CODE'] = $classCode;
$_SESSION['SUBJECT_CODE'] = $subjectCode;

header("Location: markingPage.php");
exit();
?>
