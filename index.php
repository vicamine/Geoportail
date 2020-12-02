<?php

    session_start();
    require_once 'model.php';
    require_once 'controller.php';

    $URI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Variable URI pour la redirection
    $PATH = getCurrentPath($URI);
    $ROOT = explode("/",$URI)[1];
    $COMPLETE_PATH = getCompletePath($URI);


    if ($COMPLETE_PATH[0] == 'ows') {
        if ( isset($_GET['request'])){
                API_action( $_GET['request'], $_SERVER['REQUEST_URI'] );
        }
        else if ( isset($_GET['REQUEST']) ) {
                API_action( $_GET['REQUEST'], $_SERVER['REQUEST_URI']  );
        }
    }
    else if ($PATH == "logout") {
        logout_action($ROOT);
    }
    else if ($PATH == 'uploadShape') {
            include 'uploadShape.php';
    }
    else if ($PATH == 'uploadStyle') {
        include 'uploadStyle.php';
    }
    else if ($PATH == 'main') {
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
                else if ( $_POST['type'] == 'style' ){
                    addLayer_action($_POST['type'], null, $URI, $ROOT, $PATH);
                }
                else if ( $_POST['type'] == 'update' ) {
                    $dataList = array();
                    foreach ($_POST as $key => $value) {
                        $dataList[$key] = $value;
                    }
                    addLayer_action(null, $dataList, $URI, $ROOT, $PATH);
                }
            }
            else if ( isset($_GET['type']) && isset($_GET['error']) && isset($_GET['layers']) ) {
                $dataList = array( 'error' => $_GET['error'], 'lays' => $_GET['layers'] );
                addLayer_action($_GET['type'], $dataList, $URI, $ROOT, $PATH);
            }
            elseif (isset($_GET['error']) && isset($_GET['type'])) {
                if ( ($_GET['error'] == 1 || $_GET['error'] == 2 || $_GET['error'] == 3 ) && $_GET['type'] == 'shapefile') {
                    $dataList['error'] = $_GET['error'];
                    addLayer_action($_GET['type'], $dataList, $URI, $ROOT, $PATH);
                } else if ( $_GET['error'] == 0 ) {
                    $dataList['error'] = $_GET['error'];
                    addLayer_action($_GET['type'], $dataList, $URI, $ROOT, $PATH);
                }
            }
            else {
                addLayer_action(null, null, $URI, $ROOT, $PATH);
            }
        }
        else if ($PATH == 'user') {
            if ( isset($_POST['supprimer']) && isset($_POST['layer']) ) {
                user_action($_POST['supprimer'], $_POST['layer'] , $URI, $ROOT, $PATH );
            }
            else if ( isset($_POST['supprimerStyle']) && isset($_POST['style']) ) {
                user_action($_POST['supprimerStyle'].'Style', $_POST['style'] , $URI, $ROOT, $PATH );
            }
            else if ( isset($_GET['action']) && isset( $_GET['data']) ) {
                user_action( $_GET['action'], $_GET['data'] , $URI, $ROOT, $PATH );
            }
            else {
                user_action(null, null, $URI, $ROOT, $PATH);
            }
        }
        else {
            echo "<h1>Error 404 : Page not found.</h1>";
        }
    }
    else {
        echo "<h1>Error 404 : Page not found.</h1>";
    }


?>
