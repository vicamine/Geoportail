
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

    $.ajax({
        url: '../getCapabilities.php',
        type: 'GET',
        data: {
            url: 'http://localhost:8080/geoserver/wms?service=wms&version=1.1.1&request=GetCapabilities',
            request: 'capabilities'
        },
        dataType: 'xml',
        success: function(res) {
            var x = res.getElementsByTagName("Layer")[0].getElementsByTagName("Layer");
            var workspaceList = [];

            if (x.length > 0) {
                var menu = document.createElement('ul');
                menu.setAttribute('id', 'menu');
                document.querySelector('#contenue').append(menu);
            }

            for (i = 0; i < x.length; i++) {
                if ( x[i].getElementsByTagName('Name')[0].innerHTML.indexOf(':') == -1 ) {
                    if ( workspaceList.indexOf('Group of layer' ) == -1) {
                        workspaceList.push('Group of layer');
                        var workspace = document.createElement('li');
                        workspace.innerHTML = 'Group of layer';
                        workspace.setAttribute('class', 'level1');
                        var sousMenu = document.createElement('ul');
                        sousMenu.setAttribute('class', 'sousMenu');
                        sousMenu.setAttribute('id', 'groupLayer');
                        workspace.append(sousMenu);
                        document.querySelector('#menu').append(workspace);
                    }
                } else {
                    var workspaceName = x[i].getElementsByTagName('Name')[0].innerHTML.substr(0, x[i].getElementsByTagName('Name')[0].innerHTML.indexOf(':'));
                    if ( workspaceList.indexOf(workspaceName) == -1 ) {
                        workspaceList.push(workspaceName);
                        var workspace = document.createElement('li');
                        workspace.innerHTML = workspaceName;
                        workspace.setAttribute('class', 'level1');
                        var sousMenu = document.createElement('ul');
                        sousMenu.setAttribute('class', 'sousMenu');
                        sousMenu.setAttribute('id', workspaceName);
                        workspace.append(sousMenu);
                        document.querySelector('#menu').append(workspace);
                    }
                }
            }

            for (i = 0; i < x.length; i++) {

                var layer = document.createElement('li');
                var link = document.createElement('a');
                var name = x[i].getElementsByTagName('Name')[0].innerHTML;
                var js = "addLay(\""+name+"\")";
                link.setAttribute('href', "javascript:void(0);");
                link.setAttribute('ondblclick', js);
                layer.setAttribute('id', 'z'+name.replace(':', '__')+'Layer');
                //var bouton = document.createElement('input');
                var styles = x[i].getElementsByTagName('Style');
                var wsName = name.substr(0, name.indexOf(':'));
                //bouton.type = 'button';
                //bouton.value = 'unselected';
                if (x[i].getElementsByTagName('SRS').length > 0) {
                    link.innerHTML += name.substr(name.indexOf(':')+1) + ' | ' +
                    x[i].getElementsByTagName('SRS')[0].innerHTML + ' | ';
                } else {
                    link.innerHTML += name.substr(name.indexOf(':')+1) + ' | ' +
                    x[i].getElementsByTagName('CRS')[0].innerHTML + ' | ';
                }

                var str = '';
                for ( elem of styles) {
                    link.innerHTML += elem.getElementsByTagName('Name')[0].innerHTML + ' ';
                    if (str == '') {
                        str += elem.getElementsByTagName('Name')[0].innerHTML;
                    } else {
                        str += ',' + elem.getElementsByTagName('Name')[0].innerHTML;
                    }
                }
                str += '';
                layer.setAttribute('styles', str);

                //bouton.setAttribute( 'onclick', 'afficher_layer(this);' );
                //bouton.setAttribute( 'name', x[i].getElementsByTagName('Name')[0].innerHTML);
                //layer.append( bouton );
                layer.append(link);

                if ( wsName == '' ) {
                    document.querySelector('#groupLayer').append(layer);
                } else {
                    document.querySelector('#'+wsName).append(layer);
                }
            }
        }
    });
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
            url: '../getMap.php',
            params: {'LAYERS': layername, 'TILED': true, 'DOMAIN': 'http://localhost:8080/geoserver/wms?'},
            serverType: 'geoserver',
        })
    }));
    layers.push(layer);

    var active = document.createElement('li');
    active.innerHTML = layername;
    active.setAttribute('id', 'z'+layername.replace(':', '__'));

    var slider = document.createElement('input');
    slider.type = 'range';
    slider.min = 1;
    slider.max = 100;
    slider.value = 50
    slider.setAttribute('class', 'slider');
    slider.setAttribute('id', layername.replace(':', '__')+'Slider');
    active.append(slider);

    var styles = document.querySelector('#'+'z'+layername.replace(':', '__')+'Layer').getAttribute('styles');
    styles = styles.split(',');

    var select = document.createElement('select');
    select.name = 'styles';
    select.setAttribute('id', layername.replace(':', '__')+'Select');

    for (elem of styles ) {
        var option = document.createElement('option');
        option.value = elem;
        option.innerHTML = elem;
        select.append(option);
    }

    active.append(select);

    var js = "removeLay(\""+layername+"\")";
    var bouton = document.createElement('input');
    bouton.setAttribute('id', layername.replace(':', '__')+'Delete');
    bouton.setAttribute('name', layername);
    bouton.type = 'button';
    bouton.value = 'delete';
    bouton.setAttribute( 'onclick', js );
    active.append(bouton);

    document.querySelector('#active ul').append(active);
}


function removeLay ( layername ) {
    var toDelete;
    map.getLayers().forEach(function (layer) {
        if (layer != null) {
            if ( layer.get('name') != undefined & layer.get('name') == layername ) {
                map.removeLayer(layer);

                del = document.querySelector('#'+'z'+layername.replace(':', '__'));
                del.remove();

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
