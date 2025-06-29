<style>
	.img-thumb-path {
		width: 100px;
		height: 80px;
		object-fit: scale-down;
		object-position: center center;
	}
</style>

<?php
// Handle approve/deny actions
if (isset($_POST['action']) && isset($_POST['volunteer_id'])) {
	$volunteer_id = $_POST['volunteer_id'];
	$action = $_POST['action'];
	$comment = $_POST['comment'] ?? '';

	// Get volunteer information
	$volunteer_sql = "SELECT * FROM volunteer_list WHERE id = ?";
	$volunteer_stmt = $conn->prepare($volunteer_sql);
	$volunteer_stmt->bind_param("i", $volunteer_id);
	$volunteer_stmt->execute();
	$volunteer = $volunteer_stmt->get_result()->fetch_assoc();

	if ($volunteer) {
		$status = ($action == 'approve') ? 1 : 2;
		$status_text = ($action == 'approve') ? 'Approved' : 'Denied';

		// Update volunteer status
		$update_sql = "UPDATE volunteer_list SET status = ?, comment = ?, date_updated = NOW() WHERE id = ?";
		$update_stmt = $conn->prepare($update_sql);
		$update_stmt->bind_param("isi", $status, $comment, $volunteer_id);

		if ($update_stmt->execute()) {
			// Send email notification
			$email_result = sendVolunteerStatusEmail($volunteer, $action, $comment);

			if ($email_result['success']) {
				$success_msg = "Volunteer $status_text successfully! Email notification sent.";
			} else {
				$success_msg = "Volunteer $status_text successfully! Email notification failed: " . $email_result['message'];
			}
		} else {
			$error_msg = "Error updating volunteer status: " . $update_stmt->error;
		}
		$update_stmt->close();
	}
	$volunteer_stmt->close();
}

// Function to send status update email
function sendVolunteerStatusEmail($volunteer, $action, $comment)
{
	try {
		// Check if PHPMailer is available
		if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
			require_once __DIR__ . '/../../vendor/autoload.php';

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
				$mail->addAddress($volunteer['email'], $volunteer['firstname'] . ' ' . $volunteer['lastname']);

				// Content
				$mail->isHTML(true);
				$mail->Subject = 'Volunteer Application ' . ucfirst($action) . ' - Dufatanye Charity Foundation';
				$mail->Body = getStatusEmailHTML($volunteer, $action, $comment);
				$mail->AltBody = getStatusEmailText($volunteer, $action, $comment);

				$mail->send();
				return ['success' => true, 'message' => 'Email sent successfully'];
			}
		}

		// Fallback to basic PHP mail
		$subject = 'Volunteer Application ' . ucfirst($action) . ' - Dufatanye Charity Foundation';
		$message = getStatusEmailHTML($volunteer, $action, $comment);

		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: Dufatanye Charity Foundation <dufatanyecharity@gmail.com>' . "\r\n";

		$success = mail($volunteer['email'], $subject, $message, $headers);

		return [
			'success' => $success,
			'message' => $success ? 'Email sent successfully' : 'Failed to send email'
		];

	} catch (Exception $e) {
		return ['success' => false, 'message' => 'Email Error: ' . $e->getMessage()];
	}
}

function getStatusEmailHTML($volunteer, $action, $comment)
{
	$status_text = ($action == 'approve') ? 'Approved' : 'Denied';
	$status_color = ($action == 'approve') ? '#28a745' : '#dc3545';
	$status_icon = ($action == 'approve') ? '✅' : '❌';
	$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF'])) . "/";

	return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Volunteer Application $status_text</title>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
            .email-container { max-width: 600px; margin: 0 auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); }
            .email-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 30px; text-align: center; }
            .email-body { padding: 40px 30px; }
            .status-banner { background: $status_color; color: white; padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0; }
            .info-item { background: #f8f9fa; padding: 15px; border-radius: 10px; margin: 10px 0; }
            .btn { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; margin: 10px 5px; }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='email-header'>
                <h1>Volunteer Application Update</h1>
                <p>Dufatanye Charity Foundation</p>
            </div>
            <div class='email-body'>
                <div class='status-banner'>
                    <h2>$status_icon Application $status_text</h2>
                    <p>Dear " . htmlentities($volunteer['firstname'] . ' ' . $volunteer['lastname']) . ",</p>
                </div>
                
                <p>Thank you for your interest in volunteering with Dufatanye Charity Foundation. We have reviewed your application and have an update for you.</p>
                
                <div class='info-item'>
                    <strong>Application Status:</strong> $status_text<br>
                    <strong>Volunteer ID:</strong> " . htmlentities($volunteer['roll']) . "<br>
                    <strong>Date:</strong> " . date('F j, Y') . "
                </div>
                
                " . (!empty($comment) ? "<div class='info-item'><strong>Admin Comment:</strong><br>" . htmlentities($comment) . "</div>" : "") . "
                
                " . ($action == 'approve' ? "
                <p><strong>Next Steps:</strong></p>
                <ul>
                    <li>You can now login to your volunteer dashboard</li>
                    <li>Access your profile and update information</li>
                    <li>Join available volunteer activities</li>
                    <li>Track your contributions and impact</li>
                </ul>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$baseUrl}volunteer_login.php' class='btn'>🔐 Access Dashboard</a>
                    <a href='{$baseUrl}volunteer/profile.php' class='btn'>👤 View Profile</a>
                </div>
                " : "
                <p>We appreciate your interest in our organization. If you have any questions about this decision or would like to reapply in the future, please don't hesitate to contact us.</p>
                ") . "
                
                <p>Thank you for your understanding and continued support.</p>
                
                <p>Best regards,<br>
                <strong>Dufatanye Charity Foundation Team</strong></p>
            </div>
        </div>
    </body>
    </html>";
}

function getStatusEmailText($volunteer, $action, $comment)
{
	$status_text = ($action == 'approve') ? 'Approved' : 'Denied';
	$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF'])) . "/";

	return "
Volunteer Application $status_text - Dufatanye Charity Foundation

Dear " . $volunteer['firstname'] . " " . $volunteer['lastname'] . ",

Thank you for your interest in volunteering with Dufatanye Charity Foundation. We have reviewed your application and have an update for you.

Application Status: $status_text
Volunteer ID: " . $volunteer['roll'] . "
Date: " . date('F j, Y') . "

" . (!empty($comment) ? "Admin Comment: $comment\n\n" : "") . "

" . ($action == 'approve' ? "
Next Steps:
- You can now login to your volunteer dashboard
- Access your profile and update information  
- Join available volunteer activities
- Track your contributions and impact

Access your dashboard at: {$baseUrl}volunteer_login.php
View your profile at: {$baseUrl}volunteer/profile.php
" : "
We appreciate your interest in our organization. If you have any questions about this decision or would like to reapply in the future, please don't hesitate to contact us.
") . "

Thank you for your understanding and continued support.

Best regards,
Dufatanye Charity Foundation Team";
}
?>

<div class="card card-outline card-primary rounded-0 shadow">
	<div class="card-header">
		<h3 class="card-title">Volunteer Applications</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<?php if (isset($success_msg)): ?>
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					<i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			<?php endif; ?>

			<?php if (isset($error_msg)): ?>
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<i class="fas fa-exclamation-triangle"></i> <?php echo $error_msg; ?>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			<?php endif; ?>

			<div class="container-fluid">
				<table class="table table-bordered table-hover table-striped">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="15%">
						<col width="20%">
						<col width="15%">
						<col width="30%">
					</colgroup>
					<thead>
						<tr class="bg-gradient-dark text-light">
							<th>#</th>
							<th>Date Created</th>
							<th>Volunteer ID</th>
							<th>Name</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						$qry = $conn->query("SELECT *,concat(lastname,', ',firstname,' ',middlename ) as fullname from `volunteer_list` where status = '0' order by concat(lastname,', ',firstname,' ',middlename) asc ");
						while ($row = $qry->fetch_assoc()):
							?>
							<tr>
								<td class="text-center"><?php echo $i++; ?></td>
								<td class=""><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
								<td class="">
									<p class="m-0 truncate-1"><?php echo $row['roll'] ?: 'N/A' ?></p>
								</td>
								<td class="">
									<p class="m-0 truncate-1"><?php echo $row['fullname'] ?></p>
								</td>
								<td class="text-center">
									<?php
									switch ($row['status']) {
										case 0:
											echo '<span class="rounded-pill badge badge-danger bg-gradient-danger px-3">Pending</span>';
											break;
										case 1:
											echo '<span class="rounded-pill badge badge-success bg-gradient-success px-3">Accepted</span>';
											break;
										case 2:
											echo '<span class="rounded-pill badge badge-warning bg-gradient-warning px-3">Denied</span>';
											break;
										case 3:
											echo '<span class="rounded-pill badge badge-info bg-gradient-info px-3">Dismissed</span>';
											break;
									}
									?>
								</td>
								<td align="center">
									<a href="./?page=volunteer/view_volunteer&id=<?= $row['id'] ?>"
										class="btn btn-flat btn-default btn-sm border"><i class="fa fa-eye"></i> View</a>
									<button type="button" class="btn btn-flat btn-success btn-sm border"
										onclick="approveVolunteer(<?= $row['id'] ?>, '<?= htmlspecialchars($row['fullname']) ?>')"><i
											class="fa fa-check"></i> Approve</button>
									<button type="button" class="btn btn-flat btn-danger btn-sm border"
										onclick="denyVolunteer(<?= $row['id'] ?>, '<?= htmlspecialchars($row['fullname']) ?>')"><i
											class="fa fa-times"></i> Deny</button>
								</td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Approve/Deny Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-labelledby="actionModalLabel"
	aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="actionModalLabel">Volunteer Action</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST" action="">
				<div class="modal-body">
					<input type="hidden" name="volunteer_id" id="volunteer_id">
					<input type="hidden" name="action" id="action_type">

					<div class="form-group">
						<label for="volunteer_name">Volunteer Name:</label>
						<input type="text" class="form-control" id="volunteer_name" readonly>
					</div>

					<div class="form-group">
						<label for="comment">Comment (Optional):</label>
						<textarea class="form-control" id="comment" name="comment" rows="3"
							placeholder="Add a comment for the volunteer..."></textarea>
					</div>

					<div class="alert alert-info">
						<i class="fa fa-info-circle"></i>
						<strong>Note:</strong> An email notification will be sent to the volunteer automatically.
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary" id="confirmBtn">Confirm</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
		$('.table td, .table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable({
			columnDefs: [
				{ orderable: false, targets: 5 }
			],
		});
	})

	function approveVolunteer(id, name) {
		$('#volunteer_id').val(id);
		$('#volunteer_name').val(name);
		$('#action_type').val('approve');
		$('#actionModalLabel').text('Approve Volunteer');
		$('#confirmBtn').removeClass('btn-danger').addClass('btn-success').html('<i class="fa fa-check"></i> Approve');
		$('#actionModal').modal('show');
	}

	function denyVolunteer(id, name) {
		$('#volunteer_id').val(id);
		$('#volunteer_name').val(name);
		$('#action_type').val('deny');
		$('#actionModalLabel').text('Deny Volunteer');
		$('#confirmBtn').removeClass('btn-success').addClass('btn-danger').html('<i class="fa fa-times"></i> Deny');
		$('#actionModal').modal('show');
	}

	function delete_volunteer($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_volunteer",
			method: "POST",
			data: { id: $id },
			dataType: "json",
			error: err => {
				console.log(err)
				alert_toast("An error occured.", 'error');
				end_loader();
			},
			success: function (resp) {
				if (typeof resp == 'object' && resp.status == 'success') {
					location.reload();
				} else {
					alert_toast("An error occured.", 'error');
					end_loader();
				}
			}
		})
	}
</script>