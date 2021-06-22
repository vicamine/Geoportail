
var map;
var view;
var viewNC;
var layers = [];


function initMap() {
    view = new ol.View({
        zoom: 7,
        center: [ 165.568866, -21.425749 ],
        projection: 'EPSG:4326'
    });

    viewNC = new ol.View({
        zoom: 7,
        center: [ 323399.78, 395030.91 ],
        extent: [ -575324.67, -279306.32, 1296651.41, 1016072.78 ],
        projection: new ol.proj.Projection({
            code: 'EPSG:3163',
            units: 'm'
        })
    });

    map = new ol.Map({
        target: 'map',
        view: view
    });


    // Fond de carte
    map.addLayer(new ol.layer.Tile({
        source: new ol.source.OSM(),
        name: 'fond_de_carte_osm',
        visible: true
    }));
    
    
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.open("GET", "../SOS/gml.gml", true);
    xmlhttp.onload = function () {
        var format = new ol.format.GML3();

        var xmlDoc = xmlhttp.responseXML;

        // Read and parse all features in XML document
        var features = format.readFeatures(xmlDoc, {
            featureProjection: 'EPSG:4326',
            dataProjection: 'EPSG:4326'
        });
        
        var vector = new ol.layer.Vector({
            source: new ol.source.Vector({
                format: format,
                style: new ol.style.Style({
                    fill: new ol.style.Fill({
                      color: 'rgba(255, 255, 255, 0.5)'
                    }),
                    stroke: new ol.style.Stroke({
                      color: 'rgb(255,255,0)',
                      width: 2
                    }),
                    image: new ol.style.Circle({
                      radius: 7,
                      fill: new ol.style.Fill({
                        color: 'rgb(0,0,255)'
                      })
                    })
                })
            }),
        });

        // Add features to the layer's source
        vector.getSource().addFeatures(features);

        //map.addLayer(vector);
    };
    xmlhttp.send();
    
    /*var procedure = new ol.layer.Vector({
        source: new ol.source.Vector({
            url: '../SOS/gml.gml',
            format: new ol.format.GML3({
                srsName: 'EPSG:4326',
                featureNS: 'map',
                featureType: 'wfs_geom',
            }),
            name: 'testGML',
            type: 'GML',
            style: new ol.style.Style({
                fill: new ol.style.Fill({
                  color: 'rgba(255, 255, 255, 0.5)'
                }),
                stroke: new ol.style.Stroke({
                  color: 'rgb(255,255,0)',
                  width: 2
                }),
                image: new ol.style.Circle({
                  radius: 7,
                  fill: new ol.style.Fill({
                    color: 'rgb(0,0,255)'
                  })
                })
            })  
        }),
        opacity: 0.5,
    });
    map.addLayer(procedure);
    layers.push(procedure);*/
}


function addFOI( offering ) {
    $.ajax({
        url: '../sosAPI.php',
        type: 'GET',
        data: {
            request: 'result',
        },
        dataType: 'json',
        success: function(res) {
            var valeur = [];
            res.Valeur.forEach(element => valeur.push(parseInt(element)));
            const chart = Highcharts.chart('container', {
                chart: {
                    type: 'line'
                },
                title: {
                    text: 'resultat'
                },
                xAxis: {
                    categories: res.Date
                },
                yAxis: {
                    title: {
                        text: 'mesure'
                    }
                },
                series: [{
                    name: 'Test',
                    data: valeur
                }, {
                    name: 'Test2',
                    data: valeur
                }]
            });
        }
    });
}


function createGML(procedure, foi){
    var featureMemberTemplate = "<gml:featureMember><ogr:capteurs fid=\"capteur.1\"><ogr:geometryProperty></ogr:geometryProperty><ogr:id>1</ogr:id></ogr:capteurs></gml:featureMember>";
    var json = null;
    $.ajax({
        'async': false,
        'url': "capabilities.json",
        'dataType': "json",
        'success': function(data) {
            json = data;
        }
    });
    for (var elem in json){
        var key = elem;
    }
    for (var elem in json[key]["procedure"]){
        var key2 = elem;
    }
    var shape = json[key]["procedure"][key2]["FOI"]["shape"];
    shape = JSON.stringify(shape);
    shape = shape.replaceAll("[", "");
    shape = shape.replaceAll("]", "");
    shape = shape.replaceAll('\\"', "'");
    shape = shape.replaceAll('"', "");
    shape = shape.replaceAll('\\n', " ");
    shape = shape.replaceAll('<sams:shape>', "");
    shape = shape.replaceAll('</sams:shape>', "");
    var points = shape.split(",");
    var featureMembers = Array();
    var index = featureMemberTemplate.indexOf("<ogr:geometryProperty>") + "<ogr:geometryProperty>".length;
    for (var i=0; i<points.length; i++) {
        var featureMember = featureMemberTemplate;
        featureMember = featureMember.replaceAll('capteur.1', 'capteur.'+(i+1));
        featureMember = featureMember.replaceAll('<ogr:id>1</ogr:id>', '<ogr:id>'+(i+1)+'</ogr:id>');
        feature = [featureMember.slice(0, index), points[i], featureMember.slice(index)].join('');
        featureMembers.push(feature);
    }
    featureMembers = featureMembers.join('');
    var template;
    $.ajax({
        'async': false,
        'url': '../SOS/gmlTemplate.gml',
        'dataType': "text",
        'success': function(data) {
            template = data;
        }
    });
    var templateIndex = template.indexOf('</gml:boundedBy>') + '</gml:boundedBy>'.length;
    var gmlFile = [template.slice(0, templateIndex), featureMembers, template.slice(templateIndex)].join('');
    $.ajax({
        'url': '../SOS/createFile.php',
        'dataType': "text",
        'type': 'POST',
        'data': {
            text: gmlFile
        }
    });
}

var observablePropertyList = [];
var foiList = [];
var procedureList = [];

function initSOS() {
    
    var jsonFile = "../sosAPI.php";
    //var jsonFile = "../SOS/capabilities.json";
    var json = null;
    $.ajax({
        'async': false,
        'url': jsonFile,
        'type': 'GET',
        'data': {
            request:'capabilities'
        },
        'dataType': "json",
        'success': function(data) {
            json = data;
        }
    });
    if (json != null) {
        Object.keys(json).forEach(function(offering) {
            var select = document.getElementById("offering");
            var option = document.createElement("option");
            option.setAttribute("value", offering);
            if (typeof json[offering]["name"] == "string") {
                option.innerHTML = json[offering]["name"];
            }
            else {
                option.innerHTML = offering;
            }
            select.append(option);
            Object.keys(json[offering]["observableProperty"]).forEach(function(observableProperty) {
                if (!observablePropertyList.includes(json[offering]["observableProperty"][observableProperty])) {
                    var select = document.getElementById("observableProperty");
                    var option = document.createElement("option");
                    option.setAttribute("value", json[offering]["observableProperty"][observableProperty]);
                    option.setAttribute("id", json[offering]["observableProperty"][observableProperty]);
                    option.setAttribute("class", offering + " _observableProperty");
                    option.setAttribute("offering", offering);
                    var observableName = json[offering]["observableProperty"][observableProperty].split("/");
                    observableName = observableName[observableName.length - 1];
                    option.innerHTML = observableName;
                    select.append(option);
                    observablePropertyList.push(json[offering]["observableProperty"][observableProperty]);
                }
                else {
                    var option = document.getElementById(json[offering]["observableProperty"][observableProperty]);
                    if (!option.className.includes(offering)) {
                        option.setAttribute("class", option.className + " " + offering);
                    }
                    if (!option.getAttribute("offering").includes(offering)) {
                        option.setAttribute("offering", option.getAttribute("offering") + " " + offering);
                    }
                }
            });

            Object.keys(json[offering]["procedure"]).forEach(function(procedure) {
                if (!procedureList.includes(procedure)) {
                    var select = document.getElementById("procedure");
                    var option = document.createElement("option");
                    option.setAttribute("value", procedure);
                    option.setAttribute("id", procedure);
                    option.setAttribute("class", offering + " _procedure");
                    var procedureName = procedure.split("/");
                    procedureName = procedureName[procedureName.length - 1]
                    option.innerHTML = procedureName;
                    select.append(option);
                    document.getElementById(procedure).style.display = "none";
                    procedureList.push(procedure);
                }
                else {
                    var option = document.getElementById(procedure);
                    if (!option.className.includes(offering)) {
                        option.setAttribute("class", option.className + " " + offering);
                    }
                }
            });
    
            Object.keys(json[offering]["procedure"]).forEach(function(procedure) {
                Object.keys(json[offering]["procedure"][procedure]["FOI"]["id"]).forEach(function(foi) {
                    if (!foiList.includes(json[offering]["procedure"][procedure]["FOI"]["id"][foi])) {
                        var select = document.getElementById("foi");
                        var option = document.createElement("option");
                        option.setAttribute("value", json[offering]["procedure"][procedure]["FOI"]["id"][foi]);
                        option.setAttribute("id", json[offering]["procedure"][procedure]["FOI"]["id"][foi]);
                        option.setAttribute("class", procedure + " " + offering + " _foi");
                        option.innerHTML = json[offering]["procedure"][procedure]["FOI"]["name"][foi];
                        select.append(option);
                        document.getElementById(json[offering]["procedure"][procedure]["FOI"]["id"][foi]).style.display = "none";
                        foiList.push(json[offering]["procedure"][procedure]["FOI"]["id"][foi]);
                    }
                    else {
                        var option = document.getElementById(json[offering]["procedure"][procedure]["FOI"]["id"][foi]);
                        if (!option.className.includes(procedure)) {
                            option.setAttribute("class", option.className + " " + procedure);
                        }
                        if (!option.className.includes(offering)) {
                            option.setAttribute("class", option.className + " " + offering);
                        }
                    }
                });
            });
        });
    }
    else {
        console.log("erreur service SOS");
    }
    
}


function updateObservableProperty() {

    var offering = document.getElementById("offering").value;

    if (offering == "none") {
        var observablePropertyOffering = document.getElementsByClassName("_observableProperty");
        for(var i=0; i<observablePropertyOffering.length ; i++){
            observablePropertyOffering[i].style.display = "block";
        }

        observable = document.getElementById("observableProperty").selectedIndex = "0";
    }
    else {
        var toHide = document.getElementsByClassName("_observableProperty");
        for(var i=0; i<toHide.length ; i++){
            toHide[i].style.display = "none";
        }
        var elem = document.getElementsByClassName(offering + " _observableProperty");
        for(var i=0; i<elem.length ; i++){
            elem[i].style.display = "block";
        }

        observable = document.getElementById("observableProperty").selectedIndex = "0";
    }
    
    var foiOffering = document.getElementsByClassName("_foi");
    for(var i=0; i<foiOffering.length ; i++){
        foiOffering[i].style.display = "none";
    }
    foi = document.getElementById("foi").selectedIndex = "0";

    var procedureOffering = document.getElementsByClassName("_procedure");
    for(var i=0; i<procedureOffering.length ; i++){
        procedureOffering[i].style.display = "none";
    }
    procedure = document.getElementById("procedure").selectedIndex = "0";
    
    enableButton();
}

function updateProcedure() {
    var observableProperty = document.getElementById("observableProperty").value;
    if (observableProperty == "none") {
        var foiObservable = document.getElementsByClassName("_foi");
        for(var i=0; i<foiObservable.length ; i++){
            foiObservable[i].style.display = "none";
        }
        foi = document.getElementById("foi").selectedIndex = "0";

        var procedureObservable = document.getElementsByClassName("_procedure");
        for(var i=0; i<procedureObservable.length ; i++){
            procedureObservable[i].style.display = "none";
        }
        procedure = document.getElementById("procedure").selectedIndex = "0";
    }
    else {
        var offering = document.getElementById(observableProperty).getAttribute("offering");
        offering = offering.split(" ");
        offering.forEach( function(elem) {
            var toShow = document.getElementsByClassName(elem + " _procedure");
            for(var i=0; i<toShow.length ; i++){
                toShow[i].style.display = "block";
            }
        });

        foi = document.getElementById("foi").selectedIndex = "0";
        procedure = document.getElementById("procedure").selectedIndex = "0";
    }
    enableButton();
}

function updateFoi() {
    var procedure = document.getElementById("procedure").value;
    if (procedure == "none") {
        var foiProcedure = document.getElementsByClassName("_foi");
        for(var i=0; i<foiProcedure.length ; i++){
            foiProcedure[i].style.display = "none";
        }
        foi = document.getElementById("foi").selectedIndex = "0";

    }
    else {
        var toShow = document.getElementsByClassName(procedure + " _foi");
        for(var i=0; i<toShow.length ; i++){
            toShow[i].style.display = "block";
        }

        foi = document.getElementById("foi").selectedIndex = "0";
    }
    enableButton();
}

function enableButton() {
    var foi = document.getElementById("foi").value;
    if (foi != "none") {
        document.getElementById("valider").disabled = false;
    }
    else {
        document.getElementById("valider").disabled = true;
    }
}

function resultatSOS() {
    var observableProperty = document.getElementById("observableProperty").value;
    var foi = document.getElementById("foi").value;
    var procedure = document.getElementById("procedure").value;
    var offering = document.getElementById("offering").value;
    $.ajax({
        url: '../sosAPI.php',
        //url: '../SOS/exResult.json',
        type: 'GET',
        data: {
            request: 'result',
            observableProperty: observableProperty,
            foi: foi,
            procedure: procedure,
            offering: offering
        },
        dataType: 'json',
        success: function(res) {
            var valeur = [];
            observablePropertyName = observableProperty.split("/");
            observablePropertyName = observablePropertyName[observablePropertyName.length - 1];
            res.Valeur.forEach(element => valeur.push(parseFloat(element)));
            const chart = Highcharts.chart('container', {
                chart: {
                    type: 'line'
                },
                title: {
                    text: 'Mesure'
                },
                xAxis: {
                    categories: res.Date
                },
                yAxis: {
                    title: {
                        text: 'valeur'
                    }
                },
                series: [{
                    name: observablePropertyName,
                    data: valeur
                }]
            });
        }
    });
}

