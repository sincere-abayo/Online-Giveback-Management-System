<?php

session_start();

require('db_config.php');

if (isset($_POST['id']) && is_numeric($_POST['id'])) {

  $image_id = (int) $_POST['id'];

  $sql = "DELETE FROM gallery WHERE id = ?";

  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param('i', $image_id);

  if ($stmt->execute()) {

    $_SESSION['success'] = 'Image deleted successfully.';

  } else {

    $_SESSION['error'] = 'Failed to delete image. Please try again.';
    error_log("Error deleting image (ID: $image_id): " . $mysqli->error);

  }

  $stmt->close();

} else {

  $_SESSION['error'] = 'Invalid image ID.';

}

header("Location: http://localhost:./gallerie/index.php");
$mysqli->close(); 

?>
