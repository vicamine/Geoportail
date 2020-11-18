<?php
    ob_start();
?>
<?php if ( isset($_SESSION['id'])){ ?>
    <p> <a href='/<?php echo $ROOT; ?>/index.php/addLayer' > Ajouter des layers ou des styles ! </a> </p>
<?php } ?>

<h2> Map </h2>
<div id='map' class="map"></div>

<h2> Features </h2>
<div id='features'> </div>

<h2> Layer active </h2>

<div id='active'>
    <ul>

    </ul>
</div>

<h2> Capabilites </h2>

<div id='contenue'> </div>


<br/>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

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
