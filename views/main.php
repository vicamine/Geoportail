<?php
    ob_start();
?>
<div id="content">

    <div id="mapSpace">

    <?php if ( isset($_SESSION['id'])){ ?>
        <p> <a href='/<?php echo $ROOT; ?>/index.php/addLayer' > Ajouter des layers ou des styles ! </a> </p>
    <?php } ?>

    <h2> Map </h2>
    <div id='map' class="map"></div>

        <h3> Legende </h3>
        <div id="legende"> </div>

        <h2> Features </h2>
        <div id='features'> </div>

    </div>
    <div id="dataSpace">

        <h3> Fond de carte </h3>
        <div id="fondCarte">
            <select id="fond" name="fond" onchange="fondChange(this.value) ">
                <option value="fond_de_carte_osm"> OpenStreetMap </option>
                <option value="fond_de_carte_stamen_terrain"> Stamen - Terrain </option>
                <option value="fond_de_carte_stamen_toner"> Stamen - Toner </option>
                <option value="fond_de_carte_stamen_watercolor"> Stamen - Watercolor </option>
            </select>
        </div>

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

    </div>

</div>

<?php
    $content = ob_get_clean();
    include("layout.php");
?>
