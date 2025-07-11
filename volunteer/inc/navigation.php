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
                    <li class="nav-header">Manage Refugee</li>
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=refugee/request" class="nav-link nav-refugee_request">
                        <i class="nav-icon fas fa-user-plus"></i>
                        <p>
                        Manage  Requests
                      </p>
                      </a>
                    </li>

                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=refugee/manage_refugee" class="nav-link nav-refugee_manage_refugee">
                        <i class="nav-icon fas fa-plus"></i>
                        <p>
                          New refugee
                        </p>
                      </a>
                    </li> 

                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=refugee" class="nav-link nav-refugee">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>
                          Refugee List
                        </p>
                      </a>
                    </li>
                    <li class="nav-header">Maintenance</li>
                  
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=district" class="nav-link nav-district">
                        <i class="nav-icon fas fa-map"></i>
                        <p>
                        Manage Districts 
                        </p>
                      </a>
                    </li>  
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=camp" class="nav-link nav-camp">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                        Manage Camps
                        </p>
                      </a>
                    <li class="nav-header">Information</li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=maintenance/cause" class="nav-link nav-cause">
                        <i class="nav-icon fas fa-hands-helping"></i>
                        <p>
                          News
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=blogs" class="nav-link nav-blogs">
                        <i class="nav-icon fas fa-blog"></i>
                        <p>
                          Blog List
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=events" class="nav-link nav-events">
                        <i class="nav-icon fas fa-calendar-day"></i>
                        <p>
                          Emergency List
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=maintenance/topics" class="nav-link nav-topics">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>
                          Topic List
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
                    <li class="nav-header">Settings</li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=user/list" class="nav-link nav-user/list">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                          User List
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