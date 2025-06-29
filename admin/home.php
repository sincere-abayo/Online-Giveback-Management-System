<h1 class="text-dark">Welcome to <?php echo $_settings->info('name') ?></h1>
<hr class="bg-dark">
<div class="row text-dark">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-light elevation-1"><i class="fas fa-donate"></i></span>
                <div class="info-box-content">
                <span class="info-box-text">Total Donations</span>
                <span class="info-box-number text-right">
                <?php
  
      $sql = "SELECT COUNT(*) AS donation FROM donations ";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $donation = $row["donation"];
        echo number_format($donation);
      } else {
        echo "No Donation found .";
      }

      $result->free_result();
    ?>
                  <?php ?>
                </span>
              </div>
              </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-user-plus"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Applications</span>
                <span class="info-box-number text-right">
                  <?php 
                    $request = $conn->query("SELECT id FROM `volunteer_list` where status = 'O' ")->num_rows;
                    echo number_format($request);
                  ?>
                </span>
              </div>
              </div>
              </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-check"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Admitted</span>
                <span class="info-box-number text-right">
                  <?php 
                    $request = $conn->query("SELECT id FROM `volunteer_list` where status = '1' ")->num_rows;
                    echo number_format($request);
                  ?>
                </span>
              </div>
              </div>
              </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-ban"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Rejected</span>
                <span class="info-box-number text-right">
                  <?php 
                    $request = $conn->query("SELECT id FROM `volunteer_list` where status = '2' ")->num_rows;
                    echo number_format($request);
                  ?>
                </span>
              </div>
              </div>
              </div>

      
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-calendar-day"></i></span>

              <div class="info-box-content">
                <span class="info-box-text"> Events </span>
                <span class="info-box-number text-right">
                <?php 
                    $event = $conn->query("SELECT id FROM `events` where date(schedule) >= '".date('Y-m-d')."' ")->num_rows;
                    echo number_format($event);
                  ?>
                </span>
              </div>
              </div>
          </div>
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-map"></i></span>

              <div class="info-box-content">
                <span class="info-box-text"> Programs</span>
                <span class="info-box-number text-right">
                <?php 
                    $program = $conn->query("SELECT id FROM `program_list` where delete_flag= '0' ")->num_rows;
                    echo number_format($program);
                  ?>
                </span>
              </div>
            </div>
          </div>
        
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <div class="info-box bg-gradient-light shadow">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-home"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Total Activities</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `activity_list` where delete_flag= 0 and `status` = 1 ")->num_rows;
                ?>
            </span>
            </div>
            </div>
            
          </div>
        
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <div class="info-box bg-gradient-light shadow">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-image"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Total Images in Gallery</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `gallery` where delete_flag= 0 ")->num_rows;
                ?>
            </span>
            </div>
            </div>
            </div>
            <hr>
<div class="row">
    <div class="col-md-12">
        <img src="<?= validate_image($_settings->info('cover')) ?>" alt="Website Cover" class="img-fluid border w-100" id="website-cover">
    </div>
</div>
