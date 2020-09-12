<?php require 'classes/main.class.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title>Theme browser</title>

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"
        integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ=="
        crossorigin="anonymous">

  <!-- Important Owl stylesheet -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.theme.min.css">

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>

<div class="container-fluid">

  <h1>Cydia Theme Browser <small><a
        href="https://www.reddit.com/r/iOSthemes/comments/3shvqc/release_browse_themes_by_picture/">Reddit</a></small></h1>

  <p>Total themes: <?php echo $results['count']; ?>
  <br>Total pages: <?php echo $total_pages; ?></p>

  <p>Last updated: <?php echo $results['thisversionrun']; ?></p>

  <div id="theme-container">

    <?php

    foreach (array_chunk($final, 4, TRUE) as $row) {
      ?>
      <div class="row theme-detail">
      <div class="col-md-12">
        <h5>Page <?php echo !empty($_GET['page']) ? $_GET['page'] : 1; ?></h5>
        <hr>
      </div>
      <?php
      foreach ($row as $id => $package) {

        ?>
        <div class="col-md-3">
        <h2><?php echo $package['name']; ?>
          <small>v<?php echo $package['version']['text']; ?></small>
        </h2>
        <p><?php echo $package['date']; ?></p>

        <p><?php echo $package['category']; ?></p>

        <p><a class="btn btn-info" href="cydia://package/<?php echo $id; ?>">Open in Cydia</a>
          <a class="btn btn-default" href="<?php echo $package['depictionurl']; ?>">More info</a>
        </p>

        <div id="carousel-<?php echo md5($id); ?>" class="owl-carousel"> <?php

          foreach ($package['pics'] as $key => $link) {
            ?>
            <div>
              <img class="<?php echo $key == 0 ? 'lazy' : 'lazyOwl'; ?>"
                   data-src="<?php echo $link; ?>" data-original="<?php echo $link; ?>" alt=""
                   width="100%" height="auto">
            </div>
            <?php
          }
          ?> </div>

        </div><?php
      }
      ?></div><?php
    }
    ?>
  </div>

  <nav>
    <ul class="pagination">
      <li class="<?php echo empty($_GET['page']) || $_GET['page'] == 1 ? 'disabled' : ''; ?>">
        <a class="previous" href="index.php?page=<?php echo !empty($_GET['page']) ? ($_GET['page'] - 1) : 1; ?>"
           aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>

      <?php for ($x = 1; $x <= $total_pages; $x++): ?>
        <li class="<?php echo (!empty($_GET['page']) && $_GET['page'] == $x) ? 'active' : ''; ?>"><a
            href='index.php?page=<?php echo $x; ?>'><?php echo $x; ?></a></li>
      <?php endfor; ?>

      <li
        class="<?php echo !empty($_GET['page']) && $_GET['page'] == $total_pages ? 'disabled' : ''; ?>">
        <a class="next"
           href="index.php?page=<?php echo !empty($_GET['page']) ? ($_GET['page'] + 1) : 2; ?>"
           aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    </ul>
  </nav>

</div>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script
  src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>
<script
  src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="assets/jquery-ias.min.js"></script>

<script type="text/javascript">
  $(document).ready(function () {

    $("img.lazy").lazyload({
      skip_invisible: true
    });

    $(".owl-carousel").owlCarousel({
      singleItem: true,
      lazyLoad: true,
    });

    var ias = jQuery.ias({
      container: '#theme-container',
      item: '.theme-detail',
      pagination: '.pagination',
      next: '.next'
    });

    jQuery.ias().extension(new IASPagingExtension());
    ias.extension(new IASSpinnerExtension({
    }));

    ias.on('rendered', function (data, items) {
      var $data = $(data);

      $data.each(function () {
        $('img.lazy', this).lazyload({
          skip_invisible: true
        });
        $('.owl-carousel', this).owlCarousel({
          singleItem: true,
          lazyLoad: true,
        });
      });
    })

  });
</script>
<script type="text/javascript">
  (function (i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function () {
        (i[r].q = i[r].q || []).push(arguments)
      }, i[r].l = 1 * new Date();
    a = s.createElement(o),
      m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
  })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

  ga('create', 'UA-62079540-1', 'auto');
  ga('send', 'pageview');
</script>
</body>
</html>