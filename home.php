<!-- Modern Hero Section -->
<section class="hero-section" id="main-header">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content">

                    <!-- Organization Badge -->
                    <div class="organization-badge animate-fadeInUp">
                        <span class="badge-text">Dufatanye Charity Foundation</span>
                    </div>

                    <!-- Main Title -->
                    <h1 class="hero-title animate-fadeInUp" style="animation-delay: 0.2s;">
                        Online Giveback<br>
                        Management System
                    </h1>

                    <!-- Hero Subtitle -->
                    <p class="hero-subtitle animate-fadeInUp" style="animation-delay: 0.4s; font-weight: 800;">
                        Empowering communities through transparent giving and meaningful volunteer opportunities
                    </p>

                    <!-- Hero Description -->
                    <div class="hero-description animate-fadeInUp" style="animation-delay: 0.5s;">
                        <p>
                            Track donations • Manage volunteers • Create lasting impact<br>
                            <span class="text-light">Join our mission to build stronger communities together</span>
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="hero-buttons animate-fadeInUp" style="animation-delay: 0.6s;">
                        <button type="button" class="btn btn-primary btn-hero"
                            onclick="window.location.href='donation.php'">
                            <i class="fa fa-gift me-2"></i>
                            Donate Now
                        </button>

                        <button type="button" class="btn btn-outline-light btn-hero"
                            onclick="window.location.href='registration.php'">
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
    .book-cover {
        object-fit: contain !important;
        height: auto !important;
    }

    /* Welcome Section Styles */
    .welcome-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        position: relative;
    }

    .welcome-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .welcome-header {
        position: relative;
        z-index: 2;
    }

    .welcome-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        padding: 12px 24px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .welcome-badge i {
        font-size: 1.1rem;
    }

    .welcome-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .welcome-divider {
        width: 80px;
        height: 4px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        margin: 0 auto;
        border-radius: 2px;
    }

    /* Content Card Styles */
    .content-card {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .content-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .content-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(45deg, #667eea, #764ba2);
    }

    .content-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .content-icon i {
        font-size: 2rem;
        color: white;
    }

    .content-card h3 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    .content-card p {
        font-size: 1.1rem;
        line-height: 1.7;
        color: #6c757d;
        margin-bottom: 25px;
    }

    /* Mission Highlights */
    .mission-highlights {
        margin-top: 30px;
    }

    .highlight-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        padding: 12px 0;
    }

    .highlight-item i {
        color: #28a745;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .highlight-item span {
        font-weight: 500;
        color: #495057;
        font-size: 1rem;
    }

    /* Image Section Styles */
    .welcome-image-section {
        position: relative;
        z-index: 2;
    }

    .image-card {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }

    .image-card:hover {
        transform: scale(1.02);
    }

    .welcome-hero-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        object-position: center;
        display: block;
    }

    .image-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
        padding: 40px 30px 30px;
        color: white;
    }

    .overlay-content h4 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .overlay-content p {
        font-size: 1rem;
        line-height: 1.6;
        margin: 0;
        opacity: 0.9;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    /* Features Grid */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    .feature-item {
        background: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .feature-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(45deg, #667eea, #764ba2);
    }

    .feature-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
    }

    .feature-icon i {
        font-size: 1.8rem;
        color: white;
    }

    .feature-item h4 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
    }

    .feature-item p {
        font-size: 1rem;
        line-height: 1.6;
        color: #6c757d;
        margin: 0;
    }

    /* CTA Section */
    .cta-section {
        position: relative;
        z-index: 2;
    }

    .cta-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 50px 40px;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .cta-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="%23ffffff" opacity="0.1"/></svg>');
        animation: float 20s infinite linear;
    }

    @keyframes float {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .cta-card h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 15px;
        position: relative;
        z-index: 2;
    }

    .cta-card p {
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 30px;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }

    .cta-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }

    .cta-buttons .btn {
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .cta-buttons .btn-primary {
        background: white;
        color: #667eea;
        border: 2px solid white;
    }

    .cta-buttons .btn-primary:hover {
        background: transparent;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
    }

    .cta-buttons .btn-outline-primary {
        background: transparent;
        color: white;
        border: 2px solid white;
    }

    .cta-buttons .btn-outline-primary:hover {
        background: white;
        color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
    }

    .cta-card blockquote {
        border-left: 4px solid white;
        padding-left: 20px;
        margin: 30px 0 0;
        position: relative;
        z-index: 2;
    }

    .cta-card blockquote p {
        font-style: italic;
        font-size: 1.1rem;
        margin: 0;
        opacity: 0.9;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .welcome-title {
            font-size: 2rem;
        }

        .content-card {
            padding: 30px;
            margin-bottom: 30px;
        }

        .welcome-hero-image {
            height: 350px;
        }

        .features-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .cta-card {
            padding: 40px 30px;
        }

        .cta-buttons {
            flex-direction: column;
            align-items: center;
        }

        .cta-buttons .btn {
            width: 100%;
            max-width: 300px;
        }
    }

    @media (max-width: 768px) {
        .welcome-title {
            font-size: 1.8rem;
        }

        .content-card {
            padding: 25px;
        }

        .content-icon {
            width: 60px;
            height: 60px;
        }

        .content-icon i {
            font-size: 1.5rem;
        }

        .welcome-hero-image {
            height: 300px;
        }

        .features-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .feature-item {
            padding: 25px;
        }

        .cta-card {
            padding: 30px 20px;
        }

        .cta-card h3 {
            font-size: 1.6rem;
        }
    }

    @media (max-width: 576px) {
        .welcome-badge {
            padding: 10px 20px;
            font-size: 0.8rem;
        }

        .welcome-title {
            font-size: 1.5rem;
        }

        .content-card {
            padding: 20px;
        }

        .welcome-hero-image {
            height: 250px;
        }

        .image-overlay {
            padding: 30px 20px 20px;
        }

        .overlay-content h4 {
            font-size: 1.3rem;
        }

        .feature-item {
            padding: 20px;
        }

        .cta-card {
            padding: 25px 15px;
        }

        .cta-card h3 {
            font-size: 1.4rem;
        }
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
                    <p>Dufatanye ensures complete transparency in donation tracking with secure, accountable processes
                        for every transaction and giveback initiative.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h4>Community Empowerment</h4>
                    <p>Building stronger communities through direct connection between donors, volunteers, and
                        beneficiaries for sustainable social impact.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Impact Measurement</h4>
                    <p>Monitor donations, volunteer contributions, and community impact with comprehensive reporting and
                        real-time analytics.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('slider.php') ?>

<!-- Welcome Content Section -->
<section class="welcome-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="welcome-header text-center mb-5">
                    <div class="welcome-badge">
                        <i class="fas fa-heart"></i>
                        <span>Welcome to Dufatanye</span>
                    </div>
                    <h2 class="welcome-title">Empowering Communities Through Compassion</h2>
                    <div class="welcome-divider"></div>
                </div>

                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="welcome-content">
                            <div class="content-card">
                                <div class="content-icon">
                                    <i class="fas fa-hands-helping"></i>
                                </div>
                                <h3>Our Mission</h3>
                                <p>The <strong>Online Giveback Management System (OGMS)</strong> is Dufatanye's
                                    comprehensive platform for tracking, managing, and reporting on donations, volunteer
                                    activities, and community impact initiatives.</p>

                                <div class="mission-highlights">
                                    <div class="highlight-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Transparent donation tracking</span>
                                    </div>
                                    <div class="highlight-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Volunteer management</span>
                                    </div>
                                    <div class="highlight-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Community impact reporting</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="welcome-image-section">
                            <div class="image-card">
                                <img src="https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
                                    alt="Community Support - Dufatanye Charity Foundation" class="welcome-hero-image">
                                <div class="image-overlay">
                                    <div class="overlay-content">
                                        <h4>Building Stronger Communities</h4>
                                        <p>Together we create lasting positive change</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-lg-12">
                        <div class="features-grid">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h4>Secure & Transparent</h4>
                                <p>We prioritize security, accountability, and simplicity, making it easy for every user
                                    to manage resources effectively.</p>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h4>Community Focused</h4>
                                <p>Dufatanye Charity Foundation is committed to building stronger communities through
                                    sustainable development programs.</p>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h4>Impact Driven</h4>
                                <p>Educational support, healthcare initiatives, and poverty alleviation efforts create
                                    measurable positive change.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-lg-12">
                        <div class="cta-section text-center">
                            <div class="cta-card">
                                <h3>Ready to Make a Difference?</h3>
                                <p>Log in using your credentials with your organization to begin managing your givebacks
                                    responsibly.</p>
                                <div class="cta-buttons">
                                    <button type="button" class="btn btn-primary btn-lg"
                                        onclick="window.location.href='donation.php'">
                                        <i class="fas fa-gift me-2"></i>Start Donating
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-lg"
                                        onclick="window.location.href='registration.php'">
                                        <i class="fas fa-hand-holding-heart me-2"></i>Join as Volunteer
                                    </button>
                                </div>
                                <blockquote class="mt-4">
                                    <p>"Let's reduce waste, promote reuse, and manage resources the smart way —
                                        together."</p>
                                </blockquote>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>