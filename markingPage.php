<?php
session_start();
$conn = new mysqli("localhost", "root", "", "attendance_system");

$lecturerId = $_SESSION['USER_ID'] ?? null;
$picPath = "default-pfp.png";
$lecturerName = "Lecturer";

if ($lecturerId) {
    $sql = "SELECT LECT_PICTURE, LECT_NAME FROM LECTURER WHERE LECT_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $lecturerId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['LECT_PICTURE']) && file_exists($row['LECT_PICTURE'])) {
            $picPath = $row['LECT_PICTURE'];
        }
        $lecturerName = $row['LECT_NAME'];
    }
}

if (!isset($_SESSION['DATE']) || !isset($_SESSION['CLASS_CODE']) || !isset($_SESSION['SUBJECT_CODE'])) {
    echo "<script>alert('⚠️ Please submit the attendance form first.'); window.location.href='mark.html';</script>";
    exit();
}

$date        = $_SESSION['DATE'];
$classCode   = $_SESSION['CLASS_CODE'];
$subjectCode = $_SESSION['SUBJECT_CODE'];

$conn = new mysqli("localhost", "root", "", "attendance_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT STUDENT_ID FROM REGISTER_CLASS WHERE CLASS_CODE = ? AND SUBJECT_CODE = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $classCode, $subjectCode);
$stmt->execute();
$students = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mark Attendance</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="top-header">
  <div class="header-content">
    <p>🖥 WELCOME TO ATTENDANCE SYSTEM 🖥</p>
    <div class="user-profile">
	  <img src="<?php echo htmlspecialchars($picPath); ?>" class="profile-photo" onclick="location.href='profile.php'">
	  <span class="user-name"><?php echo htmlspecialchars($lecturerName); ?></span>
	</div>
  </div>
</header>

<div class="container">
  <aside class="sidebar">
    <button onclick="location.href='mainpageLect.php'">🏠 Home</button>

    <div class="dropdown">
      <button class="dropbtn">👨‍🏫 Lecturer</button>
      <div class="dropdown-content">
        <a href="registerLect.php">📑 Register</a>
        <a href="markPage.php">✅ Mark Attendance</a>
        <a href="searchAttLectPage.php">📊 Attendance Record</a>
      </div>
    </div>
  </aside>

  <main class="main-content">
    <section class="record-form">
      <h2 class="record-title">Marking — <?php echo "$classCode / $subjectCode"; ?></h2>
      
	<form method="POST" action="attendLecturer.php">
	  <table>
		<thead>
		  <tr>
			<th>No</th>
			<th>Student ID</th>
			<th>✅ Present</th>
			<th>📝 Approve Leave</th>
			<th>📄 Leave Request</th>
		  </tr>
		</thead>
		<tbody>
		  <?php
		  $count = 1;
		  while ($row = $students->fetch_assoc()) {
			$studentId = $row['STUDENT_ID'];
			echo "
                <tr>
                  <td>$count</td>
                  <td>
                    $studentId
                    <input type='hidden' name='student[]' value='$studentId'>
                  </td>
                  <td><input type='checkbox' name='present[]' value='$studentId'></td>
                  <td><input type='checkbox' name='approved[]' value='$studentId'></td>
                  <td><a href='viewLeave.php?student_id=$studentId' target='_blank'>📄 View</a></td>
                </tr>
              ";
			$count++;
		  }
		  ?>
		 </tbody>
        </table>

        <input type="hidden" name="DATE" value="<?php echo $date; ?>">
        <input type="hidden" name="CLASS_CODE" value="<?php echo $classCode; ?>">
        <input type="hidden" name="SUBJECT_CODE" value="<?php echo $subjectCode; ?>">
        <br>
        <button type="submit">✅ Submit Attendance</button>
      </form>
    </section>
  </main>
</div>
</body>
</html>
