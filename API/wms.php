<?php

    $host = 'http://localhost:8080/geoserver/wms?';
    $finalUrl = $host.explode('?', $url)[1];

    if ( strtolower($request) == 'getcapabilities') {
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="Capabilities.xml"');
        readfile($finalUrl);
    }
    else if ( strtolower($request) == 'getmap' ) {
        $image = imagecreatefrompng($finalUrl);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        header('Content-type: image/png');
        imagepng($image);
    }
    else if ( strtolower($request) == 'getfeatureinfo' ) {
        header('Content-type: text/html');
        readfile($finalUrl);
    }

 ?>
