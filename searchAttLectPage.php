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
  <title>Search Attendance - Attendance System</title>
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

      <div class="dropdown">
        <button class="dropbtn">👨‍🏫 Lecturer</button>
        <div class="dropdown-content">
          <a href="registerLect.php">📑 Register</a>
          <a href="markPage.php">✅ Mark Attendance</a>
          <a href="searchAttLectPage.php">📊 Attendance Record</a>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <section class="class-info-form">
        <h2>SEARCH ATTENDANCE</h2>
        <form method="POST" action="searchAttLect.php">
          <label for="Date">Date</label>
          <input type="date" name="DATE" required>

          <label for="Subject Code">Subject</label>
          <input type="text" name="SUBJECT_CODE" placeholder="e.g. CSC264" required>

          <label for="Class Code">Class</label>
          <input type="text" name="CLASS_CODE" placeholder="e.g. JCDCS1104C" required>

          <button type="submit">Search</button>
        </form>
      </section>
    </main>
  </div>
</body>
</html>