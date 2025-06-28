<?php
include('../../config.php');
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `volunteer_history` where id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        $res = $qry->fetch_array();
        foreach($res as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }else{
        echo "<center><small class='text-muted'>Unkown volunteer_history ID.</small</center>";
        exit;
    }
}
?>
<style>
	img#cimg{
		height: 17vh;
		width: 25vw;
		object-fit: scale-down;
	}
</style>
<div class="container-fluid">
    <form action="" id="shelter-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name="volunteer_id" value="<?php echo isset($_GET['volunteer_id']) ? $_GET['volunteer_id'] : '' ?>">
        <div class="row">
            <div>
        </div>
            <div class="form-group col-md-6">
                </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="activity_id" class="control-label">Activity</label>
                <select name="activity_id" id="activity_id" class="form-control form-control-sm form-control-border rounded-0 select2" required>
                    <option <?= !isset($course_id) ? 'selected' : '' ?> disabled></option>
                    <?php 
                        $activity = $conn->query("SELECT c.*, d.name as program FROM `activity_list` c inner join `program_list` d on c.program_id = d.id where c.delete_flag = 0 and c.status = 1 ".(isset($activity_id) ? " or c.id = '{$activity_id}' " : "")." order by d.name asc, c.name asc ");
                        while($row = $activity->fetch_assoc()):
                    ?>
                    <option value="<?=  $row['id'] ?>" <?= isset($activity_id) && $activity_id == $row['id'] ? "selected" : '' ?>><?= $row['program']. " - " . $row['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="year" class="control-label">Event Date</label>
                <input type="date" id="year" name ="year" value="<?= isset($year) ? $year : '' ?>" class="form-control form-control-border form-control-sm" required>
            </div>
        </div>
        
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#uni_modal').on('shown.bs.modal',function(){
            $('#amount').focus();
            $('#activity_id').select2({
                placeholder:'Please Select Here',
                width:"100%",
                dropdownParent:$('#uni_modal')
            })
        })
        $('#uni_modal #shelter-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            if(_this[0].checkValidity() == false){
                _this[0].reportValidity();
                return false;
            }
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_shelter",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload();
                    }else if(!!resp.msg){
                        el.addClass("alert-danger")
                        el.text(resp.msg)
                        _this.prepend(el)
                    }else{
                        el.addClass("alert-danger")
                        el.text("An error occurred due to unknown reason.")
                        _this.prepend(el)
                    }
                    el.show('slow')
                    $('html,body,.modal').animate({scrollTop:0},'fast')
                    end_loader();
                }
            })
        })
    })
</script>