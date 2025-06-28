<style>
    #uni_modal .modal-content>.modal-footer,#uni_modal .modal-content>.modal-header{
        display:none;
    }
</style>
<div class="container-fluid">
<body class="hold-transition login-page  dark-mode"> 
    
    <div class="row">
    <h3 class="float-right">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
    </h3>
    <div class="col-lg-12">
  <h3 class="text-center dark-mode">Request / Access / Monitor</h3>
  <hr>

  <form action="" id="login-form">
    <div class="form-row justify-content-center text-center text-dark">

      <!-- Volunteer Now -->
      <div class="col-12 col-sm-6 col-md-4 mb-4">
        <a class="btn btn-primary w-100 py-3" href="registration.php">
          <img src="img/add.png" alt="Volunteer" style="width: 50px; margin-bottom: 10px;">
          <br><strong>Volunteer !</strong>
        </a>
      </div>

      <!-- Track Progress -->
      <div class="col-12 col-sm-6 col-md-4 mb-4">
        <a class="btn btn-primary w-100 py-3" href="volunteer/index.php">
          <img src="img/login.png" alt="Progress" style="width: 50px; margin-bottom: 10px;">
          <br><strong>Track Progress</strong>
        </a>
      </div>

      <!-- Admin / Manager -->
      <div class="col-12 col-sm-6 col-md-4 mb-4">
        <a class="btn btn-primary w-100 py-3" href="admin/index.php">
          <img src="img/admin.png" alt="Admin" style="width: 50px; margin-bottom: 10px;">
          <br><strong>Administrator</strong>
        </a>
      </div>

    </div>
  </form>
</div>

        <!--div class="col-lg-12">
            <h3 class="text-center dark-mode">Request/ Access/ Monitor </h3>
            <hr>
            <form action="" id="login-form">
                <div class="form-group">
<!---                    <label for="" class="control-label">Email</label>
                    <input type="email" class="form-control form" name="email" required>
                </div>
                <div class="form-group">
                    <label for="" class="control-label">Password</label>
                    <input type="password" class="form-control form" name="password" required>
                </div> ----->
                <!--div class="form-group flex justify-content-between text-dark">
                <a class="btn btn-primary" href="registration.php"><img src="img/add.png" style="width: 60px;margin-bottom:-25px;color: #fff;"><br><br><b>Volunteer Now!</a>
                <a href="javascript:void()" id="create_account"></a>
            
                <a class="btn btn-primary" href="volunteer/index.php"><img src="img/login.png" style="width: 60px;margin-bottom:-25px;color: #fff;"><br><br><b>Track Progress</a>
                    <a href="javascript:void()" id="login_refugee"></a>   
                
                   <a class="btn btn-primary" href="admin/index.php"><img src="img/admin.png" style="width: 60px;margin-bottom:-25px;color: #fff;"><br><br><b>Admin/Manager</a>
                    <a href="javascript:void()" id="login_admin"></a>
                    
                    <!--<button class="btn btn-primary btn-flat">Login</button>-->
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#create_account').click(function(){
            uni_modal("","registration.php","mid-large")
        })
        $('#refugee_admin').click(function(){
            uni_modal("","Volunteer/index.php","mid-large")
        }) 
        $('#login_admin').click(function(){
            uni_modal("","admin/index.php","mid-large")
        })
        $('#login-form').submit(function(e){
            e.preventDefault();
             start_loader()
            if($('.err-msg').length > 0)
                $('.err-msg').remove();
            $.ajax({
                url:_base_url_+"classes/Login.php?f=login_user",
                method:"POST",
                data:$(this).serialize(),
                dataType:"json",
                error:err=>{
                    console.log(err)
                    alert_toast("an error occured",'error')
                    end_loader()
                },
                success:function(resp){
                    if(typeof resp == 'object' && resp.status == 'success'){
                        alert_toast("Login Successfully",'success')
                        setTimeout(function(){
                            location.reload()
                        },2000)
                    }else if(resp.status == 'incorrect'){
                        var _err_el = $('<div>')
                            _err_el.addClass("alert alert-danger err-msg").text("Incorrect Credentials.")
                        $('#login-form').prepend(_err_el)
                        end_loader()
                        
                    }else{
                        console.log(resp)
                        alert_toast("an error occured",'error')
                        end_loader()
                    }
                }
            })
        })
    })
</script>