<?php
//"fonction qui sert a recuperer le PATH"
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
//"fonction qui sert a recuperer le PATH entier"
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
//"fonction qui sert a rechercher des offerings dans un get capa"
  function rechercheOffering($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("Operation");
    foreach ($parameterList as $parameter) {
      $parameterName=$parameter->getAttribute("name");
      if ($parameterName=="GetObservation"){
        $valueList=$parameter->getElementsByTagName("Parameter");
        foreach ($valueList as $value) {
          $parameterName2=$value->getAttribute("name");
          if ($parameterName2=="offering"){
            $valueList2=$value->getElementsByTagName("Value");
            for ($i=0; $i < count($valueList2); $i++) {
              $valueArray[]=$valueList2[$i]->nodeValue;
            }
          }
        }
      }
    }
    return $valueArray;
  }
//"fonction qui sert a rechercher des procedure dans un get capa"
  function rechercheProcedure($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("Operation");
    foreach ($parameterList as $parameter) {
      $parameterName=$parameter->getAttribute("name");
      if ($parameterName=="DescribeSensor"){
        $valueList=$parameter->getElementsByTagName("Parameter");
        foreach ($valueList as $value) {
          $parameterName2=$value->getAttribute("name");
          if ($parameterName2=="procedure"){
            $valueList2=$value->getElementsByTagName("Value");
            for ($i=0; $i < count($valueList2); $i++) {
              $valueArray[]=$valueList2[$i]->nodeValue;
            }
          }
        }
      }
    }
    return $valueArray;
  }
//"fonction qui sert a rechercher les nom des fournisseur dans un get capa"
  function rechercheProviderName($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }

    $parameterList = $dom->getElementsByTagName("ProviderName");
    foreach ($parameterList as $parameter) {
      $liste1[] = $parameter->nodeValue;
    }

    for ($i=0; $i <count($liste1) ; $i++) {
      echo $liste1[$i];
      echo "<br>";
    }
  }
//"fonction qui sert a rechercher la date d'instalation et de desinstalation d'un capteur dans un DescribeSensor"
  function rechercheDateInstallUnstall($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("beginPosition");
    $endPosition = $dom->getElementsByTagName("endPosition");
    foreach ($parameterList as $parameter) {
      $liste1[] = $parameter->nodeValue;
    }
    foreach ($endPosition as $end) {
      $liste2[] = $end->nodeValue;
    }
    $unique1=array_unique($liste1);
    $cpt=0;
    foreach ($unique1 as $key1) {
      $var=$key1;
      echo $var;
      echo "<br>";
      $cpt++;
      if ($liste2[$cpt]==""){
        echo "encore en place";
      }
      else {
        echo $liste2[$cpt];
      }
      echo "<br>";
    }
  }
//"fonction qui sert a rechercher les observableProperty dans un DescribeSensor"
  function rechercheObservableProperty($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("OutputList");
    foreach ($parameterList as $parameter) {
      $parameterList2=$parameter->getElementsByTagName("output");
      foreach ($parameterList2 as $name) {
        $parameterName=$name->getAttribute("name");
        $liste1[] = $parameterName;
      }
    }
    $unique=array_unique($liste1);
    return $unique;
  }
//"fonction qui sert a rechercher quand on debuter les observation dans un get observation"
  function rechercheTimePosition($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("TimeInstant");
    foreach ($parameterList as $parameter) {
      $parameterName[]=$parameter->getAttribute("gml:id");
      $liste1[] = $parameter->nodeValue;
    }

    for ($i=0; $i <count($liste1) ; $i++) {
      echo $parameterName[$i].": ";
      echo $liste1[$i];
      echo "<br>";
    }
  }
//"fonction qui sert a rechercher la position d'un capteur dans un DescribeSensor"
  function recherchePosition($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("position");
    foreach ($parameterList as $parameter) {
      $liste1=$parameter->getElementsByTagName("value");
      foreach ($liste1 as $value) {
        $liste2[]=$value->nodeValue;
      }
      $liste3=$parameter->getElementsByTagName("coordinate");
      foreach ($liste3 as $name) {
        $parameterName=$name->getAttribute("name");
        if ($parameterName=="easting"){
          $parameterName2[]="longitude en Degré";
        }
        else if ($parameterName=="northing"){
          $parameterName2[]="latitude en Degré";
        }
        else if ($parameterName=="altitude"){
          $parameterName2[]="altitude en mètre";
        }
        else {
          $parameterName2[]=$parameterName=$name->getAttribute("name");
        }
      }
    }
    //$tableau[]=$parameterName2;
    //$tableau[]=$liste2;
    //var_dump($tableau);

    for ($i=0; $i <count($liste2) ; $i++) {
      $tableau[]=$parameterName2[$i].": ".$liste2[$i];
    }
    return $tableau;
  }
//"fonction qui sert a rechercher les features of interest d'un capteur dans un get capa"
  function rechercheFOI($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("Operation");
    foreach ($parameterList as $parameter) {
      $parameterName=$parameter->getAttribute("name");
      if ($parameterName=="GetObservation"){
        $valueList=$parameter->getElementsByTagName("Parameter");
        foreach ($valueList as $value) {
          $parameterName2=$value->getAttribute("name");
          if ($parameterName2=="featureOfInterest"){
            $valueList2=$value->getElementsByTagName("Value");
            for ($i=0; $i < count($valueList2); $i++) {
              $valueArray[]=$valueList2[$i]->nodeValue;
            }
          }
        }
      }
    }
    return $valueArray;
  }
//"fonction qui sert a rechercher la version et le service dans un get capa"
  function rechercheServiceVersion($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("Parameter");
    foreach ($parameterList as $parameter) {
      $parameterName=$parameter->getAttribute("name");
      if ($parameterName=="service"){
        $liste1[] = $parameter->nodeValue;
      }
      if ($parameterName=="version"){
        $valueList=$parameter->getElementsByTagName("Value");
        for ($i=0; $i < count($valueList); $i++) {
          $liste1[]=$valueList[$i]->nodeValue;
        }

      }
    }
    for ($i=0; $i <count($liste1) ; $i++) {
      echo $liste1[$i];
      echo "<br>";
    }
  }
//"fonction qui sert a rechercher les operations disponible dans un get capa"
  function rechercheComparisonOpe($fichier){
    $dom = new DOMDocument();
   $dom->validateOnParse=true;
   if (!$dom->load($fichier)) {
       die("Impossible de charger le fichier XML");
   }
   $parameterList = $dom->getElementsByTagName("ComparisonOperator");
   foreach ($parameterList as $parameter) {
     $parameterName[] = $parameter->getAttribute("name")."<br>";
       $liste1[] = $parameter->nodeValue;
   }
   for ($i=0; $i <count($liste1) ; $i++) {
     echo $liste1[$i];
     echo $parameterName[$i];
   }
  }
//"fonction qui sert a rechercher des capteurs"
  function rechercheDuCapteur($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("identifier");
    foreach ($parameterList as $parameter) {
      $parameterName=$parameter->getAttribute("codeSpace");
      if ($parameterName=="uniqueID"){
        $liste2[]=$parameter->nodeValue;
      }
    }
    for ($i=0; $i <count($liste2)/5 ; $i++) {
      echo $liste2[$i];
      echo "<br>";
    }
  }
//"fonction qui sert a rechercher les offerings dans un DescribeSensor"
  function rechercheOfferingDS($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("capabilities");
    foreach ($parameterList as $parameter) {
      $parameterName=$parameter->getAttribute("name");
      if ($parameterName=="offerings"){
        $valueList2=$parameter->getElementsByTagName("value");
        for ($i=0; $i < count($valueList2); $i++) {
          $valueArray[]=$valueList2[$i]->nodeValue;
        }
        break;
      }
    }
    return $valueArray;
  }
//"fonction qui sert a rechercher les resultat dans un get result"
  function rechercheResult($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("resultValues");
    foreach ($parameterList as $value) {
      $valueList=$value->nodeValue;
    }
    if ($parameterList->length==0){
      $valueList="non defini";
    }
    $valueBR = str_replace("#","<br>",$valueList);
    $valueT = str_replace("T"," ",$valueBR);
    $valueVirgule = str_replace(","," ",$valueT);
    $valueZ = str_replace("Z"," ",$valueVirgule);
    return $valueZ;
  }
//"fonction qui sert a parser un csv"
  function ParseCsv($fichierCsv){
    $fichierCsv="upload/".$fichierCsv;
    $row = 1;
    $list=[];
    if (($handle = fopen($fichierCsv, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $num = count($data);
            //echo "<p> $num champs à la ligne $row: <br /></p>\n";
            $row++;
            /*for ($c=0; $c < $num; $c++) {
                echo $data[$c] . "<br />\n";
            }*/
            $final=str_replace(" ","T",$data[0]);
            $final=$final.",".$data[1];
            $final="#".$final;
            $list[]=$final;
            $chaineFin=implode($list);
        }
        fclose($handle);
    }
    return $chaineFin;
  }
//"fonction qui retourne les dates du GetResult"
  function dateGraph($fichierResult){
    $replace = rechercheResult($fichierResult);
    $replace = str_replace("<br>"," ",$replace);
    $replace = explode(" ",$replace);
    $intval = intval($replace[0]);
    $temp = 1;
    $replace1=[];
    for ($i=0;$i<$intval*2;$i++) {
      //$tiretToVirgule = str_replace("-",",",$replace[$temp]);
      $replace1[] = $replace[$temp];
      $temp = $temp +2;
      //echo "<br>";
    }

    return $replace1;
  }
//"fonction qui retourne les heures du GetResult"
  function heureGraph($fichierResult){
    $replace = rechercheResult($fichierResult);
    $replace = str_replace("<br>"," ",$replace);
    $replace = explode(" ",$replace);
    $intval = intval($replace[0]);
    $temp1 = 2;
    $replace1=[];
    for ($i=0;$i<$intval;$i++){
      $replace1[] = $replace[$temp1];
      $temp1 = $temp1 +4;
      //echo $replace1;
      //echo "<br>";
    }
    return $replace1;
  }
//"fonction qui retourne les resultat du GetResult"
  function resultatGraph($fichierResult){
    $replace = rechercheResult($fichierResult);
    $replace = str_replace("<br>"," ",$replace);
    $replace = explode(" ",$replace);
    $intval = intval($replace[0]);
    $temp2 = 4;
    $replace1=[];
    for ($i=0;$i<$intval;$i++){
      $cpt = floatval($replace[$temp2]);
      $replace1[] = $cpt;
      $temp2 = $temp2 +4;
      //echo $replace1;
      //echo "<br>";
    }
    return $replace1;
  }
//"fonction qui retourne toutes les infos du GetResult"
  function infoGraph($fichierResult){
    $replace = rechercheResult($fichierResult);
    //echo $replace;
    //$replace = str_replace("<br>"," ",$replace);
    //$replace = explode(" ",$replace);
    $replace = explode("<br>",$replace);
    $intval = intval($replace[0]);
    $temp1 = 2;
    $tabDate=[];
    $tabHeure=[];
    $tabValeur=[];
    for ($i=1;$i<=$intval;$i++){
      $replaceTemp = explode(" ",$replace[$i]);
      $tabDate[$i-1] = $replaceTemp[0];
      $tabHeure[$i-1] = $replaceTemp[1];
      $tabValeur[$i-1] = $replaceTemp[3];
    }
    $arr = array("Date"=>$tabDate,"Heure"=>$tabHeure,"Valeur"=>$tabValeur);
    echo json_encode($arr);
  }
//"fonction qui sert a rechercher les id des features dans un fichier get FOI"
  function IdFOI($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("identifier");
    foreach ($parameterList as $value) {
      $valueList[]=$value->nodeValue;
    }
    return $valueList;
  }
//"fonction qui sert a rechercher les nom des feature dans un fichier get FOI"
  function NameFOI($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $parameterList = $dom->getElementsByTagName("name");
    foreach ($parameterList as $value) {
      $valueList[]=$value->nodeValue;
    }
    return $valueList;
  }
//"fonction qui sert a rechercher les shape (forme) des feature dans un fichier get FOI"
  function IdShapeFOI($fichier){
  $dom = new DOMDocument();
  $dom->validateOnParse=true;
  if (!$dom->load($fichier)) {
      die("Impossible de charger le fichier XML");
  }
  //var_dump( $dom->saveXML());

  $parameterList = $dom->getElementsByTagName("shape");
  foreach ($parameterList as $parameter) {
    $shapeGml = $parameter->ownerDocument->saveXML($parameter);
    echo $shapeGml;
    }
  }
//"fonction qui retourne un dico de FOI"
  function ParseFOI($fichier){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $tabId=[];
    $tabShape=[];
    $tabName=[];
    $parameterList = $dom->getElementsByTagName("featureMember");
    foreach ($parameterList as $parameter) {
      $parameterList2 = $parameter->getElementsByTagName("shape");
      foreach ($parameterList2 as $parameter2) {
        $shapeGml = $parameter2->ownerDocument->saveXML($parameter2);
        //echo($shapeGml);
        $tabShape[] = $shapeGml;
      }

      $parameterList3 = $parameter->getElementsByTagName("name");
      foreach ($parameterList3 as $value) {
        $tabName[] = $value->nodeValue;
      }
      $parameterList4 = $parameter->getElementsByTagName("identifier");
      foreach ($parameterList4 as $value) {
        $tabId[]=$value->nodeValue;
      }
    }

    $arr = array("id"=>$tabId,"name"=>$tabName,"shape"=>$tabShape);
    echo json_encode($arr);

  }
//"fonction qui retourne un dico de capa"
  function ParseCapa($fichier,$URLServeur){
    $dom = new DOMDocument();
    $dom->validateOnParse=true;
    if (!$dom->load($fichier)) {
        die("Impossible de charger le fichier XML");
    }
    $tabOffering = [];
    $tabProcedure = [];
    $tabOP = [];
    $arr = [];
    $procedureDescriptionFormat = [];
    $listepro = [];
    $listeBeginPosition = [];
    $listeEndPosition = [];

    $parameterList = $dom->getElementsByTagName("ObservationOffering");
    foreach ($parameterList as $parameter) {
      $tempId = $parameter->getElementsByTagName("identifier");
      foreach ($tempId as $value) {
        $id = $value->nodeValue;
      }
      $tempPDF = $parameter->getElementsByTagName("procedureDescriptionFormat");
      foreach ($tempPDF as $value) {
        $tabPDF[] = $value->nodeValue;
      }

      $tempName = $parameter->getElementsByTagName("name");
      foreach ($tempName as $value) {
        $name = $value->nodeValue;
      }
      $temp = $parameter->getElementsByTagName("procedure");
      foreach ($temp as $value) {
        $tabProcedure1 [] = $value->nodeValue;
        if (in_array("http://www.opengis.net/sensorml/2.0",$tabPDF)){
          $fichierDesSen = "$URLServeur?request=DescribeSensor&service=SOS&version=2.0.0&procedure=$value->nodeValue&procedureDescriptionFormat=http://www.opengis.net/sensorml/2.0";
          if (in_array($fichierDesSen,$listepro)){
            for($i=0;$i<count($tabProcedure[$value->nodeValue]);$i++){
              $valeur =$tabProcedure[$value->nodeValue];
              if ($valeur == $value->nodeValue){
                $tabProcedure[$value->nodeValue] = $valeur;
              }
            }
          }
          else{
            $listepro[$fichierDesSen] = $fichierDesSen;
            $dom1 = new DOMDocument();
            $dom1->validateOnParse=true;
            if (!$dom1->load($fichierDesSen)) {
                die("Impossible de charger le fichier XML");
            }
              $parameterBegin = $dom1->getElementsByTagName("beginPosition");
              $endPosition = $dom1->getElementsByTagName("endPosition");
              foreach ($parameterBegin as $begin) {
                $listeBegin[] = $begin->nodeValue;
              }
              foreach ($endPosition as $end) {
                $listeEndPosition[] = $end->nodeValue;
              }
              $listeBeginPosition=array_unique($listeBegin);
              $cpt=0;
              foreach ($listeBeginPosition as $key1) {
                $cpt++;
                if ($listeEndPosition[$cpt]==""){
                  $listeEndPosition[$cpt] = "encore en place";
                }
                else {
                  $listeEndPosition[$cpt];
                }
              }
              $parameterPosition = $dom1->getElementsByTagName("position");
              foreach ($parameterPosition as $parameter) {
                $liste1=$parameter->getElementsByTagName("value");
                foreach ($liste1 as $value) {
                  $liste2[]=$value->nodeValue;
                }
                $liste3=$parameter->getElementsByTagName("coordinate");
                foreach ($liste3 as $name) {
                  $parameterName=$name->getAttribute("name");
                    $parameterName2[]=$parameterName=$name->getAttribute("name");
                  }
                }
              }
              for ($i=0; $i <count($liste2) ; $i++) {
                $tableau[]=$parameterName2[$i].": ".$liste2[$i];
              }
              $parameterOP = $dom1->getElementsByTagName("OutputList");
              foreach ($parameterOP as $OP) {
                $parameterOP2=$OP->getElementsByTagName("output");
                foreach ($parameterOP2 as $OP2) {
                  $parameterOP2=$OP2->getAttribute("name");
                  $listeOP[$parameterOP2] = $parameterOP2;
                }
              }
              $uniqueOP=array_unique($listeOP);
              $fichierFOI = "$URLServeur?request=GetFeatureOfInterest&service=SOS&version=2.0.0";
              $FOI = ParseFOI($fichierFOI);
              $arrayProcedure = array("listeOP"=>$uniqueOP,"listeBeginPosition"=>$listeBeginPosition,"listeEndPosition"=>$listeEndPosition,"position"=>$tableau,"FOI"=>$FOI);
              foreach ($temp as $value) {
              $tabProcedure[$value->nodeValue] = $arrayProcedure;
            }
        }
      }
      $temp1 = $parameter->getElementsByTagName("observableProperty");
      foreach ($temp1 as $value) {
        $tabOP []= $value->nodeValue;
      }
      $tabOffering["id"] = $id;
      $tabOffering["name"] = $name;
      $tabOffering["procedure"] = $tabProcedure;
      $tabOffering["observableProperty"] = $tabOP;

      $arr[$id] = $tabOffering;
    }
    echo json_encode($arr);
  }

?>
