<?php
  //"function qui sert a renvoyer sur une autre page"
  function capabilities_action($URI,$URLServer,$requete,$version,$service,$acceptVersions,$acceptFormats,$updateSequence){
    $fichierFOI = "$URLServer?request=GetFeatureOfInterest&version=$version";
    //mettre en param le nom de domaine
    $fichierCapa = "$URLServer?request=GetCapabilities&service=$service&version=$version&acceptVersions=$acceptVersions&acceptFormats=$acceptFormats";
    if($updateSequence!=""){
      $fichierCapa = $fichierCapa.$updateSequence;
    }
    ParseCapa($fichierCapa,$URLServer);
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
    infoGraph($fichierResult);
  }
  //"function qui sert a renvoyer sur une autre page"
  /*function describeSensor_action($URI,$URLServer,$requete,$version,$service,$procedure,$procedureDescriptionFormat,$validTime){
    $fichierFOI = "$URLServer?request=GetFeatureOfInterest&version=$version";
    //mettre en param le nom de domaine
    $fichierDesSen = "$URLServer?request=DescribeSensor&service=$service&version=$version&procedure=$procedure&procedureDescriptionFormat=$procedureDescriptionFormat";
    if($validTime!=""){
      $fichierDesSen = $fichierDesSen.$validTime;
    }
    ParseCapa($fichierCapa,$URLServeur);
  }*/
?>
