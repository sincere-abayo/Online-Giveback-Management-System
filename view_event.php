<?php 
require_once('config.php');
$qry = $conn->query("SELECT * from `events` where id = '{$_GET['id']}' ");
if($qry->num_rows > 0){
    foreach($qry->fetch_assoc() as $k => $v){
        $$k=$v;
    }
}
?>
<style>
    #uni_modal .modal-content {
        border-radius: 20px;
        overflow: hidden;
        border: none;
    }
    #uni_modal .modal-header {
        background-color: #2c5aa0;
        color: #fff;
        border: none;
        padding: 1.5rem;
    }
    #uni_modal .modal-body {
        padding: 0 !important;
    }
    #uni_modal .modal-footer {
        border: none;
        padding: 1.5rem;
        background: #fff;
        color: #222;
    }
    .event-modal-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        object-position: center;
    }
    .event-modal-content {
        padding: 2rem;
        background-color: #fff;
        color: #222;
    }
    .event-modal-date {
        background-color: #2c5aa0;
        color: #fff;
        padding: 10px 20px;
        border-radius: 25px;
        display: inline-block;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .event-modal-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c5aa0;
        margin-bottom: 1rem;
    }
    .event-modal-description {
        color: #6c757d;
        line-height: 1.8;
        font-size: 1.1rem;
    }
</style>

<div class="event-modal">
    <div class="event-modal-image-container">
        <img src="<?php echo validate_image($img_path) ?>" class="event-modal-image" alt="<?php echo $title ?>">
    </div>
    <div class="event-modal-content">
        <div class="event-modal-date">
            <i class="fas fa-calendar-alt me-2"></i>
            <?php echo date("F j, Y", strtotime($schedule)) ?>
        </div>
        <h2 class="event-modal-title"><?php echo $title ?></h2>
        <div class="event-modal-description">
            <?php echo $description ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-times me-2"></i>Close
    </button>
    <button type="button" class="btn btn-primary">
        <i class="fas fa-share me-2"></i>Share Event
    </button>
</div>