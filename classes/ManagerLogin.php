<?php
require_once('DBConnection.php');
class ManagerLogin extends DBConnection{
    private $settings;
    
    public function __construct(){
        parent::__construct();
        global $_settings;
        $this->settings = $_settings;
        
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }
    }
    
    public function __destruct(){
        parent::__destruct();
    }
    
    public function index(){
        echo "<script>window.location.href='".base_url."manager/login.php';</script>";
    }
    
    public function login(){
        extract($_POST);
        if(empty($username) || empty($password)){
            return json_encode(array('status'=>'error','message'=>'Username and password are required.'));
        }
        $qry = $this->conn->query("SELECT * FROM managers WHERE username = '".$this->conn->real_escape_string($username)."' LIMIT 1");
        if($qry === false) {
            return json_encode(array('status'=>'error','message'=>'Database error: '. $this->conn->error));
        }
        if($qry->num_rows > 0){
            $row = $qry->fetch_assoc();
            if($row['status'] != 1){
                $this->logFailedAttempt($username, 'Inactive account');
                return json_encode(array('status'=>'inactive','message'=>'Your account is inactive. Please contact the administrator.'));
            }
            if(password_verify($password, $row['password'])){
                foreach($row as $k => $val){
                    if($k != 'password' && $k != 'id')
                        $_SESSION['managerdata'][$k]=$val;
                }
                // Update last login
                $this->conn->query("UPDATE managers SET last_login = NOW() WHERE id = ".$row['id']);
                // Log activity
                $this->logActivity('login', 'Manager logged in successfully');
                return json_encode(array('status'=>'success'));
            }else{
                $this->logFailedAttempt($username, 'Wrong password');
                return json_encode(array('status'=>'incorrect','message'=>'Incorrect username or password.'));
            }
        }else{
            $this->logFailedAttempt($username, 'Username not found');
            return json_encode(array('status'=>'incorrect','message'=>'Incorrect username or password.'));
        }
    }
    
    public function logout(){
        if(isset($_SESSION['managerdata'])){
            // Log activity before destroying session
            $this->logActivity('logout', 'Manager logged out');
            unset($_SESSION['managerdata']);
            foreach($_SESSION as $key => $val){
                unset($_SESSION[$key]);
            }
        }
        echo "<script>window.location.href='".base_url."manager/login.php';</script>";
    }
    
    public function logActivity($action, $description, $target_table = null, $target_id = null){
        if(isset($_SESSION['managerdata']['id'])){
            $manager_id = $_SESSION['managerdata']['id'];
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $sql = "INSERT INTO manager_activity_log (manager_id, action, description, target_table, target_id, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("issssss", $manager_id, $action, $description, $target_table, $target_id, $ip_address, $user_agent);
            $stmt->execute();
        }
    }
    
    public function logFailedAttempt($username, $reason){
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $sql = "INSERT INTO manager_activity_log (manager_id, action, description, ip_address, user_agent) VALUES (NULL, 'failed_login', ?, ?, ?)";
        $desc = "Failed login for username: $username. Reason: $reason";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $desc, $ip_address, $user_agent);
        $stmt->execute();
    }
    
    public function getManagerData($field = null){
        if(isset($_SESSION['managerdata'])){
            if($field){
                return $_SESSION['managerdata'][$field] ?? null;
            }
            return $_SESSION['managerdata'];
        }
        return null;
    }
    
    public function hasPermission($permission){
        if(isset($_SESSION['managerdata']['permissions'])){
            $permissions = json_decode($_SESSION['managerdata']['permissions'], true);
            return isset($permissions[$permission]) && $permissions[$permission] === true;
        }
        return false;
    }
    
    public function getManagerRole(){
        return $this->getManagerData('role') ?? 'general_manager';
    }
    
    public function isLoggedIn(){
        return isset($_SESSION['managerdata']);
    }
    
    public function requireLogin(){
        if(!$this->isLoggedIn()){
            echo "<script>window.location.href='".base_url."manager/login.php';</script>";
            exit;
        }
    }
    
    public function requirePermission($permission){
        $this->requireLogin();
        if(!$this->hasPermission($permission)){
            echo "<script>alert('Access Denied: You do not have permission to access this feature.');window.location.href='".base_url."manager/';</script>";
            exit;
        }
    }
}

$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new ManagerLogin();

switch($action){
    case 'login':
        echo $sysset->login();
        break;
    case 'logout':
        echo $sysset->logout();
        break;
    default:
        echo $sysset->index();
        break;
}
?> 