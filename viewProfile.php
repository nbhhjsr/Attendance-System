<?php
session_start();

$role = strtolower($_GET['USER_ROLE'] ?? '');
$id   = $_GET['USER_ID'] ?? '';

if (!$role || !$id) {
    echo "<h3>❌ Missing role or user ID in URL.</h3>";
    exit;
}

$conn = new mysqli("localhost", "root", "", "attendance_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table     = $role === 'student' ? 'STUDENT' : 'LECTURER';
$id_col    = $role === 'student' ? 'STUDENT_ID' : 'LECT_ID';
$name_col  = $role === 'student' ? 'STUDENT_NAME' : 'LECT_NAME';
$email_col = $role === 'student' ? 'STUDENT_EMAIL' : 'LECT_EMAIL';
$pic_col   = $role === 'student' ? 'STUDENT_PICTURE' : 'LECT_PICTURE';

$sql = "SELECT * FROM $table WHERE $id_col = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();

// ❌ If no user found
if (!$user) {
    echo "<h3>❌ No profile found for user ID: <code>$id</code></h3>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Profile</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      padding: 20px;
    }
    .profile-card {
      max-width: 400px;
      margin: auto;
      background: white;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      text-align: center;
    }
    .profile-card img {
      width: 150px;
      border-radius: 50%;
      object-fit: cover;
    }
    .profile-card p {
      margin: 10px 0;
    }
  </style>
</head>
<body>
  <div class="profile-card">
    <h2>👤 Profile Info</h2>
    <img src="<?= htmlspecialchars($user[$pic_col] ?: 'default-pfp.png') ?>" alt="Profile Picture">
    <p><strong>ID:</strong> <?= htmlspecialchars($user[$id_col]) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($user[$name_col]) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user[$email_col]) ?></p>
    <p><strong>Role:</strong> <?= ucfirst($role) ?></p>
  </div>
</body>
</html>
