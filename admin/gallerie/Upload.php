<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
require_once('../../config.php');

// Define base_url if not already defined
if (!defined('base_url')) {
  define('base_url', 'http://localhost/utb/GMS/');
}

if (isset($_POST) && !empty($_FILES['image']['name']) && !empty($_POST['title'])) {

  $name = $_FILES['image']['name'];
  $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
  $max_size = 1048576;
  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  if (!in_array($ext, $allowed_extensions)) {
    $_SESSION['error'] = 'Invalid image format. Please upload a JPEG, PNG, or GIF image.';
    header("Location: " . base_url . "admin/?page=gallerie");
    exit;
  }

  if ($_FILES['image']['size'] > $max_size) {
    $_SESSION['error'] = 'Image size exceeds the maximum limit of 1MB.';
    header("Location: " . base_url . "admin/?page=gallerie");
    exit;
  }

  $image_name = time() . "." . $ext;
  $upload_path = __DIR__ . '/uploads/' . $image_name;

  if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {

    $title = $conn->real_escape_string($_POST['title']);
    $sql = "INSERT INTO gallery (title, image) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $title, $image_name);

    if ($stmt->execute()) {
      $_SESSION['success'] = 'Image uploaded successfully.';
    } else {
      $_SESSION['error'] = 'Failed to insert image into database. Please try again.';
      error_log("Error inserting image: " . $conn->error);
      // Delete the uploaded file if database insert fails
      if (file_exists($upload_path)) {
        unlink($upload_path);
      }
    }
    $stmt->close();

  } else {
    $_SESSION['error'] = 'Failed to upload image.';
  }

} else {
  $_SESSION['error'] = 'Please select an image and write a title.';
}

header("Location: " . base_url . "admin/?page=gallerie");
exit;

?>