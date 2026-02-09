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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Main Page - Attendance System</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Header -->
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
    <!-- Sidebar -->
    <aside class="sidebar">
      <button onclick="location.href='mainpageLect.php'">🏠 Home</button>

      <!-- Lecturer Dropdown Box -->
      <div class="dropdown-box">
        <div class="dropdown">
          <button class="dropbtn">👨‍🏫 Lecturer</button>
          <div class="dropdown-content">
            <a href="registerLect.php">📑 Register</a>
            <a href="markPage.php">✅ Mark Attendance</a>
            <a href="searchAttLectPage.php">📊 Attendance Record</a>
          </div>
        </div>
      </div>

    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <section class="homepage">
        <h1>ATTENDANCE SYSTEM</h1>
      </section>
    </main>
  </div>
</body>
</html>
