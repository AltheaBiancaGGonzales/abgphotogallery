<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Photo Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .thumb-img {
            height: 120px;
            object-fit: cover;
        }
        .carousel-inner img {
            max-height: 600px;
            object-fit: cover;
            margin: auto;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Photo Gallery</a>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#">My Cats</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Others</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <div id="catCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $images = glob("images/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
            foreach ($images as $index => $imgPath) {
                $active = $index === 0 ? 'active' : '';
                echo "<div class='carousel-item $active'>
                        <img src='$imgPath' class='d-block w-100' alt='Cat $index'>
                      </div>";
            }
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#catCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#catCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <h5>Photo Gallery</h5>
    <div class="row">
        <?php
        foreach ($images as $imgPath) {
            echo "<div class='col-6 col-sm-4 col-md-3 mb-3'>
                    <img src='$imgPath' class='img-fluid thumb-img rounded border'>
                  </div>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
