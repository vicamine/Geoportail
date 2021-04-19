<?php

    require_once 'model.php';
    require_once 'config.php';
    
    if( isset($_REQUEST['REQUEST'])) {
        if ( $_REQUEST['REQUEST'] == 'capabilities') {
            $url = $domain.'SERVICE=wms&VERSION=1.1.1&REQUEST=GetCapabilities';
            $file = file_get_contents($url);
            $doc = new DOMDocument();
            $doc->loadXML($file);
            $res = getAllPrivate();
            $private = array();
            $toRemove = array();

            if ( !is_array($res) ) {
                $res = array( $res );
            }

            if ( $_REQUEST['user'] == 'null') {
                foreach ( $res as $value ) {
                    array_push($private, str_replace('.', ':', $value['layername']));
                }
            }
            else {
                foreach ( $res as $value ) {
                    if ( strpos($value['layername'], $_REQUEST['user']) !== 0 ) {
                        array_push($private, str_replace('.', ':', $value['layername']));
                    }
                }
            }

            $layers = $doc->getElementsByTagName('Layer');
            foreach ($layers as $value) {
                $layer = $value->getElementsByTagName('Name')->item(0);
                $name = $layer->nodeValue;
                if ( in_array($name, $private) ) {
                    array_push( $toRemove, $value);
                }
            }
            foreach ($toRemove as $value) {
                $value->parentNode->removeChild($value);
            }
            $res = $doc->saveXML();
            header('Content-type: text/xml');
            echo $res;
        }
        else if ($_GET['REQUEST'] == 'GetMap') {
            $url = explode('?', basename($_SERVER['REQUEST_URI']))[1];
            $url = $domain.$url;
            $image = imagecreatefrompng($url);
            imagealphablending($image, false);
            imagesavealpha($image, true);
            header('Content-type: image/png');
            imagepng($image);
        }
        else if ($_GET['REQUEST'] == 'GetFeatureInfo') {
            $url = explode('?', basename($_SERVER['REQUEST_URI']))[1];
            $url = $domain.$url;
            header('Content-type: text/html');
            $file = file_get_contents($url);
            echo $file;
        }
        else if ($_GET['REQUEST'] == 'GetLegendGraphic') {
            $url = explode('?', $_SERVER['REQUEST_URI'])[1];
            $url = str_replace('"', '', $domain).$url;
            $image = imagecreatefrompng($url);
            imagealphablending($image, false);
            imagesavealpha($image, true);
            header('Content-type: image/png');
            imagepng($image);
        }
    }

?>
