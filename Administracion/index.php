<?php
ob_start();
require_once "config/sessionControl.php";
require_once "router/router.php";
require_once "views/layout/head.php";
// require_once "views/layout/navbar.php";
?>

<div class="container-fluid">
  <div class="row">
    <?php
    $vista = router();
    if (!file_exists($vista)) {
      echo "Error, REVISA TUS RUTAS";
    } else {
      require_once($vista);
    }
    ?>
  </div>
</div>

<?php
require_once "views/layout/footer.php";
?>
