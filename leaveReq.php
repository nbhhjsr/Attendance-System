<?php
session_start();

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$leaveID = $_POST['LEAVE_ID'] ?? '';
$studentID = $_POST['STUDENT_ID'] ?? '';
$fileName = $_FILES['FILE_ATTACH']['name'];
$tmpName = $_FILES['leaveFile']['tmp_name'];
$uploadDir = "uploads/";
$targetFile = $uploadDir . basename($fileName);

// Check for empty fields
if (empty($leaveID) || empty($studentID) || empty($fileName)) {
    die("<script>alert('❌ All fields are required.'); history.back();</script>");
}

// Create uploads folder if not exists
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Move uploaded file
if (move_uploaded_file($tmpName, $targetFile)) {
    // Insert into leave_request table
    $stmt = $conn->prepare("INSERT INTO LEAVE_REQUEST (LEAVE_ID, STUDENT_ID, FILE ATTACH) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $leaveID, $studentID, $targetFile);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Leave request submitted successfully.'); window.location.href='record.html';</script>";
    } else {
        echo "<script>alert('❌ Error: " . $stmt->error . "'); history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('❌ Failed to upload document.'); history.back();</script>";
}

$conn->close();
?>