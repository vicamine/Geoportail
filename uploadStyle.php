<?php

    $file_parts = pathinfo($_FILES["fileToUpload"]["name"]);
    $file = $file_parts['filename'];
    $file = str_replace(' ', '_', $file);
    $target_dir = "C:/xampp/htdocs/Geoportail/Uploads/".$file."/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $error = false;

    //checks to see if there was a file uploaded.
    if(isset($_POST["submit"])) {
        $check = filesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1; // files is ok for upload
        } else {
            $uploadOk = 0; // files is not ok for upload
        }
    }

    // checks if file was already uploaded.
    if (file_exists($target_file)) {
        $uploadOk = 0;
    }

    // checks the file size.
    if ($_FILES["fileToUpload"]["size"] > 40000000) {
        $uploadOk = 0;
    }

    // checks the file type.
    if($fileType != "zip") {
        //echo "Sorry, only ZIP files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        //echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (!is_dir($target_dir)) {
            mkdir($target_dir);
        }
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $zip = new ZipArchive;
            $res = $zip->open($target_file);
            if ($res = TRUE ) {
                $zip->extractTo($target_dir);
                $zip->close();
            } else {
                //echo 'extraction error';
            }
            $error = false;
            $styleName = glob($target_dir."*.SLD");
            $styleNameMaj = glob($target_dir."*.sld");
            $all = array_merge($styleName, $styleNameMaj);
            foreach ($all as $value) {
                $res = publishStyle($value);
                if ( !$res ) {
                    $error = true;
                }
            }
        }
        else {
            //echo "Sorry, there was an error uploading your file.";
        }
    }

    // clean Uploads directory
    removeDirectory("C:/xampp/htdocs/Geoportail/Uploads");

    header('Location: /' .$ROOT. '/index.php/addLayer?type=style&error='.$error);

?>
