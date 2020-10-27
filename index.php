<?php

    session_start();
    require_once 'model.php';
    require_once 'controller.php';

    $URI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Variable URI pour la redirection
    $PATH = getCurrentPath($URI);
    $ROOT = explode("/",$URI)[1];
    $COMPLETE_PATH = getCompletePath($URI);

    if ($PATH == "logout") {
        logout_action($ROOT);
    }

    if ($PATH == 'main') {
        main_action( $ROOT, $PATH );
    }
    else if ($PATH == 'login') {
        if(isset($_POST['login']) && isset($_POST['password'])){
            login_action($_POST['login'], $_POST['password'], $URI, $ROOT, $PATH);
        } else {
            login_action(null, null, $URI, $ROOT, $PATH);
        }
    }
    else if(isset($_SESSION['id'])) {
        if($PATH == "addLayer"){
            if (isset($_POST['type'])) {
                addLayer_action($_POST['type'], $URI, $ROOT, $PATH);
            } else {
                addLayer_action(null, $URI, $ROOT, $PATH);
            }
        } else {
            echo "<h1>Error 404 : Page not found.</h1>";
        }
    }
    else {
        echo "<h1>Error 404 : Page not found.</h1>";
    }


?>
