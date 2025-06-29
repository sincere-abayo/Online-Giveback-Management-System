<?php
require_once '../config.php';
class Login extends DBConnection
{
	private $settings;
	public function __construct()
	{
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct()
	{
		parent::__destruct();
	}
	public function index()
	{
		echo "<h1>Access Denied</h1> <a href='" . base_url . "'>Go Back.</a>";
	}
	public function login()
	{
		extract($_POST);

		// First try to find user by username
		$qry = $this->conn->query("SELECT * from users where username = '$username'");
		if ($qry->num_rows > 0) {
			$user = $qry->fetch_array();

			// Check password - support both modern password_hash and legacy md5
			$password_valid = false;

			// First try password_verify (modern hashing)
			if (password_verify($password, $user['password'])) {
				$password_valid = true;
			}
			// If that fails, try md5 (legacy support)
			elseif (md5($password) === $user['password']) {
				$password_valid = true;
				// Upgrade the password to modern hashing
				$new_hash = password_hash($password, PASSWORD_DEFAULT);
				$this->conn->query("UPDATE users SET password = '$new_hash' WHERE id = {$user['id']}");
			}

			if ($password_valid) {
				foreach ($user as $k => $v) {
					if (!is_numeric($k) && $k != 'password') {
						$this->settings->set_userdata($k, $v);
					}
				}
				$this->settings->set_userdata('login_type', 1);
				return json_encode(array('status' => 'success'));
			}
		}

		return json_encode(array('status' => 'incorrect', 'last_qry' => "SELECT * from users where username = '$username'"));
	}
	public function logout()
	{
		if ($this->settings->sess_des()) {
			redirect('admin/login.php');
		}
	}
	function login_user()
	{
		extract($_POST);
		$qry = $this->conn->query("SELECT * from clients where email = '$email' and password = md5('$password') ");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $k => $v) {
				$this->settings->set_userdata($k, $v);
			}
			$this->settings->set_userdata('login_type', 1);
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'incorrect';
		}
		if ($this->conn->error) {
			$resp['status'] = 'failed';
			$resp['_error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login':
		echo $auth->login();
		break;
	case 'login_user':
		echo $auth->login_user();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	default:
		echo $auth->index();
		break;
}