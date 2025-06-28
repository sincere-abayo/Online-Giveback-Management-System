<?php include('../config.php'); ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<head>
<link rel="shortcut icon" href="../rrms1.png" type="image/png">
<body class="hold-transition login-page  dark-mode">
  <body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed dark-mode sidebar-mini-md sidebar-mini-xs" data-new-gr-c-s-check-loaded="14.991.0" data-gr-ext-installed="" style="height: auto;">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, shrink-to-fit=no">
  <title>GMS</title>
  <link rel="stylesheet" type="text/css" href="inc/.css" media="all">
  <link rel="icon" href="../uploads/1744824240_icon.png" />
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { 
    background-color: black; 
    color: white;
} 
    </style>
</head>

    <div class="wrapper">
     <section class="content text-dark  bg-dark">
       <div class="container-fluid">
       <div class="card card-outline card-primary">
    <div class="card-header text-center mb-5">
      <a href="./" class="h1"><b>Volunteer Status</b></a>
    </div>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
            <div class="login-box">
                <div class="card mt-5">
                    <div class="card-header text-center">
                        <h4>Follow-up for more details</h4>
                    </div>
                    <div class="card-body">

                        <form action="" method="GET">
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="text" name="volunteer_id" value="<?php if(isset($_GET['volunteer_id'])){echo $_GET['volunteer_id'];} ?>" class="form-control" placeholder="Contact">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success">Check</button>
                                </div>
                            </div>
                        </form>

                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                                <?php 
                                    $con = mysqli_connect("localhost","root","","gms");

                                    if(isset($_GET['volunteer_id']))
                                    {
                                        $volunteer_id = $_GET['volunteer_id'];

                                        $query = "SELECT * FROM volunteer_list WHERE contact='$volunteer_id' ";
                                        $query_run = mysqli_query($con, $query);

                                        if(mysqli_num_rows($query_run) > 0)
                                        {
                                            foreach($query_run as $row)
                                            {
                                                ?>
                                                <div class="form-group mb-3">
                                                    <label for="">Dear,</label>
                                                    <input type="text" value="<?= $row['firstname']; ?>  <?= $row['middlename']; ?>  <?= $row['lastname']; ?>" class="form-control"  disabled>
                                                    </div>
                                                <div class="form-group mb-3">
                                                    <label for="">E-mail & Contact</label>
                                                    <input type="text" value="<?= $row['email']; ?>/<?= $row['contact']; ?>" class="form-control"disabled>
                                                </div>
                                                <?php 
    // Convert numeric status to label
    $status = $row['status'];
    $status_label = '';
    switch ($status){
        case 0:
            $status_label = 'Pending';
            $status_class = 'badge badge-danger bg-gradient-danger';
            break;
        case 1:
            $status_label = 'Accepted';
            $status_class = 'badge badge-success bg-gradient-success';
            break;
        case 2:
            $status_label = 'Denied';
            $status_class = 'badge badge-warning bg-gradient-warning';
            break;
        default:
            $status_label = 'Unknown';
            $status_class = 'badge badge-secondary';
    }
?>
<div class="form-group mb-3">
    <label for="status">Status</label><br> <small>
        <span class="rounded-pill <?= $status_class ?> px-3"><?= $status_label ?></span>
    </small></small>
    <input type="hidden" value="<?= $status_label ?>" class="form-control" disabled>
   
</div>

                                                <div class="form-group mb-3">
                                                    <label for="">Response Comment </label>
                                                    <input type="text" value="<?= $row['comment']; ?>" class="form-control" disabled>
                                                </div>
                                                <?php
                                            }
                                        }
                                        else
                                        {
                                            echo "No Record Found";
                                        }
                                    }
                                   
                                ?>
                                 <b> <div class="col-6 mb-5">
                                      <a class="btn btn-primary " href="./index1.php">Use E-mail</a> <img src="../img/gmail.png" style="width: 30px;margin-bottom:-2px;color: #fff;"> OR
                                      
                                      <a class="btn btn-primary " href="./index.php">Use Contact</a> <img src="../img/passport.png" style="width: 30px;margin-bottom:-2px;color: #fff;"> 
            </div>
<div class="row">
          <div class="col-8 mb-5">
            <b><img src="../img/back.png" style="width: 30px;margin-bottom:-2px;color: #fff;"><a href="../index.php">Go Back</a> 
          </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php 
include("inc/footer.php");
    ?>
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
  })
</script>
</body>
</html>