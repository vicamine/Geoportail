<?php

    require_once 'model.php';

    $host = 'http://localhost:8080/geoserver/wms?';
    $finalUrl = $host.explode('?', $url)[1];

    if ( strtolower($request) == 'getcapabilities') {
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="Capabilities.xml"');
        $file = file_get_contents($finalUrl);
        $doc = new DOMDocument();
        $doc->loadXML($file);
        $res = getAllPrivate();
        $private = array();
        $toRemove = array();

        if ( !is_array($res) ) {
            $res = array( $res );
        }

        foreach ( $res as $value ) {
            array_push($private, str_replace('.', ':', $value['layername']));
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
        echo($res);
    }
    else if ( strtolower($request) == 'getmap' ) {
        $res = getAllPrivate();
        $private = array();

        if ( !is_array($res) ) {
            $res = array( $res );
        }

        foreach ( $res as $value ) {
            array_push($private, str_replace('.', ':', $value['layername']));
        }

        $urlElem = parse_url( strtolower( $finalUrl ), PHP_URL_QUERY );
        $urlElem = explode('&', $urlElem);
        $elem = array();
        foreach ($urlElem as $value) {
            $value = explode('=', $value);
            $elem[$value[0]] = $value[1];
        }

        if ( !in_array( urldecode($elem['layers']), $private) ) {
            $image = imagecreatefrompng($finalUrl);
            imagealphablending($image, false);
            imagesavealpha($image, true);
            header('Content-type: image/png');
            imagepng($image);
        }
        else {
            echo 'Vous ne disposez pas des droits pour visualiser cette layer.';
        }
    }
    else if ( strtolower($request) == 'getfeatureinfo' ) {
        $res = getAllPrivate();
        $private = array();

        if ( !is_array($res) ) {
            $res = array( $res );
        }

        foreach ( $res as $value ) {
            array_push($private, str_replace('.', ':', $value['layername']));
        }

        $urlElem = parse_url( strtolower( $finalUrl ), PHP_URL_QUERY );
        $urlElem = explode('&', $urlElem);
        $elem = array();
        foreach ($urlElem as $value) {
            $value = explode('=', $value);
            $elem[$value[0]] = $value[1];
        }

        if ( !in_array( urldecode($elem['layers']), $private) ) {
            header('Content-type: text/html');
            readfile($finalUrl);
        }
        else {
            echo 'Vous ne disposez pas des droits pour visualiser cette layer.';
        }
    }

 ?>
