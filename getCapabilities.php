<?php
    if( isset($_REQUEST['request']) && isset($_REQUEST['url'])) {
        if ( $_REQUEST['request'] == 'capabilities') {
            $file = file_get_contents($_REQUEST['url']);
            echo $file;
        }
    }
?>
