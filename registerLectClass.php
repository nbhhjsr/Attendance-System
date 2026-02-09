<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST values
$classCode    = $_POST['CLASS_CODE'];
$subjectCode  = $_POST['SUBJECT_CODE'];
$subjectName  = $_POST['SUBJECT_NAME'];
$lectId       = $_POST['LECT_ID'];
$studentId    = $_POST['STUDENT_ID'];

// ❌ Check 1: student already registered in this subject under the same class
$checkSql = "SELECT * FROM REGISTER_CLASS WHERE CLASS_CODE = ? AND SUBJECT_CODE = ? AND LECT_ID = ? AND STUDENT_ID = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("ssss", $classCode, $subjectCode, $lectId, $studentId);
$stmt->execute();
$result1 = $stmt->get_result();

if ($result1->num_rows > 0) {
    echo "<script>alert('This Student already registered in this Subject Code/Class Code. 🤗'); window.location.href='registerLect.html';</script>";
    exit();
}

// ❌ Check 2: student already registered in the same subject (in any class)
$checkSubjectSql = "SELECT * FROM REGISTER_CLASS WHERE STUDENT_ID = ? AND SUBJECT_CODE = ?";
$stmt2 = $conn->prepare($checkSubjectSql);
$stmt2->bind_param("ss", $studentId, $subjectCode);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows > 0) {
    echo "<script>alert('This Student is already registered for this Subject Code in another class. 🚫'); window.location.href='registerLect.html';</script>";
    exit();
}

// ❌ Check 3: If class code & subject code exist, LECT_ID must be same (enforce same lecturer)
$lecturerCheckSql = "SELECT LECT_ID FROM REGISTER_CLASS WHERE CLASS_CODE = ? AND SUBJECT_CODE = ?";
$stmt3 = $conn->prepare($lecturerCheckSql);
$stmt3->bind_param("ss", $classCode, $subjectCode);
$stmt3->execute();
$result3 = $stmt3->get_result();

if ($result3->num_rows > 0) {
    $row = $result3->fetch_assoc();
    $existingLectId = $row['LECT_ID'];

    if ($existingLectId !== $lectId) {
        echo "<script>alert('This CLASS CODE & SUBJECT CODE is already assigned to another Lecturer. Please use the same LECT_ID. ❌'); window.location.href='registerLect.html';</script>";
        exit();
    }
}

// ✅ Insert data
$insertSql = "INSERT INTO REGISTER_CLASS (CLASS_CODE, SUBJECT_CODE, SUBJECT_NAME, LECT_ID, STUDENT_ID)
              VALUES (?, ?, ?, ?, ?)";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("sssss", $classCode, $subjectCode, $subjectName, $lectId, $studentId);

if ($insertStmt->execute()) {
    echo "<script>alert('Class registered successfully! 🙂‍↕️'); window.location.href='registerLect.html';</script>";
} else {
    echo "Error: " . $insertStmt->error;
}

$conn->close();
?>