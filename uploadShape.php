<?php

    $file_parts = pathinfo($_FILES["fileToUpload"]["name"]);
    $file = $file_parts['filename'];
    $file = str_replace(' ', '_', $file);
    $target_dir = "C:/xampp/htdocs/Geoportail/Uploads/".$file."/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $error = 0;
    $password = "dabrion";

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
        }
        else {
            //echo "Sorry, there was an error uploading your file.";
        }
    }

    // converting shapefile into table and add them to DB.
    putenv("PGPASSWORD=".$password);
    $shpName = glob($target_dir."*.shp");
    $shpNameMaj = glob($target_dir."*.SHP");
    $all = array_merge($shpName, $shpNameMaj);
    if (count($all) > 1) {
        $error = 2;
    } else if ( count($all) == 0 ) {
        $error = 3;
    }
    else {
        $layer = '';
        foreach ($all as $value) {
            $tblname = strtolower(pathinfo($value)['filename']);
            $tblname = str_replace(' ', '_',$tblname);
            $tblname = str_replace('-', '_',$tblname);
            $tblname = str_replace('.', '_',$tblname);
            $tblname = str_replace('%20', '_',$tblname);
            $tblname = $_SESSION['login'].'.'.$tblname;
            $file = basename( $value );
            if (strpos($_POST['Projection'], ':') == false) {
                $projection = $_POST['Projection'];
            }
            else {
                $projection = substr($_POST['Projection'], strpos($_POST['Projection'], ':'));
            }
            $queries = "shp2pgsql -I -s ".$projection." -c ". $target_dir . $file ." ". $tblname ." | psql -h localhost -p 5432 -U postgres -d Geoportail";
            $output = shell_exec($queries);
            if (stripos($output, 'rollback') == NULL){
                $error = 0;
                if (isset($_POST['Title'])){
                    $title = $_POST['Title'];
                }
                else {
                    $title = null;
                }
                if (isset($_POST['Abstract'])) {
                    $abstract = $_POST['Abstract'];
                }
                else {
                    $abstract = null;
                }
                $tblname = substr($tblname, strpos($tblname, '.')+1);
                $res = publishLayerDB($tblname, $title, $abstract);
                $layer = $tblname;
            } else {
                $error = 1;
            }
            if ( $error == 0 ) {
                addPrivacy($tblname);
                $sldName = glob($target_dir."*.sld");
                $sldNameMaj = glob($target_dir."*.SLD");
                $allSld = array_merge($sldName, $sldNameMaj);
                foreach ($allSld as $stl) {
                    $res = publishStyle($stl);
                    if ( $res ) {
                        $style = pathinfo($stl)['filename'];
                        $add = addStyleToLayer($layer, $style );
                        if ($add != $style) {
                            $error == 4;
                        }
                    }
                    else {
                        $error = 4;
                    }
                }
            }
        }
    }

    putenv('PGPASSWORD');
    removeDirectory("C:/xampp/htdocs/Geoportail/Uploads");
    header('Location:/'.$ROOT.'/index.php/addLayer?type=shapefile&error='.$error);

?>
