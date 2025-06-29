<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("SELECT * from `events` where id = '{$_GET['id']}' ");
	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			$$k = $v;
		}
	}
}
?>

<style>
	img#cimg {
		height: 20vh;
		width: 15vw;
		object-fit: cover;
		object-position: center top;
		border: 2px dashed #ddd;
		border-radius: 8px;
		transition: all 0.3s ease;
	}

	img#cimg:hover {
		border-color: #007bff;
		transform: scale(1.02);
	}

	.image-upload-area {
		border: 2px dashed #ddd;
		border-radius: 8px;
		padding: 20px;
		text-align: center;
		background: #f8f9fa;
		transition: all 0.3s ease;
	}

	.image-upload-area:hover {
		border-color: #007bff;
		background: #f0f8ff;
	}

	.image-upload-area.has-image {
		border-style: solid;
		border-color: #28a745;
		background: #f8fff8;
	}

	.custom-file-label {
		cursor: pointer;
	}

	.custom-file-label:hover {
		background-color: #e9ecef;
	}
</style>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update " : "Create New " ?> Event</h3>
	</div>
	<div class="card-body">
		<form action="" id="event-form">
			<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
			<div class="form-group">
				<label for="schedule" class="control-label">Date Schedule</label>
				<input type="date" class="form-control form" required name="schedule"
					value="<?php echo isset($schedule) ? $schedule : '' ?>">
			</div>
			<div class="form-group">
				<label for="title" class="control-label">Title</label>
				<input type="text" class="form-control form" required name="title"
					value="<?php echo isset($title) ? $title : '' ?>">
			</div>
			<div class="form-group">
				<label for="description" class="control-label">Description</label>
				<textarea rows="2" class="form-control form" required
					name="description"><?php echo isset($description) ? stripslashes($description) : '' ?></textarea>
			</div>
			<div class="form-group">
				<label for="" class="control-label">Thumbnail</label>
				<div
					class="image-upload-area <?php echo (isset($img_path) && !empty($img_path)) ? 'has-image' : ''; ?>">
					<div class="custom-file">
						<input type="file" class="custom-file-input rounded-circle" id="customFile" name="img"
							onchange="displayImg(this,$(this))" accept="image/jpeg,image/jpg,image/png,image/gif">
						<label class="custom-file-label" for="customFile">Choose file</label>
					</div>
					<small class="text-muted d-block mt-2">Accepted formats: JPG, PNG, GIF. Maximum size: 2MB</small>
				</div>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img align="center" src="<?php echo validate_image(isset($img_path) ? $img_path : '') ?>" alt=""
					id="cimg" class="img-fluid img-thumbnail">
			</div>
			<?php if (isset($img_path) && !empty($img_path)): ?>
				<div class="form-group text-center">
					<button type="button" class="btn btn-sm btn-danger" onclick="removeImage()">
						<i class="fa fa-trash"></i> Remove Image
					</button>
				</div>
			<?php endif; ?>
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="event-form">Save</button>
		<a class="btn btn-flat btn-default" href="?page=events">Cancel</a>
	</div>
</div>
<script>
	function displayImg(input, _this) {
		if (input.files && input.files[0]) {
			var file = input.files[0];

			// Validate file type
			var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
			if (!allowedTypes.includes(file.type)) {
				alert('Please select a valid image file (JPG, PNG, or GIF)');
				input.value = '';
				return;
			}

			// Validate file size (2MB)
			var maxSize = 2 * 1024 * 1024; // 2MB
			if (file.size > maxSize) {
				alert('File size is too large. Please select an image smaller than 2MB.');
				input.value = '';
				return;
			}

			var reader = new FileReader();
			reader.onload = function (e) {
				$('#cimg').attr('src', e.target.result);
				_this.siblings('.custom-file-label').html(file.name);
				$('.image-upload-area').addClass('has-image');
			}
			reader.readAsDataURL(file);
		}
	}

	function removeImage() {
		if (confirm('Are you sure you want to remove this image?')) {
			var eventId = $('input[name="id"]').val();
			if (!eventId) {
				alert('Event ID not found. Please save the event first.');
				return;
			}

			start_loader();
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=remove_event_image",
				data: {
					id: eventId
				},
				method: 'POST',
				dataType: 'json',
				error: function (err) {
					console.log(err);
					alert_toast("An error occurred", 'error');
					end_loader();
				},
				success: function (resp) {
					if (resp.status == 'success') {
						$('#cimg').attr('src', '<?php echo validate_image("") ?>');
						$('#customFile').val('');
						$('.custom-file-label').html('Choose file');
						$('.image-upload-area').removeClass('has-image');
						alert_toast(resp.msg, 'success');
					} else {
						alert_toast(resp.msg || "Failed to remove image", 'error');
					}
					end_loader();
				}
			});
		}
	}

	$(document).ready(function () {
		$('#event-form').submit(function (e) {
			e.preventDefault();
			var _this = $(this)
			$('.err-msg').remove();
			start_loader();
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=save_event",
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				type: 'POST',
				dataType: 'json',
				error: err => {
					console.log(err)
					alert_toast("An error occured", 'error');
					end_loader();
				},
				success: function (resp) {
					if (typeof resp == 'object' && resp.status == 'success') {
						location.href = "./?page=events";
					} else if (resp.status == 'failed' && !!resp.msg) {
						var el = $('<div>')
						el.addClass("alert alert-danger err-msg").text(resp.msg)
						_this.prepend(el)
						el.show('slow')
						$("html, body").animate({
							scrollTop: _this.closest('.card').offset().top
						}, "fast");
						end_loader()
					} else {
						alert_toast("An error occured", 'error');
						end_loader();
						console.log(resp)
					}
				}
			})
		})
	})
</script>