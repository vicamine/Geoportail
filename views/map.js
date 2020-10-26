
var layers = [];
var map;
var view;

function initMap () {
    view = new ol.View({
        center: ol.proj.fromLonLat([ 0, 0 ]),
        zoom: 2,
        projection: 'EPSG:4326'
    });

    map = new ol.Map({
        target: 'map',
        view: view
    });

    map.addLayer(new ol.layer.Tile({
        source: new ol.source.OSM(),
        name: 'fond_de_carte'
    }));

    map.on('singleclick', function (evt) {
        document.getElementById('features').innerHTML = '';

        layers.forEach(function (layer) {
            var viewResolution = /** @type {number} */ (view.getResolution());
            var url = layer.getSource().getFeatureInfoUrl(
                evt.coordinate,
                viewResolution,
                'EPSG:4326',
                {'INFO_FORMAT': 'text/html'}
            );
            if (url) {
                fetch(url)
                .then(function (response) { return response.text(); })
                .then(function (html) {
                    document.getElementById('features').innerHTML += html + '<br/>';
                });
            }
        });
    });

}


function capabilities() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Typical action to be performed when the document is ready:
            //document.getElementById("contenue").innerHTML = xhttp.responseText;
            var xmlData = xhttp.responseXML;
            var x = xmlData.getElementsByTagName("Layer")[0].getElementsByTagName("Layer");

            for (i = 0; i < x.length; i++) {
                var layer = document.createElement('p');
                var bouton = document.createElement('input');
                var styles = x[i].getElementsByTagName('Style');
                bouton.type = 'button';
                bouton.value = 'unselected';

                if ( x[i].getElementsByTagName('Name')[0].innerHTML.indexOf(':') == -1 ) {
                    layer.innerHTML = 'Group of layer | ';
                } else {
                    layer.innerHTML = 'Layer | ';
                }

                layer.innerHTML += x[i].getElementsByTagName('Name')[0].innerHTML + ' | ' +
                x[i].getElementsByTagName('SRS')[0].innerHTML + ' | ';

                for ( elem of styles) {
                    layer.innerHTML += elem.getElementsByTagName('Name')[0].innerHTML + ' ';
                }

                bouton.setAttribute( 'onclick', 'afficher_layer(this);' );
                bouton.setAttribute( 'name', x[i].getElementsByTagName('Name')[0].innerHTML);
                layer.append( bouton );
                document.querySelector('#contenue').append(layer);
            }
        }
    };

    xhttp.open("GET", "http://localhost:8080/geoserver/wms?service=wms&version=1.1.1&request=GetCapabilities", true);
    xhttp.overrideMimeType('text/xml');
    xhttp.send();
}


function afficher_layer(bouton) {
    if ( bouton.value == 'unselected' ) {
        bouton.value = 'selected';
        addLay (bouton.getAttribute('name'));
    } else {
        bouton.value = 'unselected';
        removeLay(bouton.getAttribute('name'));
    }
}


function addLay ( layername ) {
    var layer;
    map.addLayer( layer = new ol.layer.Image ({
        visible: true,
        name: layername,
        source: new ol.source.ImageWMS({
            url: 'http://localhost:8080/geoserver/wms?service=wms',
            params: {'LAYERS': layername, 'TILED': true},
            serverType: 'geoserver',
        })
    }));
    layers.push(layer);
}


function removeLay ( layername ) {
    var toDelete;
    map.getLayers().forEach(function (layer) {
        if (layer != null) {
            if ( layer.get('name') != undefined & layer.get('name') == layername ) {
                map.removeLayer(layer);

                layers.forEach(function (elem, index) {
                    if ( elem.get('name') == layer.get('name')) {
                        toDelete = index;
                    }
                });
                layers.splice(toDelete, 1);

            }
        }
    });
}
