<?php

session_start();
require('db_config.php');

if (isset($_POST) && !empty($_FILES['image']['name']) && !empty($_POST['title'])) {

  $name = $_FILES['image']['name'];
  $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
  $max_size = 1048576; 
  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  if (!in_array($ext, $allowed_extensions)) {
    $_SESSION['error'] = 'Invalid image format. Please upload a JPEG, PNG, or GIF image.';
    header("Location: http://localhost:./gallerie/index.php");
    exit;
  }

  if ($_FILES['image']['size'] > $max_size) {
    $_SESSION['error'] = 'Image size exceeds the maximum limit of 1MB.';
    header("Location: http://localhost:./gallerie/index");
    exit;
  }

  $image_name = time() . "." . $ext;

  if (move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image_name)) {

    $title = mysqli_real_escape_string($mysqli, $_POST['title']); 
        $sql = "INSERT INTO gallery (title, image) VALUES ('$title', '$image_name')";

    if ($mysqli->query($sql)) {
      $_SESSION['success'] = 'Uploading of image is successfully.';
      header("Location: http://localhost:./gallerie/index.php");
    } else {
      $_SESSION['error'] = 'Failed to insert image into database. Please try again.';
      error_log("Error inserting image: " . $mysqli->error);
    }

  } else {
    $_SESSION['error'] = 'Failed to upload image.';
  }

} else {
  $_SESSION['error'] = 'Please Select Image or Write title';
  header("Location: http://localhost:./gallerie/index.php");
}

?>
