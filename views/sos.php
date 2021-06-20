<?php
    ob_start();
?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<div id="data">

    <div id="container"></div>

    <div id="selecteurs">

        <div id="filtres">
            <h3> Filtres </h3>
            <h4>Thème</h4>
            <select name="offering" id="offering" onchange="updateObservableProperty()">
                <option value="none" selected> --- </option>
            </select>
            <h4>Phénomène observé</h4>
            <select name="observableProperty" id="observableProperty" onchange="updateProcedure()">
                <option value="none" selected> --- </option>
            </select>
            <h4>Fournisseur de la donnée</h4>
            <select name="procedure" id="procedure" onchange="updateFoi()">
                <option value="none" selected> --- </option>
            </select>
            <h4>Zone observé</h4>
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
    
    //createGML("../SOS/SOScapa.json");
    initMap();
    initSOS();
</script>

<?php
    $content = ob_get_clean();
    include("layout.php");
?>
