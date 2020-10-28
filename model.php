<?php

    function connectToDB(){
        include('database.php');
        $sql = pg_connect('host='.$host.' port='.$port.' dbname='.$dbname.' user='.$login.' password='.$password );
        return $sql;
    }


    function disconnectFromDB($sql){
        pg_close($sql);
    }


    function doPreparedSelect($requete, $params){

        $sql=connectToDB();
        $result=pg_prepare($sql, "prepared_query", $requete);
        $result = pg_execute($sql, "prepared_query", $params);
        $row = pg_fetch_all($result);
        pg_free_result($result);
        disconnectFromDB($sql);
        return $row;

    }


    function doPreparedRequest($requete, $params){

        $sql=connectToDB();
        $result=pg_prepare($sql, "prepared_query", $requete);
        $result = pg_execute($sql, "prepared_query", $params);
        disconnectFromDB($sql);

    }


    function isUser($login, $password){

        $sql=connectToDB();
        $result=pg_prepare($sql, "prepared_query", 'SELECT * FROM admin.user WHERE login=$1 AND password=$2');
        $result = pg_execute($sql, "prepared_query", array($login, $password));
        $rows = pg_fetch_assoc($result);
        pg_free_result($result);
        disconnectFromDB($sql);

        if ( $rows != null ) {
            return $rows;
        }
        return null;
    }


    function insert_user( $nom, $prenom, $login, $password ) {
        if ( !verification_login ($login)) {
            return false;
        } else {
            $query = 'INSERT INTO admin.user (nom, prenom, login, password) VALUES ( $1, $2, $3, $4 )';
            $params = [ $nom, $prenom, $login, $password ];
            doPreparedRequest( $query, $params );
            return true;
        }
    }

    function verification_login($login){
        $query = "SELECT * FROM admin.user WHERE login=$1";
        $result = doPreparedSelect($query, array($login));
        if (is_array($result)) {
            if (sizeof($result) == 0) {
                return true;
            }
        } else if ($result == null) {
            return true;
        }
        return false;
    }


    function getCurrentPath($URI){
        $PATH = explode("/", $URI);
        $i = 0;
        $found = false;
        while($i < sizeof($PATH)-1 && $found != true) {
            if ($PATH[$i] == "index.php") {
                $found = true;
            }
            $i++;
        }
        return $PATH[$i];
    }


    function getCompletePath($URI){
        $PATH = explode("/", $URI);
        $i = 0;
        $found = false;
        $COMPLETE_PATH =[];
        while($i < sizeof($PATH)-1) {
            if ($PATH[$i] == "index.php") {
                $found = true;
            }
            $i++;
            if ($found){
                $COMPLETE_PATH[] = $PATH[$i];
            }

        }
        return $COMPLETE_PATH;
    }


    function getForm($type, $URI) {
        if ($type == 'postgis') {
            $form = '<form action="'.$URI.'" method="post">

                <h2> Store</h2>

                <label for="store"> Nom du store : </label>
                <input type="text" id="store" name="store" required>
                <br>
                <label for="description"> Description : </label>
                <input type="text" id="description" name="description" required>

                <h2> Information de connexion </h2>

                <label for="host"> host : </label>
                <input type="text" id="host" name="host" required>
                <br>
                <label for="port"> port : </label>
                <input type="text" id="port" name="port" required>
                <br>
                <label for="database"> database : </label>
                <input type="text" id="database" name="database" required>
                <br>
                <label for="schema"> schema : </label>
                <input type="text" id="schema" name="schema" required>
                <br>
                <label for="user"> user : </label>
                <input type="text" id="user" name="user" required>
                <br>
                <label for="password"> password : </label>
                <input type="password" id="password" name="password" required>
                <br>
                <input type="submit" value="Ajouter la base de donnée" required>
            </form>';
        }
        elseif ($type =='shapefile') {
            $form = null;
        }
        return $form;
    }


    function create_workspace($login) {
        $url = "http://localhost:8080/geoserver/rest/workspaces"; // your address can change depending upon the configuration of the geoserver
        $ch = curl_init( $url );

        // Setup request to send json via POST.
        $payload = json_encode( array( "workspace"=> array('name' => $login) ) ); //supply json as payload
        curl_setopt($ch, CURLOPT_POST, True);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver'); //credentials (user:password)
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        // Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        // Send request.
        $result = curl_exec($ch);
        curl_close($ch);
        // Print response.
        echo "<pre>$result</pre>";
    }





?>
