<?php
    if (isset($_GET['url'])) {
        header('Content-type: image/png');
        $image = imagecreatefrompng($_GET['url']);
        $background = imagecolorallocate($image , 255, 255, 255);
        imagecolortransparent($image, $background);
        $image = imagepng($image);
    }
?>
