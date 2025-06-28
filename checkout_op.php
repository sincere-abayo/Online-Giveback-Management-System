<?php
// DB connection
$conn = new mysqli('localhost', 'root', '', 'gms');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize inputs
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$phone = isset($_POST['card-num']) ? trim($_POST['card-num']) : '';
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$provider = isset($_POST['provider']) ? $_POST['provider'] : null;

// Validation
if ($fullname == '' || $phone == '' || $amount <= 0 || !$provider) {
    die("Invalid input. Please fill in all fields.");
}

// Save into DB
$stmt = $conn->prepare("INSERT INTO donation (fullname, phone_number, amount, provider) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssds", $fullname, $phone, $amount, $provider);

if ($stmt->execute()) {
    echo "Donation offered successfully!";
    header("refresh:2;URL= ./index.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<head>
<body class="hold-transition login-page  dark-mode">
  <body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed dark-mode sidebar-mini-md sidebar-mini-xs" data-new-gr-c-s-check-loaded="14.991.0" data-gr-ext-installed="" style="height: auto;">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, shrink-to-fit=no">
  <title>gms</title>
  <link rel="stylesheet" type="text/css" href="inc/.css" media="
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<body><center>                                  
    <img src="img/check.gif" alt="gree_check"style="
width: 800px;
margin-bottom:-450px;
color: #fff;"></center>
    <br>
    <style>
        body{
            margin: 0px;
            padding: 0px;
            background-color:white;
            position:center;
        
        }
        img{
           margin-top: 120px;
        }
        </style>