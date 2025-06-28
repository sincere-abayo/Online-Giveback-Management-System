<!-- Modern Hero Section -->
<section class="hero-section" id="main-header">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content">
                    <div class="organization-badge animate-fadeInUp">
                        <span class="badge-text">Dufatanye Charity Foundation</span>
                    </div>
                    <h1 class="hero-title animate-fadeInUp" style="animation-delay: 0.2s;">
                        Online Giveback Management System
                    </h1>
                    <p class="hero-subtitle animate-fadeInUp" style="animation-delay: 0.4s;">
                        Empowering communities through transparent giving and meaningful volunteer opportunities with Dufatanye Charity Foundation
                    </p>
                    <div class="hero-description animate-fadeInUp" style="animation-delay: 0.5s;">
                        <p>A comprehensive platform for tracking donations, managing volunteers, and creating lasting impact in our communities</p>
                    </div>
                    <div class="hero-buttons animate-fadeInUp" style="animation-delay: 0.6s;">
                        <button type="button" class="btn btn-primary btn-hero" onclick="window.location.href='checkout_mtn.php'">
                            <i class="fa fa-gift me-2"></i>
                            Give a Gift
                        </button>
                        <button type="button" class="btn btn-outline-light btn-hero" onclick="window.location.href='registration.php'">
                            <i class="fas fa-hand-holding-heart me-2"></i>
                            Volunteer Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="scroll-indicator">
        <div class="scroll-arrow"></div>
    </div>
</section>
<!-- Section-->
<style>
    .book-cover{
        object-fit:contain !important;
        height:auto !important;
    }
</style>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Secure & Transparent</h4>
                    <p>Dufatanye ensures complete transparency in donation tracking with secure, accountable processes for every transaction and giveback initiative.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h4>Community Empowerment</h4>
                    <p>Building stronger communities through direct connection between donors, volunteers, and beneficiaries for sustainable social impact.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Impact Measurement</h4>
                    <p>Monitor donations, volunteer contributions, and community impact with comprehensive reporting and real-time analytics.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('slider.php')  ?>

<!-- Welcome Content Section -->
<section class="welcome-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="welcome-content">
                    <?php require_once('welcome_content.html') ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number" data-target="250">0</div>
                        <div class="stat-label">Donors Registered</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-target="120">0</div>
                        <div class="stat-label">Active Volunteers</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-target="1850">0</div>
                        <div class="stat-label">Lives Transformed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-target="68">0</div>
                        <div class="stat-label">Community Projects</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>