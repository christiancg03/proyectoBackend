<?php
$rutaJS = GenerarRutaJs($vista ?? '');
echo (file_exists($rutaJS)) ? "<script src='$rutaJS'></script>" : "";
?>

<script src="/GameNation/Administracion/assets/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" crossorigin="anonymous"></script>
<script>
  feather.replace();
</script>
</body>
</html>
