<?php
    ob_start();
?>

<div id="content">

    <div id="mapSpace">

    <div id='map' class="map"></div>

        <a id="export"> Download PNG </a>
        <a id="image-download" download="map.png"></a>

        <h3> Legende </h3>
        <div id="legende"> </div>

        <h2> Features </h2>
        <div id='features'> </div>

    </div>
    <div id="onglet">
        <h2 onclick='displayCapa()'> X </h2>
        <div id='contenue'>
            <h3> Fond de carte </h3>
            <div id="fondCarte">
                <select id="fond" name="fond" onchange="fondChange(this.value)">
                    <option value="fond_de_carte_osm"> OpenStreetMap </option>
                    <option value="fond_de_carte_stamen_terrain"> Stamen - Terrain </option>
                    <option value="fond_de_carte_stamen_toner"> Stamen - Toner </option>
                    <option value="fond_de_carte_stamen_watercolor"> Stamen - Watercolor </option>
                </select>
            </div>
        </div>
    </div>
    <div id="dataSpace">

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <script>
        <?php require 'map.js'; ?>
        </script>

        <script defer>
        initMap();
        capabilities( '<?php if (isset($_SESSION['login'])) { echo $_SESSION['login']; } else { echo 'null'; } ?>' );
        </script>

    </div>

</div>

<?php
    $content = ob_get_clean();
    include("layout.php");
?>
