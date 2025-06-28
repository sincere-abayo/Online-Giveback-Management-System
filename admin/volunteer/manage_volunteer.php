<?php
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `volunteer_list` where id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        $res = $qry->fetch_array();
        foreach($res as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }
}
?>
<div class="content py-3">
    <div class="card card-outline card-primary shadow rounded-0">
        <div class="card-header">
            <h3 class="card-title"><b><?= isset($id) ? "Update volunteer Details - ". $id : "New volunteer" ?></b></h3>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <form action="" id="volunteer_form">
                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                    <fieldset class="border-bottom">
                        <div class="row">
                            </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="firstname" class="control-label">First Name</label>
                                <input type="text" name="firstname" id="firstname" value="<?= isset($firstname) ? $firstname : "" ?>" class="form-control form-control-sm rounded-0" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="middlename" class="control-label">Middle Name</label>
                                <input type="text" name="middlename" id="middlename" value="<?= isset($middlename) ? $middlename : "" ?>" class="form-control form-control-sm rounded-0" placeholder='optional'>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="lastname" class="control-label">Last Name</label>
                                <input type="text" name="lastname" id="lastname" autofocus value="<?= isset($lastname) ? $lastname : "" ?>" class="form-control form-control-sm rounded-0" required>
                            </div>
                            
                        
                            <div class="form-group col-md-4">
                                <label for="contact" class="control-label">Contact #</label>
                                <input type="text" name="contact" id="contact" value="<?= isset($contact) ? $contact : "" ?>" class="form-control form-control-sm rounded-0" required>
                        </div>
                        
                        <div class="form-group col-md-4">
                                <label for="email" class="control-label">E-mail</label>
                                <input type="text" name="email" id="email" autofocus value="<?= isset($email) ? $email : "" ?>" class="form-control form-control-sm rounded-0" required>
                            </div>
                        
                            <div class="form-group col-md-4">
                                <label for="motivation" class="control-label">Motivation</label>
                                <textarea rows="" name="motivation" id="motivation" class="form-control form-control-sm rounded-0" required><?= isset($motivation) ? $motivation: "" ?></textarea>
                            </div>
                            </div>
                            <div class="container-fluid">
    <form action="" id="status-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="form-group col-md-4">
            <label for="status" class="control-label">Status</label>
            <select id="status" name ="status" class="form-control form-control-border form-control-sm" required>
                <option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>Pending</option>
                <option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Accepted</option>
                <option value="2" <?= isset($status) && $status == 2 ? 'selected' : '' ?>>Denied</option>
            </select>
        </div>
                            <div class="form-group col-md-4">
                                <label for="comment" class="control-label">feedback comment</label>
                                <textarea rows="" name="comment" id="comment" class="form-control form-control-sm rounded-0" ><?= isset($comment) ? $comment : "" ?></textarea>
                            </div>
                    </fieldset>
                </form>
            </div>
        </div>
        <div class="card-footer text-right">
            <button class="btn btn-flat btn-primary btn-sm" type="submit" form="volunteer_form">Save Volunteer Details</button>
            <a href="./?page=volunteer" class="btn btn-flat btn-default border btn-sm">Cancel</a>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#volunteer_form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_volunteer",
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
                        location.href="./?page=volunteer/view_volunteer&id="+resp.sid;
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