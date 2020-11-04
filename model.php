<?php

    function connectToDB(){
        include('database.php');
        $sql = pg_connect('host='.$host.' port='.$port.' dbname='.$dbname.' user='.$user.' password='.$password );
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

                <input name="type" type="hidden" value="postgis">

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
                <input type="text" id="schema" name="schema" value="public" required>
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
            $form = '<form action="upload" method="post" enctype="multipart/form-data">
            	<p> Select zip to upload: </p>
            	<input type="file" name="fileToUpload" id="fileToUpload">
            	<!-- <input type="file" name="files[]" id="files[]"> -->
            	<input type="submit" value="Upload zip" name="submit">
            </form>';
        }
        else {
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
        return $result;
    }


    function create_store_db($login, $dataList){
        $payload = array('dataStore' => array('name' => $dataList['store'], //name of the datastore
                                      'description' => $dataList['description'],
                                      'enabled' => true,
                                      'workspace' => array('name' => $login, 'link' => 'http://'.$login), //workspace information ,link is namespace URI
                                      'connectionParameters' => array('host' => $dataList['host'], //DB connection information
                                                                      'port' => $dataList['port'],
                                                                      'database' => $dataList['database'],
                                                                      'schema' => $dataList['schema'],
                                                                      'user' => $dataList['user'],
                                                                      'passwd' => $dataList['password'],
                                                                      'dbtype' => 'postgis'
                                                                      ),
                                      '__default' => false, //if you want to make this datastore default datastore then pass true
                                      'featureTypes' => array('test') //usually the name of the workspace
                                      )
                  );
        // echo(json_encode($payload)); die();
        $url = "http://localhost:8080/geoserver/rest/workspaces/".$login."/datastores";// your address can change depending upon the configuration of the geoserver
        $ch = curl_init( $url );

        # Setup request to send json via POST.
        curl_setopt($ch, CURLOPT_POST, True);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver');
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($payload)); //supply json as payload

        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result == $dataList['store']) {
            return true;
        }
        return false;
    }


    function getTable( $dataList ) {
        // Connexion, sélection de la base de données
        $dbconn = pg_connect("host=".$dataList['host']." dbname=".$dataList['database']." user=".$dataList['user']." password=".$dataList['password']);

        // Exécution de la requête SQL
        $query = 'SELECT table_name FROM information_schema.tables WHERE table_schema=\''.$dataList['schema'].'\' AND table_type=\'BASE TABLE\' AND table_name!=\'spatial_ref_sys\'';
        $result = pg_query($query);
        $res = array();

        while ($line = pg_fetch_row($result)) {
          array_push($res, $line[0]);
        }

        // Libère le résultat
        pg_free_result($result);
        // Ferme la connexion
        pg_close($dbconn);

        return $res;
    }


    function publishLayerDB( $layerName, $dataList ) {
        $payload = array('featureType' => array( 'name' => $layerName ));
        // echo(json_encode($payload)); die();
        $url = "http://localhost:8080/geoserver/rest/workspaces/".$dataList['login']."/datastores/".$dataList['store']."/featuretypes";// your address can change depending upon the configuration of the geoserver
        $ch = curl_init( $url );

        # Setup request to send json via POST.
        curl_setopt($ch, CURLOPT_POST, True);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver');
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($payload)); //supply json as payload

        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }


    function geoDelete($layerList) {
        foreach ($layerList as $layer) {
            $url = "http://localhost:8080/geoserver/rest/workspaces/".$_SESSION['login']."/datastores/".$_SESSION['login']."/featuretypes/".str_replace($_SESSION['login'].':', '', $layer)."?recurse=true";
            $ch = curl_init( $url );
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver');
            //curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/atom+xml"));
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
            curl_exec($ch);
            curl_close($ch);
        }
    }


    function deleteLayer($layerList) {
        foreach ( $layerList as $layer) {
            $sql = connectToDB();
            $query = 'DROP TABLE '.$_SESSION['login'].'.'.str_replace($_SESSION['login'].':', '', $layer).' CASCADE';
            $result = pg_query($query);
            disconnectFromDB($sql);
        }
    }

?>
