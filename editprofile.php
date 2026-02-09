<?php
session_start();

$userRole   = $_SESSION['USER_ROLE'] ?? '';
$userId     = $_SESSION['USER_ID'] ?? '';
$email      = $_SESSION['REG_EMAIL'] ?? '';
$fullname   = $_SESSION['USER_FULLNAME'] ?? '';
$profilePic = $_SESSION['PROFILE_PIC'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="top-header">
    <p>🖥 EDIT YOUR PROFILE 🖥</p>
  </header>

  <main class="profile-edit-form">
    <form action="profile.php" method="POST" enctype="multipart/form-data">
      <label for="USER_ROLE">User Role:</label>
      <select name="USER_ROLE" id="USER_ROLE" readonly onmousedown="return false;">
        <option value="Student" <?= $userRole === 'Student' ? 'selected' : '' ?>>Student</option>
        <option value="Lecturer" <?= $userRole === 'Lecturer' ? 'selected' : '' ?>>Lecturer</option>
      </select><br><br>

      <div class="pfp-upload">
        <label for="pfp">Profile Picture</label><br>
        <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile Picture" width="100"><br>
        <input type="file" id="pfp" name="pfp" accept="image/*">
      </div>

      <input type="email" placeholder="Email" name="REG_EMAIL" required value="<?= htmlspecialchars($email) ?>">
      <input type="text" placeholder="Student/Lecturer ID" name="USER_ID" required
             value="<?= htmlspecialchars($userId) ?>">
      <input type="text" placeholder="Full Name" name="USER_FULLNAME" required value="<?= htmlspecialchars($fullname) ?>">

      <button type="submit">Save Changes</button>
    </form>
  </main>
</body>
</html>
