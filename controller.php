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
                $dataList = array( 'host' => $host_data, 'port' => $port_data, 'database' => $db_data, 'user' => $user_data, 
                    'password' => $password_data,
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

            if ($type == 'shapefile') {
                if (isset($dataList['error'])) {
                    if ( $dataList['error'] == 0 ) {
                        echo "<script>alert(\"Layer ajoutée !\")</script>";
                    }
                    else if ( $dataList['error'] == 1 ) {
                        $error = 'La layer est déja présente ou corrompue !';
                    }
                    else if ( $dataList['error'] == 2 ){
                        $error = 'Vous ne pouvez uploader qu\'un seule shapefile à la fois !';
                    }
                    else if ( $dataList['error'] == 3 ){
                        $error = 'Aucun shapefile trouvé.';
                    }
                    else if ( $dataList['error'] == 4 ){
                        $error = 'Erreur lors de l\'ajout du ou des styles';
                    }
                }
            }
            elseif ($type == 'style') {
                if (isset($dataList['error'])) {
                    if (!$dataList['error']) {
                        echo "<script>alert(\"Styles ajoutées !\")</script>";
                        $error = '';
                    }
                    else {
                        $error = 'Style not added or partially !';
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
            delPrivacy($data);
        }
        else if ($action == 'SupprimerStyle') {
            deleteStyle($data);
        }
        else if ($action == 'Layer') {
            $layer = $data;
        }
        include('views/user.php');
    }


    function API_action($request, $url) {
        include("API/wms.php");
    }


    function sos_action( $URI, $ROOT, $PATH ) {
        include("views/sos.php");
    }
?>
