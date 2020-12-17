<?php
    ob_start();
?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $.ajax({
            url: '../sosAPI.php',
            type: 'POST',
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
</script>
<div id="container" style="width:100%; height:400px;"></div>

<?php
    $content = ob_get_clean();
    include("layout.php");
?>
