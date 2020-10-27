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
            echo $user;
            if ($user != null) {
                $_SESSION['id'] = $user['userid'];
                $_SESSION['login'] = $user['login'];
                echo $_SESSION['id'];
                header("Location: /" . $ROOT . "/index.php/main");
            } else {
                $error = "Nom d'utilisateur ou mot de passe incorrecte.";
            }
        }
        include("views/login.php");
    }


    function register_action($nom, $prenom, $login, $password, $URI, $ROOT, $PATH) {
        if ($nom !=null && $prenom !=null && $login !=null && $password !=null) {
            $success = insert_user($nom, $prenom, $login, $password);
            if ($success) {
                header('Location: /' .$ROOT. '/index.php/main');
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


    function addLayer_action($type, $URI, $ROOT, $PATH) {
        if ($type != null) {
            $form = getForm($type, $URI);
        }
        include('views/addLayer.php');
    }

?>
