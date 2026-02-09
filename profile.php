<?php
session_start();

// ✅ Crop and Resize function
function cropAndResizeImage($sourcePath, $destPath, $size = 200) {
    $info = getimagesize($sourcePath);
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $src = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $src = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $src = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }

    $width = imagesx($src);
    $height = imagesy($src);
    $short = min($width, $height);
    $cropX = ($width - $short) / 2;
    $cropY = ($height - $short) / 2;

    $crop = imagecrop($src, ['x' => $cropX, 'y' => $cropY, 'width' => $short, 'height' => $short]);
    $resized = imagecreatetruecolor($size, $size);
    imagecopyresampled($resized, $crop, 0, 0, 0, 0, $size, $size, $short, $short);

    imagejpeg($resized, $destPath, 90);

    imagedestroy($src);
    imagedestroy($crop);
    imagedestroy($resized);
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "root", "", "attendance_system");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $role     = $_POST['USER_ROLE'] ?? '';
	$id       = $_POST['USER_ID'] ?? '';
    $email    = $_POST['REG_EMAIL'] ?? '';
    $fullname = $_POST['USER_FULLNAME'] ?? '';

    $profile_pic = $_FILES['pfp']['name'] ?? '';
    $tmp_name    = $_FILES['pfp']['tmp_name'] ?? '';
    $upload_dir  = "uploads/";
    $sanitized_name = str_replace(' ', '_', basename($profile_pic));
    $target_file = $upload_dir . $sanitized_name;

    if ($role !== 'Student' && $role !== 'Lecturer') {
        die("❌ Invalid user role selected.");
    }

    if ($role === 'Student') {
        $table = "STUDENT";
        $pic_col = "STUDENT_PICTURE";
		$id_col = "STUDENT_ID";
        $name_col = "STUDENT_NAME";
        $email_col = "STUDENT_EMAIL";
    } else {
        $table = "LECTURER";
        $pic_col = "LECT_PICTURE";
		$id_col = "LECT_ID";
		$name_col = "LECT_NAME";
        $email_col = "LECT_EMAIL";
    }

    // ✅ Handle image upload
    if (!empty($profile_pic)) {
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        if (move_uploaded_file($tmp_name, $target_file)) {
            cropAndResizeImage($target_file, $target_file, 200);
        } else {
            die("❌ Failed to upload image.");
        }
    }

    // ✅ Check if user exists
    $checkSql = "SELECT * FROM $table WHERE $id_col = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // ✅ User exists → UPDATE
        if (!empty($profile_pic)) {
            $sql = "UPDATE $table SET $pic_col = ?, $email_col = ?, $name_col = ? WHERE $id_col = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $target_file, $email, $fullname, $id);
        } else {
            $sql = "UPDATE $table SET $email_col = ?, $name_col = ? WHERE $id_col = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $email, $fullname, $id);
        }
    } else {
        // ❗ User doesn't exist → INSERT
        if (!empty($profile_pic)) {
            $sql = "INSERT INTO $table ($id_col, $email_col, $name_col, $pic_col) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $id, $email, $fullname, $target_file);
        } else {
            $sql = "INSERT INTO $table ($id_col, $email_col, $name_col) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $id, $email, $fullname);
        }
    }

    // ✅ Execute INSERT or UPDATE
    if ($stmt->execute()) {
        $_SESSION['REG_EMAIL'] = $email;
        $_SESSION['USER_FULLNAME'] = $fullname;
        $_SESSION['USER_ID'] = $id;
        $_SESSION['USER_ROLE'] = $role;
        if (!empty($profile_pic)) {
            $_SESSION['PROFILE_PIC'] = $target_file;
        }

        header("Location: viewProfile.php?USER_ROLE=$role&USER_ID=$id");
        exit;
    } else {
        echo "<script>alert('❌ Error: " . $stmt->error . "'); window.location.href = 'editprofile.php';</script>";
    }

    $stmt->close();
    $checkStmt->close();
    $conn->close();
} else {
    echo "<script>alert('❌ Invalid access.'); window.location.href = 'editprofile.php';</script>";
}
?>
