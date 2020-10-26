<?php

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
?>
