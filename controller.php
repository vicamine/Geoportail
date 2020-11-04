<?php

    function main_action($ROOT, $PATH){
        include("views/main.php");
    }


    function login_action($login, $password, $URI, $ROOT, $PATH){
        if(isset($_SESSION['id'])){
            unset($_SESSION['id']);
        }

        if($login != null && $password != null) {
            $user = isUser($login, $password);
            //echo $user;
            if ($user != null) {
                $_SESSION['id'] = $user['userid'];
                $_SESSION['login'] = $user['login'];
                header("Location: /" . $ROOT . "/index.php/main");
            } else {
                $error = "Nom d'utilisateur ou mot de passe incorrecte.";
            }
        }
        include("views/login.php");
    }


    function register_action($nom, $prenom, $login, $passwd, $URI, $ROOT, $PATH) {
        if ($nom !=null && $prenom !=null && $login !=null && $passwd !=null) {
            $success = insert_user($nom, $prenom, $login, $passwd);
            if ($success) {
                create_workspace($login);
                include('database.php');
                $dataList = array( 'host' => $host, 'port' => $port, 'database' => $dbname, 'user' => $user, 'password' => $password,
                    'store' => $login, 'description' => 'Geoserver Database', 'schema' => $login, 'login' => $login);
                create_store_db($login, $dataList);
                header('Location: /' .$ROOT. '/index.php/login');
            } else {
                $error = 'Une erreur est survenue ou le login est déja pris.';
            }
        }
        include('views/register.php');
    }


    function logout_action($ROOT) {
        $_SESSION = [];
        session_destroy();
        header('Location: /' .$ROOT. '/index.php/main');
    }


    function addLayer_action($type, $dataList, $URI, $ROOT, $PATH) {
        if ($type != null) {
            $form = getForm($type, $URI);
            if ($type == 'postgis') {
                if ($dataList != null) {
                    $login = $_SESSION['login'];
                    $error = create_store_db($login, $dataList);
                    if ($error) {
                        echo "<script>alert(\"Base de donnée ajouté !\")</script>";
                        $table = getTable($dataList);
                        $dataList['login'] = $_SESSION['login'];
                        foreach ($table as $value) {
                            publishLayerDB( $value, $dataList );
                        }
                        $error = '';
                    } else {
                        $error = 'Database not added !';
                    }
                }
            }
            else {
                if ($type == 'shapefile') {
                    if (isset($dataList['error'])) {
                        if (!$dataList['error']) {
                            echo "<script>alert(\"Layers ajoutées !\")</script>";
                            $error = '';
                            include('database.php');
                            $dataList = array( 'host' => $host, 'port' => $port, 'database' => $dbname, 'user' => $user, 'password' => $password,
                                'store' => $_SESSION['login'], 'schema' => $_SESSION['login'], 'login' => $_SESSION['login']);
                            $table = getTable($dataList);
                            foreach ($table as $value) {
                                $error = publishLayerDB( $value, $dataList );
                            }
                        }
                        else {
                            include('database.php');
                            $dataList = array( 'host' => $host, 'port' => $port, 'database' => $dbname, 'user' => $user, 'password' => $password,
                                'store' => $_SESSION['login'], 'schema' => $_SESSION['login'], 'login' => $_SESSION['login']);
                            $table = getTable($dataList);
                            foreach ($table as $value) {
                                publishLayerDB( $value, $dataList );
                            }
                            $error = 'Layers not added or partially !';
                        }
                    }
                }
            }
        }
        include('views/addLayer.php');
    }


    function user_action($action, $data, $URI, $ROOT, $PATH ) {
        if ($action == 'Supprimer') {
            deleteLayer($data);
            geoDelete($data);
        }
        include('views/user.php');
    }

?>
