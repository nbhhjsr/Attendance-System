<html>
<head>
	<title>Log In</title>
</head>

<body>
<?php
session_start(); // ✅ Needed to store session variables
include 'att_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$UserRole = $_POST['USER_ROLE'];
	$userID = $_POST['USER_ID'];
	$RegPass= $_POST['REG_PASS'];

	//search user in database
	$sql = "SELECT * FROM REGISTER WHERE USER_ID = '$userID' AND USER_ROLE = '$UserRole'";
	$result = mysqli_query($conn, $sql) or die("Query failed" . mysqli_error($conn));	//SQL statement for checking

	if ($result && mysqli_num_rows($result) > 0) {
		$user = mysqli_fetch_assoc($result);
		
			//password verify() if (password_verify ($RegPass, $user['REG_PASS'])) 
			if ($RegPass === $user['REG_PASS']) {
				$_SESSION['USER_ID'] = $user['USER_ID']; // ✅ Store user ID in session
				$_SESSION['USER_ROLE'] = $user['USER_ROLE']; // optional, in case you want to use it

				if ($UserRole === 'Lecturer'){
					echo "<script>alert('Login successfull. Welcome, $userID 🤍'); window.location.href='mainpageLect.php';</script>";
				}
				else
					echo "<script>alert('Login successfull. Welcome, $userID 🤍'); window.location.href='mainpageStud.html';</script>";
			} 
			else {
				echo "Incorrect password 🥲. <a href='login.html'>Try again 🫠</a>";
			}
	}
	else {
		echo "User not found or role mismatch 🤔. <a href='login.html'>Try again 🫠</a>";
	}
		mysqli_close($conn);
	}
else {
	echo "Please submit the form!";
}


?>
</body>
</html>