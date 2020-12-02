<?php
    ob_start();
?>

<section class="addLayer">
    <h2> Ajouter des layers </h1>
    <form action="<?php echo $URI; ?>" method="POST">

        <p> Choisissez le type des layers : </p>
        <div>
            <input type="radio" id='shapefile' name="type" value="shapefile" <?php if ($type == 'shapefile') {echo 'checked=check';} ?>/>
            <label for="shapefile"> Archive de shapefile au format ZIP </label>
            <br>
            <input type="radio" id='style' name="type" value="style" <?php if ($type == 'style') {echo 'checked=check';} ?>/>
            <label for="style"> Archive de style (SLD) au format ZIP </label>
        </div>
        <br>
        <div>
            <input type="submit" value="Valider">
        </div>
    </form>
    <?php if (isset($form)){echo $form;} ?>
    <?php if(isset($error)){
        echo "<p class='error'>".$error."</p>";
    } ?>
</section>

<?php
    $content = ob_get_clean();
    include("layout.php");
?>
