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

    if ($PATH == 'upload') {
        include 'upload.php';
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
    else if ($PATH == 'register') {
        if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['login']) && isset($_POST['password'])) {
            register_action($_POST['nom'], $_POST['prenom'], $_POST['login'], $_POST['password'], $URI, $ROOT, $PATH);
        } else {
            register_action(null, null, null, null, $URI, $ROOT, $PATH);
        }
    }
    else if(isset($_SESSION['id'])) {
        if($PATH == "addLayer"){
            if (isset($_POST['type'])) {
                if ($_POST['type'] == 'postgis') {
                    if (isset($_POST['store']) && isset($_POST['description']) && isset($_POST['host']) && isset($_POST['port'])
                    && isset($_POST['database']) && isset($_POST['schema']) && isset($_POST['user']) && isset($_POST['password'])){
                        $dataList = array('store' => $_POST['store'], 'description' => $_POST['description'], 'host' => $_POST['host'],
                        'port' => $_POST['port'], 'database' => $_POST['database'], 'schema' => $_POST['schema'],
                        'user' => $_POST['user'], 'password' => $_POST['password']);
                        addLayer_action($_POST['type'], $dataList, $URI, $ROOT, $PATH);
                    } else {
                        addLayer_action($_POST['type'], null, $URI, $ROOT, $PATH);
                    }
                }
                else if ( $_POST['type'] == 'shapefile' ) {
                    addLayer_action($_POST['type'], null, $URI, $ROOT, $PATH);
                }
            }
            else if ( isset($_GET['type']) && isset($_GET['error'])) {
                $dataList['error'] = $_GET['error'];
                addLayer_action($_GET['type'], $dataList, $URI, $ROOT, $PATH);
            }
            else {
                addLayer_action(null, null, $URI, $ROOT, $PATH);
            }
        } else {
            echo "<h1>Error 404 : Page not found.</h1>";
        }
    }
    else {
        echo "<h1>Error 404 : Page not found.</h1>";
    }


?>
