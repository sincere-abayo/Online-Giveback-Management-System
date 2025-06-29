<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../config.php';

// Check if volunteer is logged in
if (!isset($_SESSION['volunteer_id']) || $_SESSION['volunteer_type'] !== 'volunteer') {
    header("Location: ../volunteer_login.php");
    exit;
}

// Get volunteer information
$volunteer_id = $_SESSION['volunteer_id'];
$sql = "SELECT * FROM volunteer_list WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $volunteer_id);
$stmt->execute();
$result = $stmt->get_result();
$volunteer = $result->fetch_assoc();

// Get all active programs with their activities
$programs_sql = "SELECT p.*, 
                        COUNT(a.id) as activity_count 
                 FROM program_list p 
                 LEFT JOIN activity_list a ON p.id = a.program_id AND a.status = 1 AND a.delete_flag = 0
                 WHERE p.status = 1 AND p.delete_flag = 0 
                 GROUP BY p.id 
                 ORDER BY p.name ASC";
$programs_result = $conn->query($programs_sql);

// Get all events
$events_sql = "SELECT * FROM events ORDER BY schedule ASC";
$events_result = $conn->query($events_sql);

// Get volunteer's current activities for highlighting
$volunteer_activities_sql = "SELECT vh.activity_id 
                             FROM volunteer_history vh 
                             WHERE vh.volunteer_id = ? AND vh.status = 1";
$volunteer_activities_stmt = $conn->prepare($volunteer_activities_sql);
$volunteer_activities_stmt->bind_param("i", $volunteer_id);
$volunteer_activities_stmt->execute();
$volunteer_activities_result = $volunteer_activities_stmt->get_result();
$volunteer_activity_ids = [];
while ($row = $volunteer_activities_result->fetch_assoc()) {
    $volunteer_activity_ids[] = $row['activity_id'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GMS - Programs & Events</title>
    <link rel="shortcut icon" href="../gms1.png" type="image/png">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
            --secondary-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: var(--card-bg);
            box-shadow: var(--shadow-xl);
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .sidebar-brand img {
            width: 32px;
            height: 32px;
        }

        .sidebar-nav {
            padding: 1.5rem 0;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s ease;
            border-radius: 0;
            font-weight: 500;
        }

        .nav-link:hover {
            background: var(--light-bg);
            color: var(--primary-color);
            text-decoration: none;
        }

        .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: var(--light-bg);
        }

        .top-navbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            box-shadow: var(--shadow-sm);
        }

        .top-navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .user-details h6 {
            margin: 0;
            font-weight: 600;
            color: var(--text-primary);
        }

        .user-details small {
            color: var(--text-secondary);
        }

        .logout-btn {
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: #dc2626;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }

        /* Programs Content */
        .programs-content {
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .card-header {
            background: var(--light-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .stat-card.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .stat-card.success {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
        }

        .stat-card.info {
            background: linear-gradient(135deg, var(--info-color), #2563eb);
            color: white;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Tabs */
        .nav-tabs {
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 2rem;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--text-secondary);
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem 0.5rem 0 0;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-tabs .nav-link.active {
            background: var(--primary-color);
            color: white;
            border: none;
        }

        .nav-tabs .nav-link:hover {
            border: none;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
        }

        /* Program Cards */
        .program-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.2s ease;
            border-left: 4px solid var(--primary-color);
            position: relative;
            overflow: hidden;
        }

        .program-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .program-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .program-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .program-title {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 1.25rem;
            margin: 0;
        }

        .activity-count {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .program-description {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        /* Activity Items */
        .activities-list {
            margin-top: 1rem;
        }

        .activity-item {
            background: var(--light-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .activity-item:hover {
            box-shadow: var(--shadow-sm);
            transform: translateX(5px);
        }

        .activity-item.volunteer-joined {
            border-left: 4px solid var(--warning-color);
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }

        .activity-item.volunteer-joined::after {
            content: '✓ Joined';
            position: absolute;
            top: 0.75rem;
            right: 1rem;
            background: var(--warning-color);
            color: #92400e;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .activity-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .activity-description {
            color: var(--text-secondary);
            font-size: 0.875rem;
            line-height: 1.5;
        }

        /* Event Cards */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .event-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }

        .event-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .event-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .event-content {
            padding: 1.5rem;
        }

        .event-title {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }

        .event-description {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            line-height: 1.5;
            font-size: 0.875rem;
        }

        .event-date {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .programs-content {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .program-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .events-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Toggle Button for Mobile */
        .sidebar-toggle {
            display: none;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 0.5rem;
            font-size: 1.25rem;
        }

        @media (max-width: 768px) {
            .sidebar-toggle {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <img src="../uploads/GMS.png" alt="GMS">
                <span>GMS Dashboard</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="../volunteer_dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="profile.php" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="../volunteer_dashboard.php#activities" class="nav-link">
                    <i class="fas fa-tasks"></i>
                    <span>Activities</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="../volunteer_dashboard.php#donations" class="nav-link">
                    <i class="fas fa-heart"></i>
                    <span>Donations</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="programs.php" class="nav-link active">
                    <i class="fas fa-project-diagram"></i>
                    <span>Programs</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="../index.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Homepage</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="top-navbar-content">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($volunteer['firstname'], 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <h6><?php echo htmlspecialchars($volunteer['firstname'] . ' ' . $volunteer['lastname']); ?></h6>
                        <small>Volunteer</small>
                    </div>
                </div>

                <a href="../?logout=1" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Programs Content -->
        <div class="programs-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Programs & Events</h1>
                <p class="page-subtitle">Explore available programs, activities, and upcoming events</p>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-number"><?php echo $programs_result->num_rows; ?></div>
                    <div class="stat-label">Active Programs</div>
                </div>
                <div class="stat-card info">
                    <div class="stat-number"><?php echo $events_result->num_rows; ?></div>
                    <div class="stat-label">Upcoming Events</div>
                </div>
                <div class="stat-card success">
                    <div class="stat-number"><?php echo count($volunteer_activity_ids); ?></div>
                    <div class="stat-label">Your Activities</div>
                </div>
            </div>

            <!-- Content Tabs -->
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="contentTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="programs-tab" data-toggle="tab" href="#programs" role="tab">
                                <i class="fas fa-project-diagram"></i> Programs & Activities
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="events-tab" data-toggle="tab" href="#events" role="tab">
                                <i class="fas fa-calendar-alt"></i> Events
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="contentTabsContent">
                        <!-- Programs Tab -->
                        <div class="tab-pane fade show active" id="programs" role="tabpanel">
                            <div class="mt-4">
                                <h3 class="mb-4">
                                    <i class="fas fa-project-diagram text-primary"></i>
                                    Our Programs & Activities
                                </h3>
                                <p class="text-muted mb-4">Explore the various programs and activities available for
                                    volunteers</p>

                                <?php if ($programs_result->num_rows > 0): ?>
                                    <?php while ($program = $programs_result->fetch_assoc()): ?>
                                        <div class="program-card">
                                            <div class="program-header">
                                                <h4 class="program-title"><?php echo htmlspecialchars($program['name']); ?></h4>
                                                <span class="activity-count">
                                                    <?php echo $program['activity_count']; ?> Activities
                                                </span>
                                            </div>

                                            <p class="program-description">
                                                <?php echo htmlspecialchars($program['description']); ?></p>

                                            <div class="activities-list">
                                                <?php
                                                // Get activities for this program
                                                $activities_sql = "SELECT * FROM activity_list 
                                                                  WHERE program_id = ? AND status = 1 AND delete_flag = 0 
                                                                  ORDER BY name ASC";
                                                $activities_stmt = $conn->prepare($activities_sql);
                                                $activities_stmt->bind_param("i", $program['id']);
                                                $activities_stmt->execute();
                                                $activities_result = $activities_stmt->get_result();

                                                if ($activities_result->num_rows > 0):
                                                    while ($activity = $activities_result->fetch_assoc()):
                                                        $is_joined = in_array($activity['id'], $volunteer_activity_ids);
                                                        ?>
                                                        <div class="activity-item <?php echo $is_joined ? 'volunteer-joined' : ''; ?>">
                                                            <div class="activity-name">
                                                                <?php echo htmlspecialchars($activity['name']); ?></div>
                                                            <div class="activity-description">
                                                                <?php echo htmlspecialchars($activity['description']); ?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    endwhile;
                                                else:
                                                    ?>
                                                    <div class="empty-state">
                                                        <i class="fas fa-clipboard-list"></i>
                                                        <p>No activities available for this program yet.</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-project-diagram"></i>
                                        <h5>No Programs Available</h5>
                                        <p>There are currently no active programs. Please check back later!</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Events Tab -->
                        <div class="tab-pane fade" id="events" role="tabpanel">
                            <div class="mt-4">
                                <h3 class="mb-4">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                    Upcoming Events
                                </h3>
                                <p class="text-muted mb-4">Stay updated with our latest events and activities</p>

                                <?php if ($events_result->num_rows > 0): ?>
                                    <div class="events-grid">
                                        <?php while ($event = $events_result->fetch_assoc()): ?>
                                            <div class="event-card">
                                                <?php if (!empty($event['img_path'])): ?>
                                                    <img src="../<?php echo htmlspecialchars($event['img_path']); ?>"
                                                        alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-image">
                                                <?php endif; ?>
                                                <div class="event-content">
                                                    <h5 class="event-title"><?php echo htmlspecialchars($event['title']); ?>
                                                    </h5>
                                                    <p class="event-description">
                                                        <?php echo htmlspecialchars($event['description']); ?></p>
                                                    <div class="event-date">
                                                        <i class="fas fa-calendar"></i>
                                                        <?php echo date('F j, Y', strtotime($event['schedule'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-alt"></i>
                                        <h5>No Events Scheduled</h5>
                                        <p>There are currently no upcoming events. Check back soon for new announcements!
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.querySelector('.sidebar-toggle');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function () {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
            }
        });

        // Add animation to program cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all program cards
        document.querySelectorAll('.program-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Observe all event cards
        document.querySelectorAll('.event-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>

</html>