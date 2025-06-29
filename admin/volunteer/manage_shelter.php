<?php
include('../../config.php');
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM `volunteer_history` where id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        $res = $qry->fetch_array();
        foreach ($res as $k => $v) {
            if (!is_numeric($k))
                $$k = $v;
        }
    } else {
        echo "<center><small class='text-muted'>Unknown volunteer_history ID.</small></center>";
        exit;
    }
}
?>
<style>
img#cimg {
    height: 17vh;
    width: 25vw;
    object-fit: scale-down;
}
</style>
<div class="container-fluid">
    <form action="" id="shelter-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name="volunteer_id"
            value="<?php echo isset($_GET['volunteer_id']) ? $_GET['volunteer_id'] : '' ?>">

        <div class="row">
            <div class="col-md-6 form-group">
                <label for="activity_id" class="control-label">Activity</label>
                <select name="activity_id" id="activity_id"
                    class="form-control form-control-sm form-control-border rounded-0 select2" required>
                    <option value="" disabled <?= !isset($activity_id) ? 'selected' : '' ?>>Select Activity</option>
                    <?php
                    $activity = $conn->query("SELECT c.*, d.name as program FROM `activity_list` c inner join `program_list` d on c.program_id = d.id where c.delete_flag = 0 and c.status = 1 " . (isset($activity_id) ? " or c.id = '{$activity_id}' " : "") . " order by d.name asc, c.name asc ");
                    while ($row = $activity->fetch_assoc()):
                        ?>
                    <option value="<?= $row['id'] ?>"
                        <?= isset($activity_id) && $activity_id == $row['id'] ? "selected" : '' ?>>
                        <?= $row['program'] . " - " . $row['name'] ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="year" class="control-label">Event Date</label>
                <input type="date" id="year" name="year" value="<?= isset($year) ? $year : '' ?>"
                    class="form-control form-control-border form-control-sm" min="<?= date('Y-m-d') ?>" required>
                <small class="form-text text-muted">Cannot select past dates</small>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label for="s" class="control-label">Session/Period</label>
                <input type="text" id="s" name="s" value="<?= isset($s) ? $s : '' ?>"
                    class="form-control form-control-border form-control-sm"
                    placeholder="e.g., Morning Session, Afternoon Session" required>
            </div>
            <div class="col-md-6 form-group">
                <label for="years" class="control-label">Additional Notes</label>
                <textarea id="years" name="years" class="form-control form-control-border form-control-sm" rows="3"
                    placeholder="Any additional notes or comments"><?= isset($years) ? $years : '' ?></textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label for="status" class="control-label">Status</label>
                <select id="status" name="status" class="form-control form-control-border form-control-sm" required>
                    <option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="end_status" class="control-label">End Status</label>
                <select id="end_status" name="end_status" class="form-control form-control-border form-control-sm">
                    <option value="0" <?= isset($end_status) && $end_status == 0 ? 'selected' : '' ?>>Ongoing</option>
                    <option value="1" <?= isset($end_status) && $end_status == 1 ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
        </div>
    </form>
</div>
<script>
$(function() {
    $('#uni_modal').on('shown.bs.modal', function() {
        $('#activity_id').focus();
        $('#activity_id').select2({
            placeholder: 'Please Select Activity',
            width: "100%",
            dropdownParent: $('#uni_modal')
        })

        // Set minimum date to today
        var today = new Date().toISOString().split('T')[0];
        $('#year').attr('min', today);

        // If editing and the date is in the past, allow it (for existing records)
        <?php if (isset($year) && !empty($year)): ?>
        var selectedDate = '<?= $year ?>';
        if (selectedDate < today) {
            $('#year').removeAttr('min');
            $('.form-text').text('Past date allowed for existing records');
        }
        <?php endif; ?>
    })

    // Date validation on change
    $('#year').on('change', function() {
        var selectedDate = $(this).val();
        var today = new Date().toISOString().split('T')[0];

        if (selectedDate < today) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Cannot select past dates</div>');
            }
            return false;
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    $('#uni_modal #shelter-form').submit(function(e) {
        e.preventDefault();
        var _this = $(this)

        // Validate date before submission
        var selectedDate = $('#year').val();
        var today = new Date().toISOString().split('T')[0];

        if (selectedDate < today) {
            alert('Cannot select past dates. Please choose a future date.');
            $('#year').focus();
            return false;
        }

        if (_this[0].checkValidity() == false) {
            _this[0].reportValidity();
            return false;
        }
        $('.pop-msg').remove()
        var el = $('<div>')
        el.addClass("pop-msg alert")
        el.hide()
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=save_shelter",
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
            success: function(resp) {
                if (resp.status == 'success') {
                    location.reload();
                } else if (!!resp.msg) {
                    el.addClass("alert-danger")
                    el.text(resp.msg)
                    _this.prepend(el)
                } else {
                    el.addClass("alert-danger")
                    el.text("An error occurred due to unknown reason.")
                    _this.prepend(el)
                }
                el.show('slow')
                $('html,body,.modal').animate({
                    scrollTop: 0
                }, 'fast')
                end_loader();
            }
        })
    })
})
</script>
</script>