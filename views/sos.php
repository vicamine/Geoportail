<?php
    ob_start();
?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
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
    });
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

<div id="capa"></div>

<div id="container" style="width:100%; height:400px;"></div>

<h3> Fond de carte </h3>
<div id="fondCarte">
    <select id="fond" name="fond" onchange="fondChange(this.value)">
        <option value="fond_de_carte_georep"> Georep </option>
        <option value="fond_de_carte_osm" selected> OpenStreetMap </option>
        <option value="fond_de_carte_stamen_terrain"> Stamen - Terrain </option>
        <option value="fond_de_carte_stamen_toner"> Stamen - Toner </option>
        <option value="fond_de_carte_stamen_watercolor"> Stamen - Watercolor </option>
    </select>
</div>

<div id='map' class="map"></div>

<script>
    <?php require 'mapSOS.js'; ?>
</script>

<script defer>
    createGML("../SOS/SOScapa.json");
    initMap();
</script>

<?php
    $content = ob_get_clean();
    include("layout.php");
?>
