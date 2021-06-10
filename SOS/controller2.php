<?php
  //"function qui sert a renvoyer sur une autre page"
  function capabilities_action($URI,$URLServer,$requete,$version,$service,$acceptVersions,$acceptFormats,$updateSequence){
    $fichierFOI = "$URLServer?request=GetFeatureOfInterest&version=$version";
    //mettre en param le nom de domaine
    $fichierCapa = "$URLServer?request=GetCapabilities&service=$service&version=$version&acceptVersions=$acceptVersions&acceptFormats=$acceptFormats";
    if($updateSequence!=""){
      $fichierCapa = $fichierCapa.$updateSequence;
    }
    header("contentType:application/json");
    echo $parseCapa = ParseCapa($fichierCapa,$URLServer);
  }
  //"function qui sert a renvoyer sur une autre page"
  function result_action($URI,$URLServer,$requete,$version,$service,$featureOfInterest,$offering,$observableProperty,$spatialFilter,$temporalFilter,$responseFormat){
    $fichierFOI = "$URLServer?request=GetFeatureOfInterest&version=$version";
    //mettre en param le nom de domaine
    $fichierResult = "$URLServer?request=GetResult&service=$service&version=$version&offering=$offering&observedProperty=$observableProperty";
    if($featureOfInterest != ""){
      $fichierResult = $fichierResult.$featureOfInterest;
    }
    if($spatialFilter != ""){
      $fichierResult = $fichierResult.$spatialFilter;
    }
    if($temporalFilter != ""){
      $fichierResult = $fichierResult.$temporalFilter;
    }
    if($responseFormat != ""){
      $fichierResult = $fichierResult.$responseFormat;
    }
    header("contentType:application/json");
    echo $infoGraph = infoGraph($fichierResult);
  }
?>