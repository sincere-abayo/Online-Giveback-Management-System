<!DOCTYPE html>
<html>

<head>
  <title>Image Gallery</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css"
    media="screen">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <style>
    body {
      background: #f8f9fa;
      color: #333;
    }

    .gallery-card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      margin-bottom: 30px;
      position: relative;
      transition: box-shadow 0.2s;
    }

    .gallery-card:hover {
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
    }

    .gallery-img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      display: block;
    }

    .gallery-title {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      background: rgba(0, 0, 0, 0.5);
      color: #fff;
      padding: 8px 12px;
      font-size: 1rem;
      text-align: center;
      font-weight: 500;
      letter-spacing: 0.5px;
    }

    .delete-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 2;
      background: rgba(255, 255, 255, 0.85);
      border: none;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
      transition: background 0.2s;
    }

    .delete-btn:hover {
      background: #dc3545;
      color: #fff;
    }

    .delete-btn img {
      width: 20px;
      height: 20px;
    }

    .gallery-container {
      padding-top: 30px;
      padding-bottom: 30px;
    }

    .upload-btn {
      margin-bottom: 30px;
    }

    .modal-header {
      border-bottom: none;
    }

    .modal-footer {
      border-top: none;
    }
  </style>
</head>

<body>

  <div class="container gallery-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">Gallery</h2>
      <button class="btn btn-primary upload-btn" data-toggle="modal" data-target="#uploadModal">
        <span class="fa fa-upload"></span> Upload Image
      </button>
    </div>

    <!-- Feedback Messages -->
    <?php if (!empty($_SESSION['error'])) { ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Whoops!</strong> <?php echo $_SESSION['error']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php unset($_SESSION['error']);
    } ?>
    <?php if (!empty($_SESSION['success'])) { ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> <?php echo $_SESSION['success']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php unset($_SESSION['success']);
    } ?>

    <!-- Gallery Grid -->
    <div class="row">
      <?php
      $sql = "SELECT * FROM gallery ORDER BY id DESC";
      $images = $conn->query($sql);
      while ($image = $images->fetch_assoc()) {
        ?>
        <div class="col-sm-6 col-md-4 col-lg-3 d-flex align-items-stretch">
          <div class="card gallery-card w-100 mb-4">
            <a class="fancybox" rel="ligthbox" href="gallerie/uploads/<?php echo $image['image'] ?>">
              <img class="gallery-img" src="gallerie/uploads/<?php echo $image['image'] ?>"
                alt="<?php echo htmlspecialchars($image['title']) ?>" />
              <div class="gallery-title"><?php echo htmlspecialchars($image['title']) ?></div>
            </a>
            <form action="gallerie/Delete.php" method="POST">
              <input type="hidden" name="id" value="<?php echo $image['id'] ?>">
              <button type="submit" class="delete-btn" title="Delete"
                onclick="return confirm('Are you sure you want to delete this image?');">
                <img src="gallerie/uploads/x.png" alt="Delete">
              </button>
            </form>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>

  <!-- Upload Modal -->
  <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="uploadModalLabel">Upload Image</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="gallerie/Upload.php" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="form-group">
              <label for="title">Title</label>
              <input type="text" name="title" class="form-control" placeholder="Title" required>
            </div>
            <div class="form-group">
              <label for="image">Image</label>
              <input type="file" name="image" class="form-control-file" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Upload</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function () {
      $(".fancybox").fancybox({
        openEffect: "none",
        closeEffect: "none"
      });
    });
  </script>

</body>

</html>