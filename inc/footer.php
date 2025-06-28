<script>
  $(document).ready(function(){
    $('#p_use').click(function(){
      uni_modal("Privacy Policy","policy.php","mid-large")
    })
     window.viewer_modal = function($src = ''){
      start_loader()
      var t = $src.split('.')
      t = t[1]
      if(t =='mp4'){
        var view = $("<video src='"+$src+"' controls autoplay></video>")
      }else{
        var view = $("<img src='"+$src+"' />")
      }
      $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
      $('#viewer_modal .modal-content').append(view)
      $('#viewer_modal').modal({
              show:true,
              backdrop:'static',
              keyboard:false,
              focus:true
            })
            end_loader()  

  }
    window.uni_modal = function($title = '' , $url='',$size=""){
        start_loader()
        $.ajax({
            url:$url,
            error:err=>{
                console.log()
                alert("An error occured")
            },
            success:function(resp){
                if(resp){
                    $('#uni_modal .modal-title').html($title)
                    $('#uni_modal .modal-body').html(resp)
                    if($size != ''){
                        $('#uni_modal .modal-dialog').addClass($size+'  modal-dialog-centered')
                    }else{
                        $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
                    }
                    $('#uni_modal').modal({
                      show:true,
                      backdrop:'static',
                      keyboard:false,
                      focus:true
                    })
                    end_loader()
                }
            }
        })
    }
    window._conf = function($msg='',$func='',$params = []){
       $('#confirm_modal #confirm').attr('onclick',$func+"("+$params.join(',')+")")
       $('#confirm_modal .modal-body').html($msg)
       $('#confirm_modal').modal('show')
    }
  })
</script>
<!-- Footer-->
<footer class="py-3 bg-dark">
            <div class="container">
              <p class="m-0 text-center text-white"><?php echo $_settings->info('short_name') ?>&copy; <?php echo date("Y");?></p>
              <!--p-- class="m-0 text-center text-white">Dev Ops : <a href="mailto:">Alain & Ben</a></!--p-->
          </div>
          <style>
          .footer {
  background: #212529;
  padding: 10px 0;
}
.footer a {
  color: white;
  font-size: 20px;
  padding: 10px;
  border-right: 1px solid white;
  transition: all .5s ease;
}
.footer a:first-child {
  border-left: 1px solid white;
}
.footer a:hover {
  color: #337ab7;
}
  </style>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet"/>
  <footer class="footer">
    <!--div class="container text-center">
      <a href="tel:+250-788-000-000" target="_blank"><i class="fa fa-phone"></i></a>
      <a href="https://api.whatsapp.com/send?phone=0788000000" target="_blank"><i class="fa fa-whatsapp"></i></a>
      <a href="mailto:alain36@gmail.com" target="_blank"><i class="fa fa-envelope"></i></a>
      <a href="https://www.linkedin.com/in/" target="_blank"><i class="fa fa-linkedin"></i></a>
      <a href="https://www.facebook.com/yourpage" target="_blank"><i class="fa fa-facebook"></i></a>
      <a href="https://twitter.com/yourhandle" target="_blank"><i class="fa fa-twitter"></i></a>
      <a href="https://www.instagram.com/"><i class="fa fa-instagram"></i></a>
      <a href="https://www.youtube.com/" target="_blank" >
      <img src="img/ytb.png" alt="YouTube" width="32" height="32"></a>
      <a href="https://www.google.com/maps/dir/Kigali+Convention+Centre,+23VV%2BMQ9,+Convention+Ctr+Roundabout,+Kigali/KG+563+St,+Kigali/@-1.9565216,30.0854836,14z/data=!3m1!4b1!4m13!4m12!1m5!1m1!1s0x19dca6f46d387275:0x7b8b917a7206d1e2!2m2!1d30.0938534!2d-1.9545556!1m5!1m1!1s0x19dca72d4fe1f26d:0xa8ef96262663ff2c!2m2!1d30.0910382!2d-1.9446173?entry=tts&g_ep=EgoyMDI1MDIxOS4xKgBIAVAD" target="_blank" >
      <img src="img/location.png" alt="Location" width="32" height="32"></a>
  </!--div-->
  </footer>

   
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="<?php echo base_url ?>plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="<?php echo base_url ?>plugins/sparklines/sparkline.js"></script>
    <!-- Select2 -->
    <script src="<?php echo base_url ?>plugins/select2/js/select2.full.min.js"></script>
    <!-- JQVMap -->
    <script src="<?php echo base_url ?>plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="<?php echo base_url ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo base_url ?>plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?php echo base_url ?>plugins/moment/moment.min.js"></script>
    <script src="<?php echo base_url ?>plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="<?php echo base_url ?>plugins/summernote/summernote-bs4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- overlayScrollbars -->
    <!-- <script src="<?php echo base_url ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script> -->
    <!-- AdminLTE App -->
    <script src="<?php echo base_url ?>dist/js/adminlte.js"></script>
    <div class="daterangepicker ltr show-ranges opensright">
      <div class="ranges">
        <ul>
          <li data-range-key="Today">Today</li>
          <li data-range-key="Yesterday">Yesterday</li>
          <li data-range-key="Last 7 Days">Last 7 Days</li>
          <li data-range-key="Last 30 Days">Last 30 Days</li>
          <li data-range-key="This Month">This Month</li>
          <li data-range-key="Last Month">Last Month</li>
          <li data-range-key="Custom Range">Custom Range</li>
        </ul>
      </div>
      <div class="drp-calendar left">
        <div class="calendar-table"></div>
        <div class="calendar-time" style="display: none;"></div>
      </div>   
      <div class="drp-calendar right">
        <div class="calendar-table"></div>
        <div class="calendar-time" style="display: none;"></div>
      </div>
      <div class="drp-buttons"><span class="drp-selected"></span><button class="cancelBtn btn btn-sm btn-default" type="button">Cancel</button><button class="applyBtn btn btn-sm btn-primary" disabled="disabled" type="button">Apply</button> </div>
    </div>
    <div class="jqvmap-label" style="display: none; left: 1093.83px; top: 394.361px;"> </div>

<script>
// Counter Animation
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    function updateCounter() {
        start += increment;
        if (start < target) {
            element.textContent = Math.floor(start);
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target;
        }
    }
    updateCounter();
}

// Initialize counters when they come into view
const observerOptions = {
    threshold: 0.5,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counters = entry.target.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                animateCounter(counter, target);
                observer.unobserve(counter);
            });
        }
    });
}, observerOptions);

// Observe stats section when page loads
document.addEventListener('DOMContentLoaded', function() {
    const statsSection = document.querySelector('.stats-grid');
    if (statsSection) {
        observer.observe(statsSection);
    }
    
    // Smooth scroll for scroll indicator
    const scrollIndicator = document.querySelector('.scroll-indicator');
    if (scrollIndicator) {
        scrollIndicator.addEventListener('click', function() {
            const featuresSection = document.querySelector('.features-section');
            if (featuresSection) {
                featuresSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
});

// Add parallax effect to hero section
window.addEventListener('scroll', function() {
    const scrolled = window.pageYOffset;
    const heroSection = document.getElementById('main-header');
    if (heroSection) {
        const rate = scrolled * -0.5;
        heroSection.style.transform = `translateY(${rate}px)`;
    }
});
</script>