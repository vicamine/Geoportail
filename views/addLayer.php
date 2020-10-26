<?php
    ob_start();
?>

<section class="addLayer">
    <h2> Ajouter des layers </h1>
    <form action="<?php echo $URI; ?>" method="POST">

        <p> Choisissez le type des layers : </p>
        <div>
            <input type="radio" id='postgis' name="type" value="postgis" <?php if ($type == 'postgis') {echo 'checked=check';} ?>/>
            <label for="postgis"> Base de donnée PostGis </label>
            <br>
            <input type="radio" id='shapefile' name="type" value="shapefile" <?php if ($type == 'shapefile') {echo 'checked=check';} ?>/>
            <label for="shapefile"> Archive de shapefile au format ZIP </label>
        </div>
        <br>
        <div>
            <input type="submit" value="Valider">
        </div>
    </form>
    <?php if (isset($form)){echo $form;} ?>
</section>

<?php
    $content = ob_get_clean();
    include("layout.php");
?>
