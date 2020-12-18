<?php

    if ( isset( $_GET['request'] ) ) {
        if ( $_GET['request'] == 'result' ) {
            header('Content-type: application/json');
            $json = file_get_contents('./json.json');
            echo $json;
        }
        else if ( $_GET['request'] == 'offering' ) {
            header('Content-type: application/json');
            $json = file_get_contents('./SOS/SOScapa.json');
            $json = json_decode($json, true);
            $res = array();
            foreach ($json as $key => $value) {
                array_push($res, $key);
            }
            $res = json_encode($res, JSON_FORCE_OBJECT);
            echo $res;
        }
        elseif ( $_GET['request'] == 'FOI' ) {
            if ( isset($_GET['procedure']) && isset($_GET['offering'])){
                header('Content-type: application/json');
                $json = file_get_contents('./SOS/SOScapa.json');
                $json = json_decode($json, true);
                $res = $json[str_replace('"', '', $_GET['offering'])]['procedure'];
                foreach ($res as $key => $value) {
                    if ( $key == str_replace('"', '', $_GET['procedure']) ) {
                        $json = $value['FOI']['shape'];
                        $json = $json[count($json)-1];
                        break;
                    }
                }
                echo $json;
            }
        }
    }

?>
