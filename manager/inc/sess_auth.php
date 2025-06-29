<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
    $link = "https"; 
else
    $link = "http"; 
$link .= "://"; 
$link .= $_SERVER['HTTP_HOST']; 
$link .= $_SERVER['REQUEST_URI'];

// Require login for all manager pages except login.php
if(!isset($_SESSION['userdata']) && !strpos($link, 'login.php')){
	redirect('manager/login.php');
}
// If already logged in and trying to access login.php, redirect to dashboard
if(isset($_SESSION['userdata']) && strpos($link, 'login.php')){
	redirect('manager/index.php');
}
// Only allow users with type=2 (manager)
if(isset($_SESSION['userdata']) && $_SESSION['userdata']['type'] != 2){
    echo "<script>console.error('Access Denied: Not a manager.');alert('Access Denied! Only managers can access this page.');location.replace('".base_url."logout.php');</script>";
    exit;
}
// Optionally, you can check for specific roles here if needed
// Example:
// if(isset($_SESSION['managerdata']['role']) && $_SESSION['managerdata']['role'] != 'general_manager'){
//     echo "<script>alert('Access Denied!');location.replace('".base_url."');</script>";
//     exit;
// }
