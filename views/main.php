<?php
    ob_start();
?>

<div id="content">

    <div id="mapSpace">

        <div id='map' class="map"></div>

        <a id="export"> Download PNG </a>
        <a id="image-download" download="map.png"></a>

        <form class="form">
            <label>Page size </label>
            <select id="format">
                <option value="a0">A0 (slow)</option>
                <option value="a1">A1</option>
                <option value="a2">A2</option>
                <option value="a3">A3</option>
                <option value="a4" selected>A4</option>
                <option value="a5">A5 (fast)</option>
            </select>
            <label>Resolution </label>
            <select id="resolution">
                <option value="72">72 dpi (fast)</option>
                <option value="150">150 dpi</option>
                <option value="300">300 dpi (slow)</option>
            </select>
            <button id="exportPDF">Export PDF</button>
        </form>

        <h3> Legende </h3>
        <div id="legende"> </div>

        <h2> Features </h2>
        <div id='features'> </div>

    </div>
    <div id="onglet">
        <p onclick='displayCapa()'><img class="sidePanel" src="../images/bouton_close.png" alt="bouton hamburger"></p>
        <div id='contenue'>
            <h3> Fond de carte </h3>
            <div id="fondCarte">
                <select id="fond" name="fond" onchange="fondChange(this.value)">
                    <option value="fond_de_carte_georep"> Georep </option>
                    <option value="fond_de_carte_osm"> OpenStreetMap </option>
                    <option value="fond_de_carte_stamen_terrain"> Stamen - Terrain </option>
                    <option value="fond_de_carte_stamen_toner"> Stamen - Toner </option>
                    <option value="fond_de_carte_stamen_watercolor"> Stamen - Watercolor </option>
                </select>
            </div>
            <h3 id="rechercheTitre" href="javascript:void(0);" onclick="displayRecherche()">Rechercher</h3>
            <div id="recherche">
                <input type="text" id="search" name="search" value="" oninput="research()" placeholder="recherche"/>
                <ul></ul>
            </div>
        </div>
    </div>
    <div id="dataSpace">

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>

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
