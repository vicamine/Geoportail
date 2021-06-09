<?php
    ob_start();
?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>


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

<div id="container"></div>

<script>
    /*document.addEventListener('DOMContentLoaded', function () {
        $.ajax({
            url: '../sosAPI.php',
            type: 'GET',
            data: {
                request: 'result',
            },
            dataType: 'json',
            success: function(res) {
                var valeur = [];
                res.Valeur.forEach(element => valeur.push(parseInt(element)));
                const chart = Highcharts.chart('container', {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: 'resultat'
                    },
                    xAxis: {
                        categories: res.Date
                    },
                    yAxis: {
                        title: {
                            text: 'mesure'
                        }
                    },
                    series: [{
                        name: 'Test',
                        data: valeur
                    }, {
                        name: 'Test2',
                        data: valeur
                    }]
                });
            }
        });
    });*/
    /*document.addEventListener('DOMContentLoaded', function () {
        $.ajax({
            url: 'http://localhost/Geoportail/SOS/index2.php?request=GetCapabilities&version=2.0.0',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                document.querySelector('#capa').innerHTML = res;
            }
        });
    });*/
</script>

<script>
    <?php require 'mapSOS.js'; ?>
</script>

<script defer>
    createGML("../SOS/SOScapa.json");
    initMap();
    initSOS();
</script>

<?php
    $content = ob_get_clean();
    include("layout.php");
?>
