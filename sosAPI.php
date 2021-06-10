<?php

    if ( isset( $_GET['request'] ) ) {
        if ( $_GET['request'] == 'result' ) {
            header('Content-type: application/json');
            $json = file_get_contents('./SOS/index2.php?request=GetResult&version=2.0.0&observedProperty='+ $_GET['observableProperty'] +'&featureOfInterest='+ $_GET['foi']);
            echo $json;
        }
        elseif ($_GET['request'] == 'capabilities') {
            header('Content-type: application/json');
            $json = file_get_contents('./SOS/index2.php?request=GetCapabilities&version=2.0.0');
            $filename = "./SOS/capapabilities.json";
            $file = fopen($filename,"w");
            fwrite($file, $json);
            fclose($file);
            echo $json;
        }
    }

?>
