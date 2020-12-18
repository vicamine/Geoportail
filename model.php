<?php

    if ( isset($_POST["action"]) ) {
        if ( $_POST["action"] == "addStyleToLayer" ) {
            $res = addStyleToLayer( $_POST["layer"], $_POST["style"] );
            if ( strtolower($res) == strtolower($_POST["style"])) {
                echo true;
            }
            else {
                echo false;
            }
        }
        else if ( $_POST["action"] == "delStyleToLayer" ) {
            $res = delStyleToLayer( $_POST["layer"], $_POST["style"] );
            if ( $res ) {
                echo true;
            }
            else {
                echo false;
            }
        }
        else if ( $_POST['action'] == 'setPrivacy' ) {
            setPrivacy($_POST['layer'], $_POST['public']);
        }
        else if ( $_POST['action'] == 'getPrivacy' ) {
            $res = getPrivacy( $_POST['layer'] );
            echo $res[0]['public'].$_POST['layer'];
        }
    }


    /**
     * Permet de créer une connexion vers la base de données
     * @return mysqli
     */
    function connectToDB(){
        include('database.php');
        $sql = pg_connect('host='.$host.' port='.$port.' dbname='.$dbname.' user='.$user.' password='.$password );
        return $sql;
    }


    /**
     * Permet de se déconnecter de la base de données
     * @param $sql
     */
    function disconnectFromDB($sql){
        pg_close($sql);
    }


    /**
     * Permet de faire des requetes Select preparer
     * @param string $requete
     * @param mixed $params
     * @return mixed $row
     */
    function doPreparedSelect($requete, $params){
        $sql=connectToDB();
        $result=pg_prepare($sql, "prepared_query", $requete);
        $result = pg_execute($sql, "prepared_query", $params);
        $row = pg_fetch_all($result);
        pg_free_result($result);
        disconnectFromDB($sql);
        return $row;
    }


    /**
     * Permet de faire des requetes preparer qui ne retourne rien
     * @param string $requete
     * @param mixed $params
     */
    function doPreparedRequest($requete, $params){
        $sql=connectToDB();
        $result=pg_prepare($sql, "prepared_query", $requete);
        $result = pg_execute($sql, "prepared_query", $params);
        disconnectFromDB($sql);
    }


    /**
     * Permet à un utilisateur de se connecter
     * @param string $login
     * @param string $password
     * @return mixed $rows
     */
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


    /**
     * Permet d'inserer un utilisateur dans la BDD
     * @param string $nom
     * @param string $prenom
     * @param string $login
     * @param string $password
     * @return bool
     */
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


    /**
     * Permet de vérifier si un login existe déja
     * @param string $login
     * @return bool
     */
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


    /**
     * Permet de savoir dans quel fichier on se trouve
     * @param string $URI
     * @return string $PATH
     */
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


    /**
     * Permet de savoir dans quel fichier on se trouve
     * @param string $URI
     * @return string $COMPLETE_PATH
     */
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


    /**
     * Permet de générer dynamiquement les formulaires d'ajouts via une préselection
     * @param string $type
     * @param string $URI
     * @return string $form
     */
    function getForm($type, $URI) {
        if ($type =='shapefile') {
            $form = '<form action="uploadShape" method="post" enctype="multipart/form-data">
                <br>
                <label for="Projection"> Projection EPSG (nombre): </label>
                <input type="text" id="Projection" name="Projection" required>
                <br>
                <label for="Title"> Title : </label>
                <input type="text" id="Title" name="Title">
                <br>
                <label for="Abstract"> Abstract : </label>
                <input type="text" id="Abstract" name="Abstract">
                <br>
            	<label for="fileToUpload"> Select zip to upload: </label>
            	<input type="file" name="fileToUpload" id="fileToUpload">
            	<!-- <input type="file" name="files[]" id="files[]"> -->
                <br><br>
                <input type="submit" value="Confirmer" name="submit" >
            </form>';
        }
        elseif ($type == 'style') {
            $form = '<form action="uploadStyle" method="post" enctype="multipart/form-data">
                <br>
                <label for="fileToUpload"> Select zip to upload: </label>
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


    /**
     * Permet de créer un workspace sur le geoserver
     * @param string $login
     * @return string $result
     */
    function create_workspace($login) {
        $url = "http://localhost:8080/geoserver/rest/workspaces"; // geoserver address
        $ch = curl_init( $url );

        // Setup request to send json via POST.
        $payload = json_encode( array( "workspace"=> array('name' => $login) ) ); //supply json as payload
        curl_setopt($ch, CURLOPT_POST, True);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver'); //credentials (user:password)
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }


    /**
     * Permet de créer un store à partir d'une base de donnée sur le geoserver
     * @param string $login
     * @param array $dataList
     * @return bool
     */
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
        $url = "http://localhost:8080/geoserver/rest/workspaces/".$login."/datastores";
        $ch = curl_init( $url );

        curl_setopt($ch, CURLOPT_POST, True);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver');
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($payload)); //supply json as payload
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result == $dataList['store']) {
            return true;
        }
        return false;
    }


    /**
     * Permet de récupérer toutes les layers dans la bdd postgis sous forme de table
     * @param array $dataList
     * @return array $res
     */
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


    /**
     * Permet de publier une layer présente dans une base de données
     * @param string $layerName
     * @param array $dataList
     * @return string $result
     */
    function publishLayerDB( $layerName, $title, $abstract ) {
        $payload = '<featureType><name>'.$layerName.'</name>';

        if ($title != null && $title != '') {
            $payload .= '<title>'.$title.'</title>';
        }
        if ($abstract != null && $abstract != '') {
            $payload .= '<abstract>'.$abstract.'</abstract>';
        }

        $payload .= '</featureType>';

        $url = "http://localhost:8080/geoserver/rest/workspaces/".$_SESSION['login']."/datastores/".$_SESSION['login']."/featuretypes";
        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_POST, True);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver');
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:text/xml'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }


    /**
     * Permet de supprimer une ou plusieurs layers de geoserver
     * @param array $layerList
     */
    function geoDelete($layerList) {
        foreach ($layerList as $layer) {
            $url = "http://localhost:8080/geoserver/rest/workspaces/".$_SESSION['login']."/datastores/".$_SESSION['login']."/featuretypes/".str_replace($_SESSION['login'].':', '', $layer)."?recurse=true";
            $ch = curl_init( $url );
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver');
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
            curl_exec($ch);
            curl_close($ch);
        }
    }


    /**
     * Permet de supprimer une ou plusieurs layers de la base de données
     * @param array $layerList
     */
    function deleteLayer($layerList) {
        foreach ( $layerList as $layer) {
            $sql = connectToDB();
            $query = 'DROP TABLE '.$_SESSION['login'].'.'.str_replace($_SESSION['login'].':', '', $layer).' CASCADE';
            $result = pg_query($query);
            disconnectFromDB($sql);
        }
    }


    /**
     * Permet de publier un style sur geoserver à partir du chemin d'un fichier .SLD
     * @param string $filePath
     * @return bool
     */
    function publishStyle($filePath){
        $fileName = explode('/', $filePath)[6];
        $styleName = explode('.', $fileName)[0];
        $url = "http://localhost:8080/geoserver/rest/workspaces/".$_SESSION['login']."/styles";
        $ch = curl_init( $url );
        $POST_DATA = "<style><name>".$styleName."</name><filename>".$fileName."</filename></style>";
        curl_setopt($ch, CURLOPT_POST, True);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml"));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $POST_DATA);
        $res = curl_exec($ch);
        curl_close($ch);

        $POST_DATA = fopen($filePath, 'rb');
        $url = "http://localhost:8080/geoserver/rest/workspaces/".$_SESSION['login']."/styles/".$styleName;
        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/vnd.ogc.sld+xml"));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_INFILE, $POST_DATA);
        $res = curl_exec($ch);
        curl_close($ch);

        if ( $res == '' ) {
            return true;
        }
        return false;
    }


    /**
     * Permet de supprimer récursivement tous les fichiers et sous-dossier d'un dossier dont le chemin est passé en paramètre
     * @param string $path
     */
    function removeDirectory($path) {
    	$files = glob($path . '/*');
    	foreach ($files as $file) {
    		is_dir($file) ? removeDirectory($file) : unlink($file);
    	}
        if ($path != "C:/xampp/htdocs/Geoportail/Uploads"){
            rmdir($path);
        }
    }


    /**
     * Permet de supprimer un ou plusieurs style publié sur geoserver
     * @param array $styleList
     */
    function deleteStyle($styleList) {
        foreach ($styleList as $style) {
            $url = "http://localhost:8080/geoserver/rest/workspaces/".$_SESSION['login']."/styles/".$style."?purge=true&recurse=true";
            $ch = curl_init( $url );
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver');
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
            curl_exec($ch);
            curl_close($ch);
        }
    }


    /**
     * Permet d'affecter un style à une layer geoserver
     * @param string $layer
     * @param string $style
     * @return string $res
     */
    function addStyleToLayer( $layer, $style ) {
        $url = "http://localhost:8080/geoserver/rest/layers/".$layer."/styles";
        $ch = curl_init( $url );
        $POST_DATA = "<style><name>".$style."</name></style>";
        curl_setopt($ch, CURLOPT_POST, True);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml"));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $POST_DATA);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }


    /**
     * Permet de supprimer un style affecté à une layer
     * @param string $layer
     * @param string $style
     * @return bool $ok
     */
    function delStyleToLayer( $layer, $style ) {
        $url = "http://localhost:8080/geoserver/rest/layers/".$layer."/styles.json";
        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res, true);
        $styles = array();
        foreach ($res["styles"]["style"] as $elem) {
            if ( $elem['name'] != $style ) {
                array_push( $styles, $elem['name'] );
            }
        }

        $url = "http://localhost:8080/geoserver/rest/layers/".$layer;
        $POST_DATA = "<layer><styles></styles></layer>";
        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:geoserver');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml"));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $POST_DATA);
        $res = curl_exec($ch);
        curl_close($ch);

        $ok = true;
        foreach ($styles as $toAdd) {
            $check = addStyleToLayer( $layer, $toAdd );
            if ($check != $toAdd) {
                $ok = false;
            }
        }

        return $ok;
    }


    /**
     * Permet d'ajouter une layer dans la table privacy pour y gérer ses droits.
     * @param array $layerList
     */
    function addPrivacy( $layername ) {
        $query = 'INSERT INTO admin.privacy (layername, userid, public) VALUES ( $1, $2, $3)';
        $params = [ $_SESSION['login'].'.'.$layername, $_SESSION['id'], 'false' ];
        doPreparedRequest( $query, $params );
        return true;
    }


    /**
     * Permet de supprimer une layer de la table privacy.
     * @param array $layerList
     */
    function delPrivacy( $layerList ) {
        foreach ( $layerList as $layer) {
            $query = 'DELETE FROM admin.privacy WHERE layername = $1';
            $params = [ str_replace(':', '.', $layer) ];
            doPreparedRequest( $query, $params );
        }
    }


    /**
     * Permet de modifier les droits d'une layer dans la table privacy de la bdd postGis
     * @param string $layerName
     * @param bool $public
     */
    function setPrivacy( $layername, $public ) {
        $query = 'UPDATE admin.privacy SET public=$1 WHERE layername=$2';
        $params = [ $public, str_replace('privacy', '', str_replace(':', '.', $layername) ) ];
        doPreparedRequest( $query, $params );
        return true;
    }


    /**
     * Permet de récupérer les droits d'une layer
     *  @param string $layername
     */
    function getPrivacy( $layername ) {
        $query = 'SELECT public FROM admin.privacy WHERE layername=$1';
        $params = [ str_replace('privacy', '', str_replace(':', '.', $layername) ) ];
        $res = doPreparedSelect( $query, $params );
        return $res;
    }


    /**
     * Permet de récupérer la liste de toutes les layers privées
     */
    function getAllPrivate() {
        $query = 'SELECT layername FROM admin.privacy where public=$1';
        $params = [ 'false' ];
        $res = doPreparedSelect( $query, $params );
        return $res;
    }

?>
