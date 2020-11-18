<?php

    $file_parts = pathinfo($_FILES["fileToUpload"]["name"]);
    $file = $file_parts['filename'];
    $file = str_replace(' ', '_', $file);
    $target_dir = "C:/xampp/htdocs/Geoportail/Uploads/".$file."/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $error = false;

    //checks to see if there was a file uploaded from the file input named 'upload'.
    if(isset($_POST["submit"])) {
        $check = filesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            //echo "File is Ok - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            //echo "File is not Ok.";
            $uploadOk = 0;
        }
    }

    if (file_exists($target_file)) {
        //echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    if ($_FILES["fileToUpload"]["size"] > 40000000) {
        //echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

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
            //echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
            $zip = new ZipArchive;
            $res = $zip->open($target_file);
            if ($res = TRUE ) {
                $zip->extractTo($target_dir);
                $zip->close();
                //echo 'extraction successful';
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

    removeDirectory("C:/xampp/htdocs/Geoportail/Uploads");

    header('Location: /' .$ROOT. '/index.php/addLayer?type=style&error='.$error);

?>
