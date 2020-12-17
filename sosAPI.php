<?php
    header('Content-type: application/json');
    $json = file_get_contents('./json.json');
    echo $json;
?>
