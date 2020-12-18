
var map;
var view;
var viewNC;


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
            url: '../getMap.php',
            params: {'LAYERS': '0', 'TILED': true, 'DOMAIN': 'https://carto.gouv.nc/public/services/fond_imagerie/MapServer/WMSServer?'},
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

    var procedure = new ol.layer.Vector({
        source: new ol.source.Vector({
            url: '../sosAPI.php?request=FOI&offering="http://www.52north.org/test/offering/10"&procedure="http://www.52north.org/test/procedure/9"',
            format: new ol.format.GML()
        }),
    });
    map.addLayer(procedure);

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
