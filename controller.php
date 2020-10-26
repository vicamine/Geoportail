<?php

    function main_action($ROOT, $PATH){
        include("views/main.php");
    }

    function addLayer_action($type, $URI, $ROOT, $PATH) {
        if ($type != null) {
            $form = getForm($type, $URI);
        }
        include('views/addLayer.php');
    }

?>
