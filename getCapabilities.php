<?php

    require_once 'model.php';

    if( isset($_REQUEST['request']) && isset($_REQUEST['url'])) {
        if ( $_REQUEST['request'] == 'capabilities') {
            $file = file_get_contents($_REQUEST['url']);
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
            echo $res;
        }
    }

?>
