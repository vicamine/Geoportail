<?php
    ob_start();
?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<div id="data">

    <div id="container"></div>

    <div id="selecteurs">
        
        <div id="fondCarte">
            <h3> Fond de carte </h3>
            <select id="fond" name="fond" onchange="fondChange(this.value)">
                <option value="fond_de_carte_georep"> Georep </option>
                <option value="fond_de_carte_osm" selected> OpenStreetMap </option>
                <option value="fond_de_carte_stamen_terrain"> Stamen - Terrain </option>
                <option value="fond_de_carte_stamen_toner"> Stamen - Toner </option>
                <option value="fond_de_carte_stamen_watercolor"> Stamen - Watercolor </option>
            </select>
        </div>

        <div id="filtres">
            <h3> Filtres </h3>
            <h4>Offering</h4>
            <select name="offering" id="offering" onchange="updateObservablePropertyAndFoi()">
                <option value="none" selected> --- </option>
            </select>
            <h4>Observable Property</h4>
            <select name="observableProperty" id="observableProperty" onchange="updateMapAndFoi()">
                <option value="none" selected> --- </option>
            </select>
            <h4>Feature Of Interest</h4>
            <select name="foi" id="foi" onchange="enableButton()">
                <option value="none" selected> --- </option>
            </select>
            <input type="button" id="valider" value="valider" onclick="resultatSOS()" disabled="true"/>
        </div>

        <div id='map' class="map"></div>

    </div>

</div>

<script>
    <?php require 'mapSOS.js'; ?>
</script>

<script defer>
    const chart = Highcharts.chart('container', {
        chart: {
            type: 'line'
        },
        title: {
            text: 'Mesure'
        },
        xAxis: {
            categories: []
        },
        yAxis: {
            title: {
                text: 'valeur'
            }
        },
        series: [{
            name: "",
            data: []
        }]
    });
    
    createGML("../SOS/SOScapa.json");
    initMap();
    initSOS();
</script>

<?php
    $content = ob_get_clean();
    include("layout.php");
?>
