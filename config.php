<?php
ob_start();
ini_set('date.timezone', 'Africa/Kigali');
date_default_timezone_set('Africa/Kigali');
session_start();

// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once('initialize.php');
require_once('classes/DBConnection.php');
require_once('classes/SystemSettings.php');
$db = new DBConnection;
$conn = $db->conn;

function redirect($url = '')
{
    if (!empty($url))
        echo '<script>location.href="' . base_url . $url . '"</script>';
}
function validate_image($file)
{
    if (!empty($file)) {
        // exit;
        if (is_file(base_app . $file)) {
            return base_url . $file;
        } else {
            return base_url . 'dist/img/no-image-available.png';
        }
    } else {
        return base_url . 'dist/img/no-image-available.png';
    }
}
function isMobileDevice()
{
    $aMobileUA = array(
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );

    //Return true if Mobile User Agent is detected
    foreach ($aMobileUA as $sMobileKey => $sMobileOS) {
        if (preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
    }
    //Otherwise return false..  
    return false;
}

// Helper function to get environment variables
function env($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}

ob_end_flush();
?>