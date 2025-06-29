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
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .main-container {
            padding: 30px 0;
        }

        .content-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }

        .section-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
        }

        .section-title {
            color: #667eea;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .section-title i {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .program-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
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
            background: linear-gradient(45deg, #667eea, #764ba2);
        }

        .program-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .program-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
        }

        .program-title {
            color: #667eea;
            font-weight: 700;
            font-size: 1.4rem;
            margin: 0;
        }

        .activity-count {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .program-description {
            color: #6c757d;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .activities-list {
            margin-top: 20px;
        }

        .activity-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #28a745;
            transition: all 0.3s ease;
            position: relative;
        }

        .activity-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .activity-item.volunteer-joined {
            border-left-color: #ffc107;
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        }

        .activity-item.volunteer-joined::after {
            content: '✓ Joined';
            position: absolute;
            top: 10px;
            right: 15px;
            background: #ffc107;
            color: #856404;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .activity-name {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .activity-description {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .event-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .event-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .event-content {
            padding: 20px;
        }

        .event-title {
            font-weight: 700;
            color: #495057;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .event-description {
            color: #6c757d;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .event-date {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
        }

        .stats-card {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 30px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            padding: 15px 25px;
            border-radius: 10px 10px 0 0;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
        }

        .nav-tabs .nav-link:hover {
            border: none;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .content-card {
                padding: 20px;
                margin: 10px;
            }

            .section-title {
                font-size: 1.4rem;
                flex-direction: column;
                gap: 10px;
            }

            .program-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .events-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <img src="../uploads/GMS.png" width="30" height="30" class="d-inline-block align-top" alt="GMS">
                GMS - Programs & Events
            </a>

            <div class="navbar-nav ml-auto">
                <span class="navbar-text mr-3">
                    Welcome, <?php echo htmlspecialchars($_SESSION['volunteer_name']); ?>
                </span>
                <a href="../volunteer_dashboard.php" class="btn btn-outline-primary btn-sm mr-2">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="profile.php" class="btn btn-outline-info btn-sm mr-2">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="../logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="container">
            <!-- Statistics -->
            <div class="content-card">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo $programs_result->num_rows; ?></div>
                            <div class="stats-label">Active Programs</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo $events_result->num_rows; ?></div>
                            <div class="stats-label">Upcoming Events</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo count($volunteer_activity_ids); ?></div>
                            <div class="stats-label">Your Activities</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="content-card">
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
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-project-diagram"></i>
                                Our Programs & Activities
                            </h2>
                            <p class="text-muted">Explore the various programs and activities available for volunteers
                            </p>
                        </div>

                        <?php if ($programs_result->num_rows > 0): ?>
                                <?php while ($program = $programs_result->fetch_assoc()): ?>
                                        <div class="program-card">
                                            <div class="program-header">
                                                <h3 class="program-title"><?php echo htmlspecialchars($program['name']); ?></h3>
                                                <span class="activity-count">
                                                    <?php echo $program['activity_count']; ?> Activities
                                                </span>
                                            </div>
                                    
                                            <p class="program-description"><?php echo htmlspecialchars($program['description']); ?></p>
                                    
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
                                                                <div class="activity-name"><?php echo htmlspecialchars($activity['name']); ?></div>
                                                                <div class="activity-description"><?php echo htmlspecialchars($activity['description']); ?></div>
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
                                    <h4>No Programs Available</h4>
                                    <p>There are currently no active programs. Please check back later!</p>
                                </div>
                        <?php endif; ?>
                    </div>

                    <!-- Events Tab -->
                    <div class="tab-pane fade" id="events" role="tabpanel">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-calendar-alt"></i>
                                Upcoming Events
                            </h2>
                            <p class="text-muted">Stay updated with our latest events and activities</p>
                        </div>

                        <?php if ($events_result->num_rows > 0): ?>
                                <div class="events-grid">
                                    <?php while ($event = $events_result->fetch_assoc()): ?>
                                            <div class="event-card">
                                                <?php if (!empty($event['img_path'])): ?>
                                                        <img src="../<?php echo htmlspecialchars($event['img_path']); ?>" 
                                                             alt="<?php echo htmlspecialchars($event['title']); ?>" 
                                                             class="event-image">
                                                <?php endif; ?>
                                                <div class="event-content">
                                                    <h4 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h4>
                                                    <p class="event-description"><?php echo htmlspecialchars($event['description']); ?></p>
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
                                    <h4>No Events Scheduled</h4>
                                    <p>There are currently no upcoming events. Check back soon for new announcements!</p>
                                </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
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