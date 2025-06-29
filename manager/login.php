<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition login-page  -mode">
   <style>
    html, body{
      height:calc(100%) !important;
      width:calc(100%) !important;
    }
    body{
      background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
      background-size:cover;
      background-repeat:no-repeat;
    }
    .login-box .card-header {
      background: linear-gradient(45deg, #28a745, #20c997) !important;
    }
    .btn-primary {
      background: linear-gradient(45deg, #28a745, #20c997) !important;
      border-color: #28a745 !important;
    }
    .btn-primary:hover {
      background: linear-gradient(45deg, #218838, #1e7e34) !important;
    }
    </style>
  <script>
    start_loader()
  </script>
  
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-success">
    <div class="card-header text-center">
      <a href="./" class="h1"><b>Manager</b></a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in as Manager</p>
      <div id="login-alert" style="display:none;"></div>
      <form id="login-frm" action="" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" placeholder="Username" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <a href="<?php echo base_url ?>">Go Back</a>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-success btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <!-- /.social-auth-links -->

      <!-- <p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p> -->
      
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function(){
    end_loader();
    
    $('#login-frm').submit(function(e){
      e.preventDefault();
      start_loader();
      $('#login-alert').hide();
      
      $.ajax({
        url: '../classes/Login.php?f=login',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        error: err => {
          console.error('AJAX error:', err);
          $('#login-alert').html('<div class="alert alert-danger">A server error occurred. Please try again later.</div>').show();
          end_loader();
        },
        success: function(resp){
          console.log('Login response:', resp);
          if(resp.status == 'success'){
            // Check if user is a manager (type == 2)
            try {
              var userType = null;
              if(window.localStorage){
                // Try to get from sessionStorage if available
                userType = window.sessionStorage.getItem('type');
              }
              // Fallback: check via AJAX (optional, or just redirect and let sess_auth handle)
            } catch(e) {}
            // Always redirect, let sess_auth.php handle type check
            location.href = './';
          }else if(resp.status == 'incorrect'){
            $('#login-alert').html('<div class="alert alert-danger">Incorrect username or password.</div>').show();
          }else{
            $('#login-alert').html('<div class="alert alert-danger">An unknown error occurred.</div>').show();
          }
          end_loader();
        }
      });
    });
  })
</script>
</body>
</html>