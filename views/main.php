<?php
    ob_start();
?>
<?php if ( isset($_SESSION['id'])){ ?>
    <p> <a href='/<?php echo $ROOT; ?>/index.php/addLayer' > Ajouter des layers ! </a> </p>
<?php } ?>

<h2> Map </h2>
<div id='map' class="map"></div>

<h2> Features </h2>
<div id='features'> </div>
<h2> Capabilites </h2>
<p> Layer type | Layer name | Base projection | Style(s) </p>
<p><====================================================></p>
<div id='contenue'> </div>

<br/>

<script>
    <?php require 'map.js'; ?>
</script>

<script defer>
    initMap();
    capabilities();
</script>

<?php
    $content = ob_get_clean();
    include("layout.php");
?>
