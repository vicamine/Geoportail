<?php
  require_once 'model.php';
  require_once 'controller2.php';
  include 'config.php';
  $URI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Variable URI pour la redirection
  if (isset($_GET["request"]) && isset($_GET["version"]) && $_GET["version"] == "2.0.0"){
    if ($_GET["request"] == "GetCapabilities"){
      $requete = $_GET["request"];
      $version = $_GET["version"];
      if (!isset($_GET["service"])){
        $service = "SOS";
      }
      if (!isset($_GET["URLServer"])){
        $URLServer = $URLServerConfig;
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
        $URLServer = $URLServerConfig;
      }
      if (!isset($_GET["featureOfInterest"])){
        echo "il manque une feature";
        return "204";
      }
      else{$featureOfInterest = $_GET["featureOfInterest"];}
      if (!isset($_GET["offering"])){
        $offering = "";
      }
      if (!isset($_GET["observedProperty"])){
        echo "il manque un observedProperty";
        return "204";
      }
      else{$observableProperty = $_GET["observedProperty"];}
      if (!isset($_GET["spatialFilter"])){
        $spatialFilter = "";
      }
      if (!isset($_GET["temporalFilter"])){
        $temporalFilter = "";
      }
      if (!isset($_GET["responseFormat"])){
        $responseFormat = "";
      }
      result_action($URI,$URLServer,$requete,$version,$service,$featureOfInterest,$offering,$observableProperty,$spatialFilter,$temporalFilter,$responseFormat);
    }
    else {
      echo "erreur 100";
    }
  }
  else {
    echo "erreur requete ou version incorrect";
  }

?>