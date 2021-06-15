<?php
    if(!empty($_POST['text'])){
        $data = $_POST['text'];
        $fname = "gml.gml";

        $file = fopen("./" .$fname, 'w');//creates new file
        fwrite($file, $data);
        fclose($file);
    }
?>