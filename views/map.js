
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
                var js = "addLay(\""+name+"\", \"\")";
                link.setAttribute('href', "javascript:void(0);");
                link.setAttribute('ondblclick', js);
                layer.setAttribute('id', 'z'+name.replace(':', '__')+'Layer');
                var styles = x[i].getElementsByTagName('Style');
                var wsName = name.substr(0, name.indexOf(':'));
                if (x[i].getElementsByTagName('SRS').length > 0) {
                    link.innerHTML += name.substr(name.indexOf(':')+1) + ' | ' +
                    x[i].getElementsByTagName('SRS')[0].innerHTML + ' | ';
                } else if (x[i].getElementsByTagName('CRS').length > 0) {
                    link.innerHTML += name.substr(name.indexOf(':')+1) + ' | ' +
                    x[i].getElementsByTagName('CRS')[0].innerHTML + ' | ';
                }

                var str = '';
                for ( elem of styles) {
                    if (str == '') {
                        link.innerHTML += elem.getElementsByTagName('Name')[0].innerHTML;
                        str += elem.getElementsByTagName('Name')[0].innerHTML;
                    } else {
                        link.innerHTML += ', ' + elem.getElementsByTagName('Name')[0].innerHTML;
                        str += ', ' + elem.getElementsByTagName('Name')[0].innerHTML;
                    }
                }
                str += '';
                layer.setAttribute('styles', str);
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


function addLay ( layername, style ) {
    var layer;
    if ( style == "" ) {
        map.addLayer( layer = new ol.layer.Image ({
            visible: true,
            name: layername,
            source: new ol.source.ImageWMS({
                url: '../getMap.php',
                params: {'LAYERS': layername, 'TILED': true, 'DOMAIN': 'http://localhost:8080/geoserver/wms?'},
                serverType: 'geoserver',
            })
        }));
    }
    else {
        map.addLayer( layer = new ol.layer.Image ({
            visible: true,
            name: layername,
            source: new ol.source.ImageWMS({
                url: '../getMap.php',
                params: {'LAYERS': layername, 'TILED': true, 'DOMAIN': 'http://localhost:8080/geoserver/wms?', 'STYLES': style},
                serverType: 'geoserver',
            })
        }));
    }

    layers.push(layer);

    var active = document.createElement('li');
    active.innerHTML = layername;
    active.setAttribute('id', 'z'+layername.replace(':', '__'));

    var slider = document.createElement('input');
    slider.type = 'range';
    slider.min = 0;
    slider.max = 1;
    slider.step = 0.01;
    slider.value = 1;
    slider.setAttribute('class', 'slider');
    slider.setAttribute('id', layername.replace(':', '__')+'Slider');
    var js = "opacityChange(this.value, \""+layername+"\")";
    slider.setAttribute('onchange', js);
    active.append(slider);

    var styles = document.querySelector('#'+'z'+layername.replace(':', '__')+'Layer').getAttribute('styles');
    styles = styles.split(',');

    var select = document.createElement('select');
    select.name = 'styles';
    select.setAttribute('id', layername.replace(':', '__')+'Select');
    var js2 = "styleChange(this.value, \""+layername+"\")";
    select.setAttribute('onchange', js2);

    for (elem of styles ) {
        var option = document.createElement('option');
        option.value = elem;
        option.innerHTML = elem;
        if (elem == style) {
            option.selected = 'selected';
        }
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
                document.getElementById('features').innerHTML = '';

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


function styleChange( style, layername ) {
    removeLay(layername);
    addLay(layername, style);
}


function opacityChange( opacity, layername ) {
    map.getLayers().forEach(function (layer) {
        if (layer != null) {
            if ( layer.get('name') != undefined & layer.get('name') == layername ) {
                layer.setOpacity(parseFloat(opacity));
            }
        }
    });
}
