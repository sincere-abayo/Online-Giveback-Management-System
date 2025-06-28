<!-- Modern Gallery Section -->
<section class="gallery-page py-5">
    <div class="container">
        <!-- Page Header -->
        <div class="gallery-header text-center mb-5">
            <div class="page-badge">
                <span class="badge-text">Dufatanye Gallery</span>
            </div>
            <h1 class="page-title">Our Gallery</h1>
            <p class="page-subtitle">Discover moments that matter - showcasing our community impact and the stories behind our charitable work</p>
        </div>

        <!-- Gallery Grid -->
        <div class="gallery-grid">
            <?php  
            require('./gallerie/db_config.php');  

            $sql = "SELECT * FROM gallery ORDER BY id DESC";  
            $images = $conn->query($sql);  

            if($images && $images->num_rows > 0) {
                while($image = $images->fetch_assoc()){  
            ?>  
                <div class="gallery-item">
                    <div class="gallery-card">
                        <div class="gallery-image-container">
                            <img src="./admin/gallerie/uploads/<?php echo $image['image'] ?>" 
                                 alt="<?php echo htmlentities($image['title']) ?>" 
                                 class="gallery-image">
                            <div class="gallery-overlay">
                                <div class="gallery-content">
                                    <h4 class="gallery-title"><?php echo htmlentities($image['title']) ?></h4>
                                    <a href="./admin/gallerie/uploads/<?php echo $image['image'] ?>" 
                                       class="view-button fancybox" 
                                       rel="gallery">
                                        <i class="fas fa-expand"></i>
                                        View Full Size
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                }
            } else {
            ?>
                <div class="col-12">
                    <div class="no-images-message text-center py-5">
                        <i class="fas fa-images mb-3"></i>
                        <h3>No Images Available</h3>
                        <p class="text-muted">Check back soon for updates to our gallery.</p>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Gallery Info Section -->
        <div class="gallery-info mt-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="info-content">
                        <h2><i class="fas fa-camera"></i> Capturing Impact</h2>
                        <p class="lead">Every image tells a story of hope, transformation, and community support.</p>
                        <p>Our gallery showcases the real impact of your donations and volunteer efforts. From educational programs to community development projects, these moments capture the essence of what Dufatanye Charity Foundation accomplishes together with our supporters.</p>
                        <ul class="impact-list">
                            <li><i class="fas fa-check-circle"></i> Community development projects</li>
                            <li><i class="fas fa-check-circle"></i> Educational support initiatives</li>
                            <li><i class="fas fa-check-circle"></i> Volunteer activities and events</li>
                            <li><i class="fas fa-check-circle"></i> Success stories and testimonials</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-images"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $images ? $images->num_rows : 0; ?>+</h3>
                                <p>Photos Shared</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="stat-info">
                                <h3>1000+</h3>
                                <p>Stories Captured</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3>500+</h3>
                                <p>Lives Documented</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="stat-info">
                                <h3>50+</h3>
                                <p>Events Covered</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="gallery-cta mt-5">
            <div class="cta-card">
                <div class="cta-content text-center">
                    <h2><i class="fas fa-hands-helping"></i> Be Part of Our Story</h2>
                    <p class="cta-text">Join our mission and help us create more moments of impact. Your support makes a difference in every story we capture.</p>
                    <div class="cta-buttons">
                        <button class="btn btn-primary btn-lg" onclick="window.location.href='?p=registration'">
                            <i class="fas fa-hand-holding-heart me-2"></i>
                            Volunteer Now
                        </button>
                        <button class="btn btn-outline-primary btn-lg" onclick="window.location.href='checkout_mtn.php'">
                            <i class="fas fa-gift me-2"></i>
                            Make a Donation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include FancyBox for lightbox functionality -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize FancyBox
    $('.fancybox').fancybox({
        buttons: [
            "slideShow",
            "thumbs",
            "zoom",
            "fullScreen",
            "share",
            "close"
        ],
        loop: true,
        protect: true
    });

    // Add hover effects
    $('.gallery-card').hover(
        function() {
            $(this).addClass('hovered');
        },
        function() {
            $(this).removeClass('hovered');
        }
    );
});
</script>