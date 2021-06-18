<html lang="fr">
    <head>
        <title>Géoportail</title>
        <meta charset="UTF-8">
        <meta name="description" content="Site de visualisation cartographique">
        <meta name="keywords" content="Géographie, Map, Carte, Nouvelle-Calédonie, NC">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.4.3/css/ol.css" type="text/css">
        <link href="/<?php echo $ROOT;?>/views/css/master.css" rel="stylesheet">
        <link href="/<?php echo $ROOT;?>/views/css/<?php echo $PATH;?>.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.4.3/build/ol.js"></script>
    </head>
    <body>
        <header>
            <h1><a href="/<?php echo $ROOT;?>/index.php/main">Géoportail</a></h1>
            <nav>
                <ul class="nav__links">
                    <li><a href="/<?php echo $ROOT;?>/index.php/sos">Séries</a></li>
                    <?php if(!isset($_SESSION['id'])){ ?>
                        <li><a href="/<?php echo $ROOT;?>/index.php/login">Se connecter</a></li>
                        <li><a href="/<?php echo $ROOT;?>/index.php/register">S'enregistrer</a></li>
                    <?php } else { ?>
                        <li><a href="/<?php echo $ROOT;?>/index.php/user"><?php echo $_SESSION['login'] ?></a></li>
                        <li><a href="/<?php echo $ROOT;?>/index.php/logout">Se déconnecter</a></li>
                    <?php } ?>
                </ul>
            </nav>
        </header>

        <div class="content">
        <?php if (isset($content)) {
            echo $content;
        } ?>
        </div>
        <footer>
            <p><img class="CNRT" src="../images/logoCNRT.png" alt="logo CNRT"></p>
            <p><img class="UNC" src="../images/logoUNC.png" alt="logo UNC"></p>
        </footer>
    </body>
</html>
