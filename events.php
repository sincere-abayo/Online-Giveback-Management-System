<!-- Modern Events Section -->
<section class="events-page py-5">
    <div class="container">
        <!-- Page Header -->
        <div class="events-header text-center mb-5">
            <div class="page-badge">
                <span class="badge-text">Dufatanye Events</span>
            </div>
            <h1 class="page-title">Upcoming Events</h1>
            <p class="page-subtitle">Join us in making a difference through community engagement and charitable
                initiatives</p>
        </div>

        <!-- Events Grid -->
        <div class="events-grid">
            <?php 
            $events = $conn->query("SELECT * FROM events order by date(schedule) asc ");
            if($events->num_rows > 0):
                while($row = $events->fetch_array()):
            ?>
            <div class="event-card">
                <div class="event-image-container">
                    <img src="<?php echo validate_image($row['img_path']) ?>" alt="<?php echo $row['title'] ?>"
                        class="event-image">
                    <div class="event-date">
                        <span class="day"><?php echo date("d", strtotime($row['schedule'])) ?></span>
                        <span class="month"><?php echo date("M", strtotime($row['schedule'])) ?></span>
                    </div>
                    <div class="event-overlay">
                        <button class="btn-view-event read_more" data-id="<?php echo $row['id'] ?>">
                            <i class="fas fa-eye"></i>
                            View Details
                        </button>
                    </div>
                </div>
                <div class="event-content">
                    <div class="event-meta">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?php echo date("F j, Y", strtotime($row['schedule'])) ?></span>
                    </div>
                    <h3 class="event-title"><?php echo $row['title'] ?></h3>
                    <p class="event-description"><?php echo substr(strip_tags($row['description']), 0, 120) ?>...</p>
                    <div class="event-actions">
                        <button class="btn-primary read_more" data-id="<?php echo $row['id'] ?>">
                            Learn More
                            <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <div class="no-events">
                <div class="no-events-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h3>No Upcoming Events</h3>
                <p>Stay tuned! We're planning exciting events for the community. Check back soon for updates.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<script>
$(function() {
    $('.read_more').click(function() {
        uni_modal("<i class='fa fa-calendar-day'></i> Event Details", 'view_event.php?id=' + $(this)
            .attr('data-id'), 'large')
    })

    // Add loading animation for buttons
    $('.btn-primary, .btn-view-event').click(function() {
        $(this).addClass('loading');
        setTimeout(() => {
            $(this).removeClass('loading');
        }, 2000);
    });
})
</script>

<style>
/* Loading state for buttons */
.btn-primary.loading,
.btn-view-event.loading {
    pointer-events: none;
    opacity: 0.7;
}

.btn-primary.loading::after,
.btn-view-event.loading::after {
    content: '';
    width: 16px;
    height: 16px;
    margin-left: 10px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    display: inline-block;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>