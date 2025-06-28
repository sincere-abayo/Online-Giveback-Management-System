<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
</style>
<!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
        <!-- Brand Logo -->
        <a href="<?php echo base_url ?>admin" class="brand-link bg-primary text-sm">
        <img src="<?php echo validate_image($_settings->info('logo'))?>" alt="Store Logo" class="brand-image img-circle elevation-3" style="opacity: .8;width: 2.5rem;height: 2.5rem;max-height: unset">
        <span class="brand-text font-weight-light"><?php echo $_settings->info('short_name') ?></span>
        </a>
        <!-- Sidebar -->
        <div class="sidebar os-host os-theme-light os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-transition os-host-scrollbar-horizontal-hidden">
          <div class="os-resize-observer-host observed">
            <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
          </div>
          <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
            <div class="os-resize-observer"></div>
          </div>
          <div class="os-content-glue" style="margin: 0px -8px; width: 249px; height: 646px;"></div>
          <div class="os-padding">
            <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow-y: scroll;">
              <div class="os-content" style="padding: 0px 8px; height: 100%; width: 100%;">
                <!-- Sidebar user panel (optional) -->
                <div class="clearfix"></div>
                <!-- Sidebar Menu -->
                <nav class="mt-4">
                   <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat nav-child-indent nav-collapse-hide-child" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item dropdown">
                      <a href="./" class="nav-link nav-home">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                          Dashboard
                        </p>
                      </a>
                    </li> 
                    <li class="nav-header">Manage Volunteer</li>
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=volunteer/request" class="nav-link nav-request">
                        <i class="nav-icon fas fa-user-plus"></i>
                        <p>
                        Manage  Applications
                      </p>
                      </a>
                    </li>

                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=volunteer/manage_volunteer" class="nav-link nav-manage_volunteer">
                        <i class="nav-icon fas fa-plus"></i>
                        <p>
                          New Volunteer
                        </p>
                      </a>
                    </li> 

                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=volunteer/index" class="nav-link nav-volunteer/">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>
                        Volunteer List
                        </p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=donation/index" class="nav-link nav-donation">
                        <i class="nav-icon fas fa-donate"></i>
                        <p>
                        Manage Donations
                        </p>
                      </a>
                    </li>
                    <li class="nav-header">Programs & Activities</li>
                  
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=program" class="nav-link nav-program">
                        <i class="nav-icon fas fa-map"></i>
                        <p>
                        Manage Programs 
                        </p>
                      </a>
                    </li>  
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=activity" class="nav-link nav-activity">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                        Manage Activities
                        </p>
                      </a>
                    <li class="nav-header">Information</li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=maintenance/cause" class="nav-link nav-cause">
                        <i class="nav-icon fas fa-hands-helping"></i>
                        <p>
                          Team
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=events" class="nav-link nav-events">
                        <i class="nav-icon fas fa-calendar-day"></i>
                        <p>
                          Manage Events
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=gallerie" class="nav-link nav-gallerie">
                        <i class="nav-icon fas fa-image"></i>
                        <p>
                          Manage Gallery
                        </p>
                      </a>
                    </li>
                    <li class="nav-header">Generate Reports</li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=report/requests" class="nav-link nav-requests">
                        <i class="nav-icon fas fa-book"></i>
                        <p>
                        Volunteer Application Reports
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=report/admitted" class="nav-link nav-admitted">
                        <i class="nav-icon fas fa-book"></i>
                        <p>
                        Admitted Volunteer Report
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=report/rejected" class="nav-link nav-rejected">
                        <i class="nav-icon fas fa-book"></i>
                        <p>
                        Rejected Volunteer Report
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=report/all" class="nav-link nav-all">
                        <i class="nav-icon fas fa-book"></i>
                        <p>
                        All Volunteer Report
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=report/donation" class="nav-link nav-donation">
                        <i class="nav-icon fas fa-book"></i>
                        <p>
                        Donation Report
                        </p>
                      </a>
                    </li>
                    <li class="nav-header">Settings</li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=user/list" class="nav-link nav-user/list">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                          Users
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=system_info" class="nav-link nav-system_info">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                          Settings
                        </p>
                      </a>
                    </li>
                  </ul>
                </nav>
                <!-- /.sidebar-menu -->
              </div>
            </div>
          </div>
          <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
            <div class="os-scrollbar-track">
              <div class="os-scrollbar-handle" style="width: 100%; transform: translate(0px, 0px);"></div>
            </div>
          </div>
          <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-auto-hidden">
            <div class="os-scrollbar-track">
              <div class="os-scrollbar-handle" style="height: 55.017%; transform: translate(0px, 0px);"></div>
            </div>
          </div>
          <div class="os-scrollbar-corner"></div>
        </div>
        <!-- /.sidebar -->
      </aside>
      <script>
    $(document).ready(function(){
      var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
      var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
      page = page.split('/');
      page = page[0];
      if(s!='')
        page = page+'_'+s;

      if($('.nav-link.nav-'+page).length > 0){
             $('.nav-link.nav-'+page).addClass('active')
        if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
          $('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
        }
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
          $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }

      }
     
    })
  </script>