<?php
require_once('../config.php');
class Master extends DBConnection
{
	private $settings;
	public function __construct()
	{
		global $_settings;
		$this->settings = $_settings;
		$this->permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		parent::__construct();
	}
	public function __destruct()
	{
		parent::__destruct();
	}
	function capture_err()
	{
		if (!$this->conn->error)
			return false;
		else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_topic()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'description'))) {
				if (!empty($data))
					$data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if (isset($_POST['description'])) {
			if (!empty($data))
				$data .= ",";
			$data .= " `description`='" . addslashes(htmlentities($description)) . "' ";
		}
		$check = $this->conn->query("SELECT * FROM `topics` where `name` = '{$name}' " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($this->capture_err())
			return $this->capture_err();
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Topic already exist.";
			return json_encode($resp);
			exit;
		}
		if (empty($id)) {
			$sql = "INSERT INTO `topics` set {$data} ";
			$save = $this->conn->query($sql);
		} else {
			$sql = "UPDATE `topics` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if ($save) {
			$resp['status'] = 'success';
			if (empty($id))
				$this->settings->set_flashdata('success', "New Topic successfully saved.");
			else
				$this->settings->set_flashdata('success', "Topic successfully updated.");
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_topic()
	{
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `topics` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Topic successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function generate_string($input, $strength = 10)
	{

		$input_length = strlen($input);
		$random_string = '';
		for ($i = 0; $i < $strength; $i++) {
			$random_character = $input[mt_rand(0, $input_length - 1)];
			$random_string .= $random_character;
		}

		return $random_string;
	}
	function upload_files()
	{
		extract($_POST);
		$data = "";
		if (empty($upload_code)) {
			while (true) {
				$code = $this->generate_string($this->permitted_chars);
				$chk = $this->conn->query("SELECT * FROM `uploads` where dir_code ='{$code}' ")->num_rows;
				if ($chk <= 0) {
					$upload_code = $code;
					$resp['upload_code'] = $upload_code;
					break;
				}
			}
		}

		if (!is_dir(base_app . 'uploads/blog_uploads/' . $upload_code))
			mkdir(base_app . 'uploads/blog_uploads/' . $upload_code);
		$dir = 'uploads/blog_uploads/' . $upload_code . '/';
		$images = array();
		for ($i = 0; $i < count($_FILES['img']['tmp_name']); $i++) {
			if (!empty($_FILES['img']['tmp_name'][$i])) {
				$fname = $dir . (time()) . '_' . $_FILES['img']['name'][$i];
				$f = 0;
				while (true) {
					$f++;
					if (is_file(base_app . $fname)) {
						$fname = $f . "_" . $fname;
					} else {
						break;
					}
				}
				$move = move_uploaded_file($_FILES['img']['tmp_name'][$i], base_app . $fname);
				if ($move) {
					$this->conn->query("INSERT INTO `uploads` (dir_code,user_id,file_path)VALUES('{$upload_code}','{$this->settings->userdata('id')}','{$fname}')");
					$this->capture_err();
					$images[] = $fname;
				}
			}
		}
		$resp['images'] = $images;
		$resp['status'] = 'success';
		return json_encode($resp);
	}
	function save_blog()
	{
		foreach ($_POST as $k => $v) {
			$_POST[$k] = addslashes($v);
		}
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'content', 'upload_code', 'banner_image', 'img', 'blog_url'))) {
				if (!empty($data))
					$data .= ",";
				$v = addslashes($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if (empty($blog_url)) {
			$blog_url = 'pages/' . (strtolower(str_replace(" ", "_", $title))) . '.php';

		}
		if (!empty($data))
			$data .= ",";
		$data .= " `blog_url`='" . $blog_url . "' ";
		if (isset($_POST['content'])) {
			if (!empty($data))
				$data .= ",";
			$data .= " `content`='" . addslashes(htmlentities($content)) . "' ";
		}
		if (!empty($data))
			$data .= ",";
		$data .= " `upload_dir_code`='{$upload_code}' ";
		if (empty($id)) {
			$data .= ", `author_id`='{$this->settings->userdata('id')}' ";
			$sql = "INSERT INTO `blogs` set {$data} ";
			$save = $this->conn->query($sql);
			$id = $this->conn->insert_id;
		} else {
			$sql = "UPDATE `blogs` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if ($save) {
			$resp['status'] = 'success';
			if (empty($id))
				$this->settings->set_flashdata('success', "New Blog successfully saved.");
			else
				$this->settings->set_flashdata('success', "Blog successfully updated.");
			$id = empty($id) ? $this->conn->insert_id : $id;
			$dir = 'uploads/blog_uploads/banners/';
			if (!is_dir(base_app . $dir))
				mkdir(base_app . $dir);
			if (isset($_FILES['banner_image'])) {
				if (!empty($_FILES['banner_image']['tmp_name'])) {
					$fname = $dir . $id . "_banner." . (pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION));
					$move = move_uploaded_file($_FILES['banner_image']['tmp_name'], base_app . $fname);
					if ($move) {
						$this->conn->query("UPDATE `blogs` set `banner_path` = '{$fname}' where id = '{$id}' ");
					}
				}
			}
			if (!isset($fnme))
				$fname = $this->conn->query("SELECT banner_path FROM `blogs` where id = '{$id}' ")->fetch_array()['banner_path'];
			$date_created = $this->conn->query("SELECT date_created FROM `blogs` where id = '{$id}' ")->fetch_array()['date_created'];

			if (!is_file(base_app . $blog_url))
				file_put_contents(base_app . $blog_url, '');
			$content = stripslashes($content);
			$contents = "<?php require_once('../config.php'); ?>\n";
			$contents .= "
<!DOCTYPE HTML>\n";
			$contents .= "

<head>\n";
			$contents .= "<title> " . $title . " | <?php echo \$_settings->info('short_name') ?></title>\n";
			$contents .= "
    <meta name=\"description\" content=\"" . $meta_description . "\">\n";
			$contents .= "
    <meta name=\"keywords\" content=\"" . $keywords . "\">\n";
			$contents .= "
    <meta name=\"robots\" content=\"index, follow\">\n";
			$contents .= '
    <meta property="og:type" content="article" />';
			$contents .= "\n";
			$contents .= '
    <meta property="og:title" content="' . (addslashes($title)) . '" />';
			$contents .= "\n";
			$contents .= '
    <meta property="og:description" content="' . (addslashes($meta_description)) . '" />';
			$contents .= "\n";
			$contents .= '
    <meta property="og:image" content="' . (validate_image($fname)) . '" />';
			$contents .= "\n";
			$contents .= '
    <meta property="og:url" content="' . (base_url . $blog_url) . '" />';
			$contents .= "\n";
			$contents .= "<?php require_once('../inc/page_header.php') ?>\n";
			$contents .= "
</head>\n";
			$contents .= "<?php include(base_app.'inc/body_block.php') ?>\n";
			$contents .= '<h2>' . addslashes($title) . '</h2>';
			$contents .= "\n";
			$contents .= '<input name="blog_id" value="' . (md5($id)) . '" type="hidden">';
			$contents .= "\n";
			$contents .= '
<hr>';
			$contents .= "\n";
			$contents .= "<span class='text-muted'><i class='fa fa-calendar-day'></i> Published: " . (date("M d,Y h:i
    A", strtotime($date_created))) . "</span>";
			$contents .= "\n";
			$contents .= '<center><img src="' . (validate_image($fname)) . '" class="img-thumbnail img-banner"
        alt="' . (base_url . $blog_url) . '" /></center>';

			$contents .= "\n";
			$contents .= ($content);
			$contents .= "\n";
			$contents .= "<?php include(base_app.'inc/body_block_end.php') ?>\n";
			file_put_contents(base_app . $blog_url, html_entity_decode($contents));

		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_blog()
	{
		extract($_POST);
		$qry = $this->conn->query("SELECT * FROM `blogs` where id = '{$id}'");
		foreach ($qry->fetch_array() as $k => $v) {
			$meta[$k] = $v;
		}
		$del = $this->conn->query("DELETE FROM `blogs` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			if (is_file(base_app . $meta['blog_url']))
				unlink((base_app . $meta['blog_url']));
			$this->settings->set_flashdata('success', " successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function delete_img()
	{
		extract($_POST);
		if (is_file(base_app . $path)) {
			if (unlink(base_app . $path)) {
				$del = $this->conn->query("DELETE FROM `uploads` where file_path = '{$path}'");
				$resp['status'] = 'success';
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete ' . $path;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown ' . $path . ' path';
		}
		return json_encode($resp);
	}
	function save_event()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			$v = addslashes($v);
			if (!in_array($k, array('id'))) {
				if (!empty($data))
					$data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if (empty($id)) {
			$sql = "INSERT INTO `events` set {$data} ";
			$save = $this->conn->query($sql);
		} else {
			$sql = "UPDATE `events` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if ($save) {
			if (empty($id))
				$this->settings->set_flashdata('success', "New Event successfully saved.");
			else
				$this->settings->set_flashdata('success', "Event successfully updated.");
			$resp['status'] = 'success';
			$id = empty($id) ? $this->conn->insert_id : $id;
			if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name'])) {
				$dir = 'uploads/events/';
				if (!is_dir(base_app . $dir))
					mkdir(base_app . $dir);
				$fname = $dir . $id . '.' . (pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
				$move = move_uploaded_file($_FILES['img']['tmp_name'], base_app . $fname);
				if ($move) {
					$this->conn->query("UPDATE `events` set img_path = '{$fname}' where id = '{$id}'");
				}
			}
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_event()
	{
		extract($_POST);
		$img_path = $this->conn->query("SELECT img_path FROM `events` where id = '{$id}'")->fetch_array()['img_path'];
		$del = $this->conn->query("DELETE FROM `events` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Event successfully deleted.");
			if (is_file(base_app . $img_path)) {
				unlink(base_app . $img_path);
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_donation()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data))
					$data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}

		$sql = "INSERT INTO `donations` set {$data} ";
		$save = $this->conn->query($sql);
		if ($save) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Donation successfully Added. Thank you!");
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		return json_encode($resp);
	}
	function save_cause()
	{
		extract($_POST);
		$blog_url = "team.php";
		if (!is_file(base_app . $blog_url))
			file_put_contents(base_app . $blog_url, '');
		$data['keywords'] = $keywords;
		$data['meta_description'] = $meta_description;
		$data['content'] = addslashes(htmlentities($content));
		file_put_contents(base_app . '/cause.json', json_encode($data));
		$content = stripslashes($content);
		$contents = "<?php require_once('config.php'); ?>\n";
		$contents .= "
<!DOCTYPE HTML>\n";
		$contents .= "

<head>\n";
		$contents .= "<title> | <?php echo \$_settings->info('short_name') ?></title>\n";
		$contents .= "
    <meta name=\"description\" content=\"" . $meta_description . "\">\n";
		$contents .= "
    <meta name=\"keywords\" content=\"" . $keywords . "\">\n";
		$contents .= "
    <meta name=\"robots\" content=\"index, follow\">\n";
		$contents .= '
    <meta property="og:type" content="article" />';
		$contents .= "\n";
		$contents .= '
    <meta property="og:title" content="Team" />';
		$contents .= "\n";
		$contents .= '
    <meta property="og:description" content="' . (addslashes($meta_description)) . '" />';
		$contents .= "\n";
		$contents .= '
    <meta property="og:image" content="' . (validate_image($this->settings->info('logo'))) . '" />';
		$contents .= "\n";
		$contents .= '
    <meta property="og:url" content="' . (base_url . $blog_url) . '" />';
		$contents .= "\n";
		$contents .= "<?php require_once(base_app.'inc/page_header.php') ?>\n";
		$contents .= "
</head>\n";
		$contents .= "<?php include(base_app.'inc/body_block.php') ?>\n";
		$contents .= "\n";
		$contents .= ($content);
		$contents .= "\n";
		$contents .= "<?php include(base_app.'inc/body_block_end.php') ?>\n";
		file_put_contents(base_app . $blog_url, html_entity_decode($contents));

		$resp['status'] = 'success';
		$this->settings->set_flashdata('success', ' Page Content Successfully updated.');
		return json_encode($resp);
	}

	function save_program()
	{
		$data = "";

		// Only process the specific fields needed for program_list table
		$allowed_fields = array('name', 'description', 'status');
		$processed_data = array();
		$id = isset($_POST['id']) ? $_POST['id'] : '';

		foreach ($allowed_fields as $field) {
			if (isset($_POST[$field])) {
				$value = $_POST[$field];
				if (!is_numeric($value))
					$value = $this->conn->real_escape_string($value);
				$processed_data[$field] = $value;
			}
		}

		// Build the data string only with allowed fields
		foreach ($processed_data as $k => $v) {
			if (!empty($data))
				$data .= ",";
			$data .= " `{$k}`='{$v}' ";
		}

		if (empty($id)) {
			$sql = "INSERT INTO `program_list` set {$data} ";
		} else {
			$sql = "UPDATE `program_list` set {$data} where id = '{$id}' ";
		}

		$check = $this->conn->query("SELECT * FROM `program_list` where `name` = '{$processed_data['name']}' " . (is_numeric($id) && $id > 0 ? " and id != '{$id}'" : "") . " ")->num_rows;
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = 'Program already exists.';

		} else {
			$save = $this->conn->query($sql);
			if ($save) {
				$rid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['id'] = $rid;
				$resp['status'] = 'success';
				if (empty($id))
					$resp['msg'] = "Program has successfully added.";
				else
					$resp['msg'] = "Program details has been updated successfully.";
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occured.";
				$resp['err'] = $this->conn->error . "[{$sql}]";
			}
		}
		if ($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function delete_program()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `program_list` set delete_flag = 1 where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Program has been deleted successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_activity()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if (!empty($data))
					$data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if (empty($id)) {
			$sql = "INSERT INTO `activity_list` set {$data} ";
		} else {
			$sql = "UPDATE `activity_list` set {$data} where id = '{$id}' ";
		}
		$check = $this->conn->query("SELECT * FROM `activity_list` where `name` = '{$name}' and `program_id` = '{$program_id}'
" . (is_numeric($id) && $id > 0 ? " and id != '{$id}'" : "") . " ")->num_rows;
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = ' Activity already exists on the selected Program.';

		} else {
			$save = $this->conn->query($sql);
			if ($save) {
				$rid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['id'] = $rid;
				$resp['status'] = 'success';
				if (empty($id))
					$resp['msg'] = " Activity has successfully added.";
				else
					$resp['msg'] = " Activity details has been saved successfully.";
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occured.";
				$resp['err'] = $this->conn->error . "[{$sql}]";
			}
		}
		if ($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function delete_activity()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `activity_list` set delete_flag = 1 where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Activity has been deleted successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_volunteer()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, ['id'])) {
				if (!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if (!empty($data))
					$data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}

		// Generate roll number for new volunteers
		if (empty($id)) {
			$year = date('Y');
			$last_roll = $this->conn->query("SELECT roll FROM volunteer_list WHERE roll LIKE '{$year}%' ORDER BY id DESC LIMIT
1")->fetch_assoc();
			if ($last_roll && !empty($last_roll['roll'])) {
				$last_num = intval(substr($last_roll['roll'], -3));
				$new_num = $last_num + 1;
			} else {
				$new_num = 1;
			}
			$roll = $year . str_pad($new_num, 3, '0', STR_PAD_LEFT);
			$data .= ", `roll`='{$roll}' ";
		}

		if (empty($id)) {
			$sql = "INSERT INTO `volunteer_list` set {$data} ";
		} else {
			$sql = "UPDATE `volunteer_list` set {$data} where id = '{$id}' ";
		}
		$check = $this->conn->query("SELECT * FROM `volunteer_list` where email = '{$email}' " . (!empty($id) ? " and id !=
'{$id}' " : "") . " ")->num_rows;
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Email already exists.";
		} else {
			$save = $this->conn->query($sql);
			if ($save) {
				$sid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['sid'] = $sid;
				$resp['roll'] = $roll ?? '';
				$resp['status'] = 'success';
				if (empty($id))
					$resp['msg'] = " Volunteer Information successfully saved.";
				else
					$resp['msg'] = " Volunteer Information successfully saved.";
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occured.";
				$resp['err'] = $this->conn->error . "[{$sql}]";
			}
		}
		if ($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}

	function send_volunteer_email()
	{
		extract($_POST);

		$volunteer_data = json_decode($volunteer_data, true);
		$is_new = $is_new == 'true';
		$status = intval($status);

		try {
			// Check if PHPMailer is available
			if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
				require_once __DIR__ . '/../vendor/autoload.php';

				if (class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
					$mail = new \PHPMailer\PHPMailer\PHPMailer(true);

					// Server settings
					$mail->isSMTP();
					$mail->Host = 'smtp.gmail.com';
					$mail->SMTPAuth = true;
					$mail->Username = 'infofonepo@gmail.com';
					$mail->Password = 'zaoxwuezfjpglwjb';
					$mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
					$mail->Port = 587;
					$mail->SMTPDebug = 0;

					// Recipients
					$mail->setFrom('dufatanyecharity@gmail.com', 'Dufatanye Charity Foundation');
					$mail->addAddress($volunteer_data['email'], $volunteer_data['firstname'] . ' ' . $volunteer_data['lastname']);

					// Content
					$mail->isHTML(true);
					$subject = $is_new ? 'Welcome to Dufatanye Charity Foundation - Volunteer Registration' : 'Volunteer Status Update -
Dufatanye Charity Foundation';
					$mail->Subject = $subject;
					$mail->Body = $this->getVolunteerEmailHTML($volunteer_data, $is_new, $status);
					$mail->AltBody = $this->getVolunteerEmailText($volunteer_data, $is_new, $status);

					$mail->send();
					return json_encode(['success' => true, 'message' => 'Email sent successfully']);
				}
			}

			// Fallback to basic PHP mail
			$subject = $is_new ? 'Welcome to Dufatanye Charity Foundation - Volunteer Registration' : 'Volunteer Status Update -
Dufatanye Charity Foundation';
			$message = $this->getVolunteerEmailHTML($volunteer_data, $is_new, $status);

			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: Dufatanye Charity Foundation <dufatanyecharity@gmail.com>' . "\r\n";

			$success = mail($volunteer_data['email'], $subject, $message, $headers);

			return json_encode([
				'success' => $success,
				'message' => $success ? 'Email sent successfully' : 'Failed to send email'
			]);

		} catch (Exception $e) {
			return json_encode(['success' => false, 'message' => 'Email Error: ' . $e->getMessage()]);
		}
	}

	private function getVolunteerEmailHTML($volunteer_data, $is_new, $status)
	{
		$status_text = '';
		$status_color = '';
		$status_icon = '';

		switch ($status) {
			case 0:
				$status_text = 'Pending Review';
				$status_color = '#ffc107';
				$status_icon = '⏳';
				break;
			case 1:
				$status_text = 'Approved';
				$status_color = '#28a745';
				$status_icon = '✅';
				break;
			case 2:
				$status_text = 'Denied';
				$status_color = '#dc3545';
				$status_icon = '❌';
				break;
		}

		$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";

		return "
    <!DOCTYPE html>
    <html lang='en'>

    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>" . ($is_new ? 'Welcome' : 'Status Update') . " - Dufatanye Charity Foundation</title>
        <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .email-body {
            padding: 40px 30px;
        }

        .status-banner {
            background: $status_color;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }

        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 10px 0;
        }

        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            margin: 10px 5px;
        }
        </style>
    </head>

    <body>
        <div class='email-container'>
            <div class='email-header'>
                <h1>" . ($is_new ? 'Welcome to Our Team!' : 'Volunteer Update') . "</h1>
                <p>Dufatanye Charity Foundation</p>
            </div>
            <div class='email-body'>
                " . ($is_new ? "
                <div class='status-banner'>
                    <h2>🎉 Registration Successful!</h2>
                    <p>Dear " . htmlentities($volunteer_data['firstname'] . ' ' . $volunteer_data['lastname']) . ",</p>
                </div>

                <p>Thank you for your interest in volunteering with Dufatanye Charity Foundation! We're excited to have
                    you join our community of dedicated volunteers.</p>

                <div class='info-item'>
                    <strong>Registration Details:</strong><br>
                    <strong>Name:</strong> " . htmlentities($volunteer_data['firstname'] . ' ' .
				$volunteer_data['lastname']) . "<br>
                    <strong>Email:</strong> " . htmlentities($volunteer_data['email']) . "<br>
                    <strong>Contact:</strong> " . htmlentities($volunteer_data['contact']) . "<br>
                    <strong>Registration Date:</strong> " . date('F j, Y') . "
                </div>

                <p><strong>What happens next?</strong></p>
                <ul>
                    <li>Our team will review your application</li>
                    <li>You'll receive an email with your approval status</li>
                    <li>Once approved, you can access your volunteer dashboard</li>
                    <li>Start making a difference in our community!</li>
                </ul>
                " : "
                <div class='status-banner'>
                    <h2>$status_icon Status: $status_text</h2>
                    <p>Dear " . htmlentities($volunteer_data['firstname'] . ' ' . $volunteer_data['lastname']) . ",</p>
                </div>

                <p>Your volunteer application status has been updated.</p>

                <div class='info-item'>
                    <strong>Current Status:</strong> $status_text<br>
                    <strong>Volunteer ID:</strong> " . htmlentities($volunteer_data['roll'] ?? 'N/A') . "<br>
                    <strong>Update Date:</strong> " . date('F j, Y') . "
                </div>
                ") . "

                " . ($status == 1 ? "
                <p><strong>Congratulations! You're now an approved volunteer.</strong></p>
                <ul>
                    <li>Access your volunteer dashboard</li>
                    <li>Update your profile information</li>
                    <li>Join available volunteer activities</li>
                    <li>Track your contributions and impact</li>
                </ul>

                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$baseUrl}volunteer_login.php' class='btn'>🔐 Access Dashboard</a>
                    <a href='{$baseUrl}volunteer/profile.php' class='btn'>👤 View Profile</a>
                </div>
                " : ($status == 2 ? "
                <p>We appreciate your interest in our organization. If you have any questions about this decision or
                    would like to reapply in the future, please don't hesitate to contact us.</p>
                " : "
                <p>Your application is currently under review. We'll notify you as soon as we have an update.</p>
                ")) . "

                <p>Thank you for your understanding and continued support.</p>

                <p>Best regards,<br>
                    <strong>Dufatanye Charity Foundation Team</strong>
                </p>
            </div>
        </div>
    </body>

    </html>";
	}

	private function getVolunteerEmailText($volunteer_data, $is_new, $status)
	{
		$status_text = '';
		switch ($status) {
			case 0:
				$status_text = 'Pending Review';
				break;
			case 1:
				$status_text = 'Approved';
				break;
			case 2:
				$status_text = 'Denied';
				break;
		}

		$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";

		return "
    " . ($is_new ? 'Welcome to Dufatanye Charity Foundation - Volunteer Registration' : 'Volunteer Status Update -
    Dufatanye Charity Foundation') . "

    Dear " . $volunteer_data['firstname'] . " " . $volunteer_data['lastname'] . ",

    " . ($is_new ? "
    Thank you for your interest in volunteering with Dufatanye Charity Foundation! We're excited to have you join our
    community of dedicated volunteers.

    Registration Details:
    - Name: " . $volunteer_data['firstname'] . " " . $volunteer_data['lastname'] . "
    - Email: " . $volunteer_data['email'] . "
    - Contact: " . $volunteer_data['contact'] . "
    - Registration Date: " . date('F j, Y') . "

    What happens next?
    - Our team will review your application
    - You'll receive an email with your approval status
    - Once approved, you can access your volunteer dashboard
    - Start making a difference in our community!
    " : "
    Your volunteer application status has been updated.

    Current Status: $status_text
    Volunteer ID: " . ($volunteer_data['roll'] ?? 'N/A') . "
    Update Date: " . date('F j, Y') . "
    ") . "

    " . ($status == 1 ? "
    Congratulations! You're now an approved volunteer.

    Next Steps:
    - Access your volunteer dashboard
    - Update your profile information
    - Join available volunteer activities
    - Track your contributions and impact

    Access your dashboard at: {$baseUrl}volunteer_login.php
    View your profile at: {$baseUrl}volunteer/profile.php
    " : ($status == 2 ? "
    We appreciate your interest in our organization. If you have any questions about this decision or would like to
    reapply in the future, please don't hesitate to contact us.
    " : "
    Your application is currently under review. We'll notify you as soon as we have an update.
    ")) . "

    Thank you for your understanding and continued support.

    Best regards,
    Dufatanye Charity Foundation Team";
	}
	function delete_volunteer()
	{
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `volunteer_list` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " volunteer has been deleted successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_shelter()
	{
		extract($_POST);

		// Validate required fields
		if (empty($volunteer_id)) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Volunteer ID is required.";
			return json_encode($resp);
		}

		if (empty($activity_id)) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Activity is required.";
			return json_encode($resp);
		}

		if (empty($year)) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Event date is required.";
			return json_encode($resp);
		}

		// Validate that the date is not in the past (for new records only)
		if (empty($id)) {
			$today = date('Y-m-d');
			if ($year < $today) {
				$resp['status'] = 'failed';
				$resp['msg'] = "Cannot select past dates for new assignments. Please choose a future date.";
				return json_encode($resp);
			}
		}

		// Set default values for missing fields
		if (empty($s)) {
			$s = 'General Session';
		}

		if (empty($years)) {
			$years = '';
		}

		if (!isset($status)) {
			$status = 1;
		}

		if (!isset($end_status)) {
			$end_status = 0;
		}

		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if (!empty($data))
					$data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}

		// Check if this volunteer is already assigned to this activity on the same date
		if (empty($id)) {
			$check_sql = "SELECT id FROM `volunteer_history` WHERE volunteer_id = '{$volunteer_id}' AND activity_id = '{$activity_id}' AND year = '{$year}'";
			$check_result = $this->conn->query($check_sql);
			if ($check_result->num_rows > 0) {
				$resp['status'] = 'failed';
				$resp['msg'] = "This volunteer is already assigned to this activity on the selected date.";
				return json_encode($resp);
			}
		}

		$is_new_assignment = empty($id);

		if (empty($id)) {
			$sql = "INSERT INTO `volunteer_history` set {$data} ";
		} else {
			$sql = "UPDATE `volunteer_history` set {$data} where id = '{$id}' ";
		}

		$save = $this->conn->query($sql);
		if ($save) {
			$resp['status'] = 'success';
			if (empty($id)) {
				$resp['msg'] = "Volunteer activity assignment has been added successfully.";
				$assignment_id = $this->conn->insert_id;

				// Send notification for new assignments only
				if ($is_new_assignment) {
					$this->sendVolunteerAssignmentNotification($assignment_id);
				}
			} else {
				$resp['msg'] = "Volunteer activity assignment has been updated successfully.";
			}
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred while saving the assignment.";
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		if ($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}

	/**
	 * Send volunteer assignment notification
	 */
	private function sendVolunteerAssignmentNotification($assignmentId)
	{
		try {
			// Include the MessagingService
			require_once __DIR__ . '/MessagingService.php';
			$messagingService = new MessagingService();

			// Send notification
			$result = $messagingService->sendVolunteerAssignmentNotification($assignmentId);

			if ($result['success']) {
				error_log("Volunteer assignment notification sent successfully. Email: " . ($result['email_sent'] ? 'Yes' : 'No') . ", SMS: " . ($result['sms_sent'] ? 'Yes' : 'No'));
			} else {
				error_log("Failed to send volunteer assignment notification: " . $result['message']);
			}

			return $result;

		} catch (Exception $e) {
			error_log("Error in sendVolunteerAssignmentNotification: " . $e->getMessage());
			return ['success' => false, 'message' => $e->getMessage()];
		}
	}
	function delete_shelter()
	{
		extract($_POST);

		if (empty($id)) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Record ID is required.";
			return json_encode($resp);
		}

		$get = $this->conn->query("SELECT * FROM `volunteer_history` where id = '{$id}'");
		if ($get->num_rows > 0) {
			$res = $get->fetch_array();
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Record not found.";
			return json_encode($resp);
		}

		$del = $this->conn->query("DELETE FROM `volunteer_history` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Volunteer activity assignment has been deleted successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred while deleting the record.";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function update_volunteer_status()
	{
		extract($_POST);

		$update = $this->conn->query("UPDATE `volunteer_list` set status = '{$status}' where id = '{$id}'");
		if ($update) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Status has been saved successfully.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_topic':
		echo $Master->save_topic();
		break;
	case 'delete_topic':
		echo $Master->delete_topic();
		break;
	case 'upload_files':
		echo $Master->upload_files();
		break;
	case 'save_blog':
		echo $Master->save_blog();
		break;
	case 'delete_blog':
		echo $Master->delete_blog();
		break;

	case 'save_event':
		echo $Master->save_event();
		break;
	case 'delete_event':
		echo $Master->delete_event();
		break;
	case 'save_donation':
		echo $Master->save_donation();
		break;
	case 'save_cause':
		echo $Master->save_cause();
		break;
	case 'delete_img':
		echo $Master->delete_img();
		break;


	case 'save_program':
		echo $Master->save_program();
		break;
	case 'delete_program':
		echo $Master->delete_program();
		break;
	case 'save_activity':
		echo $Master->save_activity();
		break;
	case 'delete_activity':
		echo $Master->delete_activity();
		break;
	case 'save_volunteer':
		echo $Master->save_volunteer();
		break;
	case 'delete_volunteer':
		echo $Master->delete_volunteer();
		break;
	case 'save_shelter':
		echo $Master->save_shelter();
		break;
	case 'delete_shelter':
		echo $Master->delete_shelter();
		break;
	case 'update_volunteer_status':
		echo $Master->update_volunteer_status();
		break;
	case 'send_volunteer_email':
		echo $Master->send_volunteer_email();
		break;

	default:

		break;
}