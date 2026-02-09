<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userRole = $_POST['USER_ROLE'] ?? '';
    $userId = $_POST['USER_ID'] ?? '';
    $regEmail = $_POST['USER_EMAIL'] ?? '';
    $regPass = $_POST['USER_PASS'] ?? '';

    // Validate required fields
    if (empty($userRole) || empty($userId) || empty($regEmail) || empty($regPass)) {
        die("❌ Please fill in all required fields.");
    }

    // Hash password
    //$hashedPass = password_hash($regPass, PASSWORD_DEFAULT);
	
	 // Insert into register table
    $insertReg = $conn->prepare("INSERT INTO REGISTER (USER_ROLE, USER_ID, REG_EMAIL, REG_PASS) VALUES (?, ?, ?, ?)");
    $insertReg->bind_param("ssss", $userRole, $userId, $regEmail, $regPass);
	
	if (!$insertReg->execute()) {
        die("❌ Registration failed (register table): " . $insertReg->error);
    }
    $insertReg->close();

    // Insert into role-specific table
    if ($userRole === 'Lecturer') {
        $insertLect = $conn->prepare("INSERT INTO LECTURER (LECT_ID, LECT_EMAIL, LECT_PASS) VALUES (?, ?, ?)");
        $insertLect->bind_param("sss", $userId, $regEmail, $regPass);
		if (!$insertLect->execute()) {
        die("❌ Lecturer insert failed: " . $insertLect->error);
    }
    $insertLect->close();
	} 
	elseif ($userRole === 'Student') {
        $insertStud = $conn->prepare("INSERT INTO STUDENT (STUDENT_ID, STUDENT_EMAIL, STUDENT_PASS) VALUES (?, ?, ?)");
        $insertStud->bind_param("sss", $userId, $regEmail, $regPass);
        if (!$insertStud->execute()) {
            die("❌ Student insert failed: " . $insertStud->error);
        }
        $insertStud->close();

    }
}
	// ✅ All successful, now show alert
    echo "<script>alert('✅ Registration successful!'); window.location.href='login.html';</script>";

$conn->close();
?>