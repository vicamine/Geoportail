<?php

    require_once 'model.php';
    require_once 'controller.php';

    $URI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Variable URI pour la redirection
    $PATH = getCurrentPath($URI);
    $ROOT = explode("/",$URI)[1];
    $COMPLETE_PATH = getCompletePath($URI);

    if ($PATH == 'main') {
        main_action( $ROOT, $PATH );
    }
    elseif ($PATH == 'addLayer') {
        if (isset($_POST['type'])) {
            addLayer_action($_POST['type'], $URI, $ROOT, $PATH);
        }
        else {
            addLayer_action(null, $URI, $ROOT, $PATH);
        }
    }


?>
