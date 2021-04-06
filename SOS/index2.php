<?php
//ex requete= http://localhost/Geoportail/SOS/index2.php?request=GetCapabilities&version=2.0.0
  require_once 'model.php';
  require_once 'controller2.php';
  require_once 'config.php';
  $URI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Variable URI pour la redirection
  if (isset($_GET["request"]) && isset($_GET["version"]) && $_GET["version"] == "2.0.0"){
    if ($_GET["request"] == "GetCapabilities"){
      $requete = $_GET["request"];
      $version = $_GET["version"];
      if (!isset($_GET["service"])){
        $service = "SOS";
      }
      if (!isset($_GET["URLServer"])){
        $URLServer = $configURL;
      }
      if (!isset($_GET["acceptVersions"])){
        $acceptVersions = "2.0.0";
      }
      if (!isset($_GET["acceptFormats"])){
        $acceptFormats = "xml";
      }
      if (!isset($_GET["updateSequence"])){
        $updateSequence = "";
      }
      capabilities_action($URI,$URLServer,$requete,$version,$service,$acceptVersions,$acceptFormats,$updateSequence);
    }
    else if ($_GET["request"] == "GetResult"){
      $requete = $_GET["request"];
      $version = $_GET["version"];
      if (!isset($_GET["service"])){
        $service = "SOS";
      }
      if (!isset($_GET["URLServer"])){
        $URLServer = "http://localhost:8080/52n-sos-webapp/service";
      }
      if (!isset($_GET["URLServer"])){
        $URLServer = "http://localhost:8080/52n-sos-webapp/service";
      }
      if (!isset($_GET["featureOfInterest"])){
        $featureOfInterest = "";
      }
      if (!isset($_GET["offering"])){
        echo "il manque un offering";
        return "203";
      }
      if (!isset($_GET["observedProperty"])){
        echo "il manque un observedProperty";
        return "204";
      }
      if (!isset($_GET["spatialFilter"])){
        $spatialFilter = "";
      }
      if (!isset($_GET["temporalFilter"])){
        $temporalFilter = "";
      }
      if (!isset($_GET["responseFormat"])){
        $responseFormat = "";
      }
      $offering = "http://www.52north.org/test/offering/test";//$_GET["offering"];
      $observableProperty = "http://www.52north.org/test/observableProperty/test_3";//$_GET["observedProperty"];
      result_action($URI,$URLServer,$requete,$version,$service,$featureOfInterest,$offering,$observableProperty,$spatialFilter,$temporalFilter,$responseFormat);
    }
//suppretion de la partie Get Observation 
    /*else if ($_GET["request"] == "DescribeSensor"){
      $requete = $_GET["request"];
      $version = $_GET["version"];
      if (!isset($_GET["service"])){
        $service = "SOS";
      }

      if (!isset($_GET["procedure"])){
        echo "il manque la procedure";
        return "201";
      }
      if (!isset($_GET["procedureDescriptionFormat"])){
        echo "il manque le format de la description";
        return "202";
      }
      if (!isset($_GET["validTime"])){
        $validTime = "";
      }
      if (!isset($_GET["URLServer"])){
        $URLServer = "http://localhost:8080/52n-sos-webapp/service";
      }

      $procedure = "http://www.52north.org/test/procedure/8";//$_GET["procedure"];
      $procedureDescriptionFormat = "http://www.opengis.net/sensorml/2.0";//$_GET["procedureDescriptionFormat"];
      describeSensor_action($URI,$URLServer,$requete,$version,$service,$procedure,$procedureDescriptionFormat,$validTime);
    }*/
    else {
      echo "erreur 100";
    }
  }
  else {
    echo "erreur requete ou version incorrect";
  }

?>
