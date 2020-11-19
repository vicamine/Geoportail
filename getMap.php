<?php
    if (isset($_GET['DOMAIN']) && isset($_GET['REQUEST'])) {
        if ($_GET['REQUEST'] == 'GetMap') {
            $url = explode('?', basename($_SERVER['REQUEST_URI']))[1];
            $url = $_GET['DOMAIN'].$url;
            $image = imagecreatefrompng($url);
            imagealphablending($image, false);
            imagesavealpha($image, true);
            header('Content-type: image/png');
            imagepng($image);
        }
        else if ($_GET['REQUEST'] == 'GetFeatureInfo') {
            $url = explode('?', basename($_SERVER['REQUEST_URI']))[1];
            $url = $_GET['DOMAIN'].$url;
            header('Content-type: text/html');
            $file = file_get_contents($url);
            echo $file;
        }
    }
?>
