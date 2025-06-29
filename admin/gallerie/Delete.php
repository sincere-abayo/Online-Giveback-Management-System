<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
require_once('../../config.php');

// Define base_url if not already defined
if (!defined('base_url')) {
  define('base_url', 'http://localhost/utb/GMS/');
}

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
  $image_id = (int) $_POST['id'];

  // Get the image filename from DB
  $getImageQuery = "SELECT image FROM gallery WHERE id = ?";
  $stmt = $conn->prepare($getImageQuery);
  $stmt->bind_param('i', $image_id);
  $stmt->execute();
  $stmt->bind_result($image);
  $stmt->fetch();
  $stmt->close();

  if ($image) {
    // Delete the actual image file
    $file_path = __DIR__ . '/uploads/' . basename($image);
    if (file_exists($file_path)) {
      unlink($file_path);
    }

    // Delete the DB record
    $deleteQuery = "DELETE FROM gallery WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $image_id);

    if ($stmt->execute()) {
      $_SESSION['success'] = 'Image deleted successfully.';
    } else {
      $_SESSION['error'] = 'Failed to delete image from database.';
    }

    $stmt->close();
  } else {
    $_SESSION['error'] = 'Image not found.';
  }
} else {
  $_SESSION['error'] = 'Invalid image ID.';
}

header("Location: " . base_url . "admin/?page=gallerie");
exit;
?>


<--?php session_start(); require('db_config.php'); if (isset($_POST['id']) && is_numeric($_POST['id'])) {
  $image_id=(int) $_POST['id']; $sql="DELETE FROM gallery WHERE id = ?" ; $stmt=$conn->prepare($sql);
  $stmt->bind_param('i', $image_id);

  if ($stmt->execute()) {

  $_SESSION['success'] = 'Image deleted successfully.';

  } else {

  $_SESSION['error'] = 'Failed to delete image. Please try again.';
  error_log("Error deleting image (ID: $image_id): " . $conn->error);

  }

  $stmt->close();

  } else {

  $_SESSION['error'] = 'Invalid image ID.';

  }

  header("refresh:1;URL= http://localhost/GMS/admin/?page=gallerie");
  $mysqli->close();

  ?-->