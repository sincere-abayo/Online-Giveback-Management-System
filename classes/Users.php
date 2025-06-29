<?php
require_once('../config.php');
class Users extends DBConnection
{
	private $settings;
	public function __construct()
	{
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct()
	{
		parent::__destruct();
	}
	public function save_users()
	{
		extract($_POST);
		$data = '';
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'password'))) {
				if (!empty($data))
					$data .= " , ";
				$data .= " {$k} = '{$v}' ";
			}
		}

		// Handle password - use modern password_hash instead of md5
		if (!empty($password)) {
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);
			if (!empty($data))
				$data .= " , ";
			$data .= " `password` = '{$hashed_password}' ";
		}

		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = 'uploads/' . strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], '../' . $fname);
			if ($move) {
				$data .= " , avatar = '{$fname}' ";
				if (isset($_SESSION['userdata']['avatar']) && is_file('../' . $_SESSION['userdata']['avatar']) && $_SESSION['userdata']['id'] == $id)
					unlink('../' . $_SESSION['userdata']['avatar']);
			}
		}

		if (empty($id)) {
			// New user - add default password if no password provided
			if (empty($password)) {
				$default_password = 'password123';
				$hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
				if (!empty($data))
					$data .= " , ";
				$data .= " `password` = '{$hashed_password}' ";
			}

			$qry = $this->conn->query("INSERT INTO users set {$data}");
			if ($qry) {
				$user_id = $this->conn->insert_id;
				$default_password_msg = empty($password) ? " Default password: password123" : "";
				$this->settings->set_flashdata('success', 'User Details successfully saved.' . $default_password_msg);
				return 1;
			} else {
				return 2;
			}

		} else {
			$qry = $this->conn->query("UPDATE users set $data where id = {$id}");
			if ($qry) {
				$this->settings->set_flashdata('success', 'User Details successfully updated.');
				foreach ($_POST as $k => $v) {
					if ($k != 'id') {
						if (!empty($data))
							$data .= " , ";
						$this->settings->set_userdata($k, $v);
					}
				}
				if (isset($fname) && isset($move))
					$this->settings->set_userdata('avatar', $fname);

				return 1;
			} else {
				return "UPDATE users set $data where id = {$id}";
			}

		}
	}
	public function delete_users()
	{
		extract($_POST);
		$avatar = $this->conn->query("SELECT avatar FROM users where id = '{$id}'")->fetch_array()['avatar'];
		$qry = $this->conn->query("DELETE FROM users where id = $id");
		if ($qry) {
			$this->settings->set_flashdata('success', 'User Details successfully deleted.');
			if (is_file(base_app . $avatar))
				unlink(base_app . $avatar);
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
	public function save_fusers()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'password'))) {
				if (!empty($data))
					$data .= ", ";
				$data .= " `{$k}` = '{$v}' ";
			}
		}

		// Use modern password hashing
		if (!empty($password))
			$data .= ", `password` = '" . password_hash($password, PASSWORD_DEFAULT) . "' ";

		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = 'uploads/' . strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], '../' . $fname);
			if ($move) {
				$data .= " , avatar = '{$fname}' ";
				if (isset($_SESSION['userdata']['avatar']) && is_file('../' . $_SESSION['userdata']['avatar']))
					unlink('../' . $_SESSION['userdata']['avatar']);
			}
		}
		$sql = "UPDATE faculty set {$data} where id = $id";
		$save = $this->conn->query($sql);

		if ($save) {
			$this->settings->set_flashdata('success', 'User Details successfully updated.');
			foreach ($_POST as $k => $v) {
				if (!in_array($k, array('id', 'password'))) {
					if (!empty($data))
						$data .= " , ";
					$this->settings->set_userdata($k, $v);
				}
			}
			if (isset($fname) && isset($move))
				$this->settings->set_userdata('avatar', $fname);
			return 1;
		} else {
			$resp['error'] = $sql;
			return json_encode($resp);
		}

	}

	public function save_susers()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'password'))) {
				if (!empty($data))
					$data .= ", ";
				$data .= " `{$k}` = '{$v}' ";
			}
		}

		// Use modern password hashing
		if (!empty($password))
			$data .= ", `password` = '" . password_hash($password, PASSWORD_DEFAULT) . "' ";

		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = 'uploads/' . strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], '../' . $fname);
			if ($move) {
				$data .= " , avatar = '{$fname}' ";
				if (isset($_SESSION['userdata']['avatar']) && is_file('../' . $_SESSION['userdata']['avatar']))
					unlink('../' . $_SESSION['userdata']['avatar']);
			}
		}
		$sql = "UPDATE students set {$data} where id = $id";
		$save = $this->conn->query($sql);

		if ($save) {
			$this->settings->set_flashdata('success', 'User Details successfully updated.');
			foreach ($_POST as $k => $v) {
				if (!in_array($k, array('id', 'password'))) {
					if (!empty($data))
						$data .= " , ";
					$this->settings->set_userdata($k, $v);
				}
			}
			if (isset($fname) && isset($move))
				$this->settings->set_userdata('avatar', $fname);
			return 1;
		} else {
			$resp['error'] = $sql;
			return json_encode($resp);
		}

	}

}

$users = new users();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save':
		echo $users->save_users();
		break;
	case 'fsave':
		echo $users->save_fusers();
		break;
	case 'ssave':
		echo $users->save_susers();
		break;
	case 'delete':
		echo $users->delete_users();
		break;
	default:
		// echo $sysset->index();
		break;
}