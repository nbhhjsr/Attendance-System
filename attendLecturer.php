<?php
session_start();

$conn = new mysqli("localhost", "root", "", "attendance_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$date        = $_POST['DATE'];
$classCode   = $_POST['CLASS_CODE'];
$subjectCode = $_POST['SUBJECT_CODE'];
$students    = $_POST['student'] ?? [];
$present     = $_POST['present'] ?? [];
$approved    = $_POST['approved'] ?? [];

foreach ($students as $studentId) {
    if (in_array($studentId, $present)) {
        $status = 'Present';
    } elseif (in_array($studentId, $approved)) {
        $status = 'Absent with Permission';
    } else {
        $status = 'Absent';
    }

    $sql = "INSERT INTO ATTENDANCE (STUDENT_ID, CLASS_CODE, SUBJECT_CODE, DATE, MARK_STATUS) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $studentId, $classCode, $subjectCode, $date, $status);
    $stmt->execute();
}

echo "<script>alert('✅ Attendance saved successfully!'); window.location.href='mark.html';</script>";
exit();
?>


// ✅ Display page snippet (e.g. attendance table)
<?php
$status = $row['MARK_STATUS'];
$decision = $row['LEAVE_DECISION'] ?? '';

if ($status === "Present") {
    echo "<td>✅ Present</td>";
} elseif ($status === "Absent with Permission") {
    echo "<td>📝 Absent with Permission";
    if ($decision === "Approved") echo " (Approved)";
    elseif ($decision === "Rejected") echo " (Rejected)";
    echo "</td>";
} else {
    echo "<td>❌ Absent</td>";
}
?>