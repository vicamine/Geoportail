<html lang="fr">
    <head>
        <title>Géoportail</title>
        <meta charset="UTF-8">
        <meta name="description" content="Site de visualisation cartographique">
        <meta name="keywords" content="Géographie, Map, Carte, Nouvelle-Calédonie, NC">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.4.3/css/ol.css" type="text/css">
        <style>
            html{
                font-family: Arial;
            }
            .map {
                height: 400px;
                width: 800px;
                border: solid black 2px;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.4.3/build/ol.js"></script>
    </head>
    <body>
        <header>
            <h1><a href="/<?php echo $ROOT;?>/index.php/main">Géoportail</a></h1>
        </header>

        <div class="content">
        <?php if (isset($content)) {
            echo $content;
        } ?>
        </div>

        <footer>
            <h1>Dabrion Victor-Emmanuel (2020)</h1>
        </footer>
    </body>
</html>
