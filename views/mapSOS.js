
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
    map.addLayer(new ol.layer.Image({
        source: new ol.source.ImageWMS({
            url: 'https://carto.gouv.nc/public/services/fond_imagerie/MapServer/WMSServer?',
            params: {'LAYERS': '0', 'TILED': true},
            serverType: 'geoserver',
        }),
        name: 'fond_de_carte_georep',
        visible: false
    }));

    map.addLayer(new ol.layer.Tile({
        source: new ol.source.OSM(),
        name: 'fond_de_carte_osm',
        //visible: false
    }));

    map.addLayer(new ol.layer.Tile({
        source: new ol.source.Stamen({
            layer: 'terrain',
        }),
        name: 'fond_de_carte_stamen_terrain',
        visible: false
    }));

    map.addLayer(new ol.layer.Tile({
        source: new ol.source.Stamen({
            layer: 'watercolor',
        }),
        name: 'fond_de_carte_stamen_watercolor',
        visible: false
    }));

    map.addLayer(new ol.layer.Tile({
        source: new ol.source.Stamen({
            layer: 'toner',
        }),
        name: 'fond_de_carte_stamen_toner',
        visible: false
    }));
    
    /*
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.open("GET", "../SOS/testPolygoneNC3.gml", true);
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
            })
        });

        // Add features to the layer's source
        vector.getSource().addFeatures(features);

        map.addLayer(vector);
    };
    xmlhttp.send();*/
    
    var procedure = new ol.layer.Vector({
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
    });
    map.addLayer(procedure);
    layers.push(procedure);
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


function fondChange( fond ) {
    var fondCarte = [ 'fond_de_carte_stamen_terrain', 'fond_de_carte_stamen_toner', 'fond_de_carte_stamen_watercolor', 'fond_de_carte_osm', 'fond_de_carte_georep' ];
    map.getLayers().forEach(function (layer) {
        if (layer.get('name') != undefined && fondCarte.indexOf( layer.get('name')) != -1 ) {
            layer.setVisible(false);
        }
    });
    map.getLayers().forEach(function (layer) {
        if (layer.get('name') != undefined && layer.get('name') == fond ) {
            layer.setVisible(true);
            if ( layer.get('name') != 'fond_de_carte_georep' ) {
                if ( map.getView().getProjection().getCode() == 'EPSG:3163' ) {
                    map.setView(view);
                    map.getView().setCenter([ 165.568866, -21.425749 ]);
                }
            }
            else {
                map.setView(viewNC);
                map.getView().setCenter([ 323399.78, 395030.91 ]);
            }
        }
    });
}


function createGML( jsonFile ){
    var featureMemberTemplate = "<gml:featureMember><ogr:capteurs fid=\"capteur.1\"><ogr:geometryProperty></ogr:geometryProperty><ogr:id>1</ogr:id></ogr:capteurs></gml:featureMember>";
    var json = null;
    $.ajax({
        'async': false,
        'url': jsonFile,
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


