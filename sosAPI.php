<?php
    if ( isset( $_GET['request'] ) ) {
        if ( $_GET['request'] == 'result' ) {
            header('Content-type: application/json');
            $url ="http://localhost/geoportail/SOS/index2.php?request=GetResult&version=2.0.0&observedProperty="+$_GET['observableProperty']+"&featureOfInterest="+$_GET['foi']+"&procedure="+$_GET['procedure'];
            //$url = "http://localhost/geoportail/SOS/index2.php?request=GetResult&version=2.0.0&observedProperty=http://grimm-dev.unc.prod/sos/observableProperty/extractionMiniereSaprolitesHumidesAnnuelle&featureOfInterest=http://grimm-dev.unc.prod/featureOfInterest/02&procedure=http://grimm-dev.unc.prod/sos/procedure/dimenc&featureOfInterest=http://grimm-dev.unc.prod/featureOfInterest/02";
	        $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded",
                    'method'  => 'GET'
                )
            );
            $context = stream_context_create( $options );
            $json = file_get_contents($url, false, $context);
            echo $json;
        }
        elseif ($_GET['request'] == 'capabilities') {
            header('Content-type: application/json');
            $url ="http://localhost/geoportail/SOS/index2.php?request=GetCapabilities&version=2.0.0";
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded",
                    'method'  => 'GET'
                )
            );
            $context = stream_context_create( $options );
            $json = file_get_contents( $url, false, $context );
            $filename = "./SOS/capabilities.json";
            $file = fopen($filename,"w");
            fwrite($file, $json);
            fclose($file);
            echo $json;
        }
    }

?>
