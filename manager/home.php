<h1 class="text-dark">Manager Dashboard - <?php echo $_settings->userdata('firstname') . ' ' . $_settings->userdata('lastname'); ?></h1>
<hr class="bg-success">
<div class="row text-dark">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-donate"></i></span>
                <div class="info-box-content">
                <span class="info-box-text">Pending Donations</span>
                <span class="info-box-number text-right">
                <?php
  
      $sql = "SELECT COUNT(*) AS donation FROM donations WHERE status = 'pending' ";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $donation = $row["donation"];
        echo number_format($donation);
      } else {
        echo "0";
      }

      $result->free_result();
    ?>
                </span>
              </div>
              </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-plus"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Pending Applications</span>
                <span class="info-box-number text-right">
                  <?php 
                    $request = $conn->query("SELECT id FROM `volunteer_list` where status = '0' ")->num_rows;
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
                <span class="info-box-text">Approved Volunteers</span>
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
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-ban"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Rejected Applications</span>
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
              <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-calendar-day"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Upcoming Events</span>
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
                <span class="info-box-text">Active Programs</span>
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
        <div class="info-box bg-white shadow">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-home"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Active Activities</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `activity_list` where delete_flag= 0 and `status` = 1 ")->num_rows;
                ?>
            </span>
            </div>
            </div>
            
          </div>
        
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <div class="info-box bg-white shadow">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-image"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Gallery Images</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `gallery` where delete_flag= 0 ")->num_rows;
                ?>
            </span>
            </div>
            </div>
            </div>
            </div>

<!-- Quick Actions Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="<?php echo base_url ?>manager/?page=volunteer/request" class="btn btn-warning btn-block">
                            <i class="fas fa-user-plus"></i><br>
                            Review Applications
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?php echo base_url ?>manager/?page=donation/index" class="btn btn-success btn-block">
                            <i class="fas fa-donate"></i><br>
                            Process Donations
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?php echo base_url ?>manager/?page=events" class="btn btn-primary btn-block">
                            <i class="fas fa-calendar"></i><br>
                            Manage Events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clock"></i> Recent Applications</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recent_apps = $conn->query("SELECT firstname, lastname, date_created, status FROM volunteer_list ORDER BY date_created DESC LIMIT 5");
                            while($row = $recent_apps->fetch_assoc()):
                                $status_class = $row['status'] == 1 ? 'success' : ($row['status'] == 2 ? 'danger' : 'warning');
                                $status_text = $row['status'] == 1 ? 'Approved' : ($row['status'] == 2 ? 'Rejected' : 'Pending');
                            ?>
                            <tr>
                                <td><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($row['date_created'])); ?></td>
                                <td><span class="badge badge-<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> Recent Donations</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Donor</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recent_donations = $conn->query("SELECT fullname, amount, status FROM donations ORDER BY created_at DESC LIMIT 5");
                            while($row = $recent_donations->fetch_assoc()):
                                $status_class = $row['status'] == 'completed' ? 'success' : ($row['status'] == 'failed' ? 'danger' : 'warning');
                            ?>
                            <tr>
                                <td><?php echo $row['fullname']; ?></td>
                                <td><?php echo number_format($row['amount']); ?> RWF</td>
                                <td><span class="badge badge-<?php echo $status_class; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<hr class="bg-success">
<div class="row">
    <div class="col-md-12">
        <img src="<?= validate_image($_settings->info('cover')) ?>" alt="Website Cover" class="img-fluid border w-100" id="website-cover">
    </div>
</div>

<div class="mb-3">
  <strong>Username:</strong> <?php echo $_settings->userdata('username'); ?><br>
  <strong>Email:</strong> <?php echo $_settings->userdata('email'); ?><br>
  <strong>Role:</strong> Manager
</div>
