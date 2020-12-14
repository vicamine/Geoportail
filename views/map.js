
var layers = [];
var map;
var view;


/**
 Cette fonction permet d'initialiser la map d'OpenLayers d'ajouter le fond de carte et préparer l'eventlistener pour le GetFeatureInfo
 */
function initMap () {
    view = new ol.View({
        zoom: 7,
        center: [ 165.568866, -21.425749 ],
        projection: 'EPSG:4326'
    });

    map = new ol.Map({
        target: 'map',
        view: view
    });

    // Fond de carte
    map.addLayer(new ol.layer.Tile({
        source: new ol.source.OSM(),
        name: 'fond_de_carte_osm'
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


    map.on('singleclick', function (evt) {
        var features = '';
        layers.forEach(function (layer) {
            var viewResolution = view.getResolution();
            var url = layer.getSource().getFeatureInfoUrl(
                evt.coordinate,
                viewResolution,
                'EPSG:4326',
                {'INFO_FORMAT': 'text/html'}
            );
            $.ajax({
                url: url,
                type: 'GET',
                async: false,
                timeout: 2000,
                success: function ( html ) {
                    features += html + '<br/>';
                }
            });
        });
        document.getElementById('features').innerHTML = features;
    });

    document.getElementById('export').addEventListener('click', function () {
        map.once('rendercomplete', function () {
            var mapCanvas = document.createElement('canvas');
            var size = map.getSize();
            mapCanvas.width = size[0];
            mapCanvas.height = size[1];
            var mapContext = mapCanvas.getContext('2d');
            Array.prototype.forEach.call(
                document.querySelectorAll('.ol-layer canvas'),
                function (canvas) {
                    if (canvas.width > 0) {
                        var opacity = canvas.parentNode.style.opacity;
                        mapContext.globalAlpha = opacity === '' ? 1 : Number(opacity);
                        var transform = canvas.style.transform;
                        var matrix = transform.match(/^matrix\(([^\(]*)\)$/)[1].split(',').map(Number);
                        CanvasRenderingContext2D.prototype.setTransform.apply( mapContext, matrix );
                        mapContext.drawImage(canvas, 0, 0);
                    }
                }
            );
            if (navigator.msSaveBlob) {
                navigator.msSaveBlob(mapCanvas.msToBlob(), 'map.png');
            }
            else {
                var link = document.getElementById('image-download');
                link.href = mapCanvas.toDataURL();
                link.click();
            }
        });
        map.renderSync();
    });
}


/**
 Permet de récupérer un fichier getCapabilities, le parse et l'affiche sous forme d'arborescence.
 */
function capabilities( user ) {

    $.ajax({
        url: '../getCapabilities.php',
        type: 'GET',
        data: {
            url: 'http://localhost:8080/geoserver/wms?service=wms&version=1.1.1&request=GetCapabilities',
            request: 'capabilities',
            user: user,
        },
        dataType: 'xml',
        success: function(res) {
            document.querySelector('#contenue').style.display = 'block';
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
                layer.setAttribute('sum', x[i].getElementsByTagName('Abstract')[0].innerHTML);
                layer.setAttribute('title', x[i].getElementsByTagName('Title')[0].innerHTML);
                layer.setAttribute('name', x[i].getElementsByTagName('Name')[0].innerHTML);
                var styles = x[i].getElementsByTagName('Style');
                var wsName = name.substr(0, name.indexOf(':'));
                if (x[i].getElementsByTagName('SRS').length > 0) {
                    link.innerHTML += x[i].getElementsByTagName('Title')[0].innerHTML + ' | ' +
                    x[i].getElementsByTagName('SRS')[0].innerHTML + ' | ';
                } else if (x[i].getElementsByTagName('CRS').length > 0) {
                    link.innerHTML += x[i].getElementsByTagName('Title')[0].innerHTML + ' | ' +
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
            map.updateSize();
        }
    });
}


/**
 Permet d'ajouter une layer sur la map et permet ensuite de la manager via une partie réservé
 */
function addLay ( layername, style ) {
    var adding = true;
    layers.forEach(function( elem ) {
        if ( elem.get('name') == layername ) {
            adding = false;
        }
    });
    if ( adding == false ) {
        return;
    }
    var layer;
    map.addLayer( layer = new ol.layer.Image ({
        visible: true,
        name: layername,
        source: new ol.source.ImageWMS({
            url: '../getMap.php',
            params: {'LAYERS': layername, 'TILED': true, 'DOMAIN': 'http://localhost:8080/geoserver/wms?'},
            serverType: 'geoserver',
        }),
        zIndex: layers.length+1
    }));
    legende( layername );

    layers.push(layer);

    var active = document.createElement('li');
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

    var up = document.createElement('input');
    var jsUp = "moveUp(\""+layername+"\")";
    up.setAttribute('id', layername.replace(':', '__') + 'Up');
    up.setAttribute('name', layername);
    up.type = 'button';
    up.value = 'up';
    up.setAttribute( 'onclick', jsUp );
    active.append(up);

    var down = document.createElement('input');
    var jsDown = "moveDown(\""+layername+"\")";
    down.setAttribute('id', layername.replace(':', '__') + 'Down');
    down.setAttribute('name', layername);
    down.type = 'button';
    down.value = 'down';
    down.setAttribute( 'onclick', jsDown );
    active.append(down);

    var js = "removeLay(\""+layername+"\")";
    var bouton = document.createElement('input');
    bouton.setAttribute('id', layername.replace(':', '__')+'Delete');
    bouton.setAttribute('name', layername);
    bouton.type = 'button';
    bouton.value = 'delete';
    bouton.setAttribute( 'onclick', js );
    active.append(bouton);

    document.querySelector('#z'+layername.replace(':', '__')+'Active').append(active);
}


/**
 Permet de supprimer une layer de la map et supprime toutes ses reférences
 */
function removeLay ( layername ) {
    var toDelete;
    var actualZ;
    map.getLayers().forEach(function (layer) {
        if (layer != null) {
            if ( layer.get('name') != undefined & layer.get('name') == layername ) {
                actualZ = layer.getZIndex();
                map.removeLayer(layer);
                document.getElementById('features').innerHTML = '';

                del = document.querySelector('#'+'z'+layername.replace(':', '__'));
                del.remove();

                delLegend = document.querySelector('#z'+layername.replace(':', '__')+'Div');
                if ( delLegend != null ) {
                    delLegend.remove();
                }

                layers.forEach(function (elem, index) {
                    if ( elem.get('name') == layer.get('name')) {
                        toDelete = index;
                    }
                });
                layers.splice(toDelete, 1);

            }
        }
    });
    map.getLayers().forEach(function (layer) {
        if (layer != null) {
            if ( layer.getZIndex() > actualZ ) {
                layer.setZIndex(layer.getZIndex()-1);
            }
        }
    });
}


/**
 Permet de changer le style d'une layer en la supprimant puis la rajoutant avec le style demandé
 */
function styleChange( style, layername ) {
    map.getLayers().forEach(function (layer) {
        if (layer != null) {
            if ( layer.get('name') != undefined & layer.get('name') == layername ) {
                source = new ol.source.ImageWMS({
                    url: '../getMap.php',
                    params: {'LAYERS': layername, 'TILED': true, 'DOMAIN': 'http://localhost:8080/geoserver/wms?', 'STYLES': style},
                    serverType: 'geoserver',
                });
                layer.setSource(source);
                var url = '../getMap.php?REQUEST=GetLegendGraphic&SERVICE=wms&FORMAT=image%2Fpng&WIDTH=20&HEIGHT=20&LAYER='+layername+'&STYLE='+style+'&TRANSPARENT=true&DOMAIN=http://localhost:8080/geoserver/wms';
                document.querySelector('#z'+layername.replace(':', '__')+'Legende').src = url;
            }
        }
    });

}


/**
 Permet de gérer la transparence d'une layer grace à des méthodes de la librairie OpenLayers
 */
function opacityChange( opacity, layername ) {
    map.getLayers().forEach(function (layer) {
        if (layer != null) {
            if ( layer.get('name') != undefined & layer.get('name') == layername ) {
                layer.setOpacity(parseFloat(opacity));
            }
        }
    });
}


/**
 Permet de récupérer la légende du style d'une layer ( GetLegendGraphic )
 */
function legende( layer ) {
    var div = document.createElement('div');
    div.setAttribute('id', 'z'+layer.replace(':', '__')+'Div');
    div.setAttribute('class', 'legendElem');
    var legende = document.createElement('img');
    legende.alt = 'Légende de la layer '+layer;
    legende.src = '../getMap.php?REQUEST=GetLegendGraphic&SERVICE=wms&FORMAT=image%2Fpng&WIDTH=20&HEIGHT=20&LAYER='+layer+'&TRANSPARENT=true&DOMAIN=http://localhost:8080/geoserver/wms';
    legende.setAttribute('id', 'z'+layer.replace(':', '__')+'Legende');
    var sum = document.querySelector('#z'+layer.replace(':', '__')+'Layer').getAttribute('sum');
    sum = "<p>" + sum + "</p>";
    var name = document.querySelector('#z'+layer.replace(':', '__')+'Layer').getAttribute('title');

    if ( document.querySelector('#z'+layer.replace(':', '__')+'Layer').getAttribute('name').substr(document.querySelector('#z'+layer.replace(':', '__')+'Layer').getAttribute('name').indexOf(':')+1) != document.querySelector('#z'+layer.replace(':', '__')+'Layer').getAttribute('title') ) {
        name += ' / ' + document.querySelector('#z'+layer.replace(':', '__')+'Layer').getAttribute('name').substr(document.querySelector('#z'+layer.replace(':', '__')+'Layer').getAttribute('name').indexOf(':')+1);
    }

    div.innerHTML = "<div><em>"+ name + "</em><br></div>";
    div.innerHTML += "<div id=\"z"+ layer.replace(':', '__') +"Active\">" + sum + "</div>";
    document.querySelector('#legende').append(div);
    document.querySelector( '#z'+layer.replace(':', '__')+'Div div').append(legende);
}


/**
 Permet de changer le fond de carte d'OpenLayers
 */
function fondChange( fond ) {
    var fondCarte = [ 'fond_de_carte_stamen_terrain', 'fond_de_carte_stamen_toner', 'fond_de_carte_stamen_watercolor', 'fond_de_carte_osm' ];
    map.getLayers().forEach(function (layer) {
        if (layer.get('name') != undefined && fondCarte.indexOf( layer.get('name')) != -1 ) {
            layer.setVisible(false);
        }
    });
    map.getLayers().forEach(function (layer) {
        if (layer.get('name') != undefined && layer.get('name') == fond ) {
            layer.setVisible(true);
        }
    });
}


/**
 Permet de faire monter une layer d'un niveau
 */
function moveUp( layername ) {
    var actualZ;
    var actualLay;
    var swapLay;
    map.getLayers().forEach(function (layer) {
        if (layer != null) {
            if ( layer.get('name') != undefined & layer.get('name') == layername ) {
                actualZ = layer.getZIndex();
                actualLay = layer;
            }
        }
    });
    if ( actualZ != layers.length ) {
        map.getLayers().forEach(function (layer) {
            if (layer != null) {
                if ( layer.getZIndex() == actualZ+1 ) {
                    swapLay = layer;
                }
            }
        });
        actualLay.setZIndex(actualZ+1);
        swapLay.setZIndex(actualZ);
    }
}


/**
 Permet de faire descendre une layer d'un niveau
 */
function moveDown( layername ) {
    var actualZ;
    var actualLay;
    var swapLay;
    map.getLayers().forEach(function (layer) {
        if (layer != null) {
            if ( layer.get('name') != undefined & layer.get('name') == layername ) {
                actualZ = layer.getZIndex();
                actualLay = layer;
            }
        }
    });
    if ( actualZ != 1 ) {
        map.getLayers().forEach(function (layer) {
            if (layer != null) {
                if ( layer.getZIndex() == actualZ-1 ) {
                    swapLay = layer;
                }
            }
        });
        actualLay.setZIndex(actualZ-1);
        swapLay.setZIndex(actualZ);
    }
}


function displayCapa() {
    if ( document.querySelector('#contenue').style.display == 'none' ) {
        document.querySelector('#contenue').style.display = 'block';
        document.querySelector('#onglet h2').innerHTML = 'X';
        map.updateSize();
    }
    else {
        document.querySelector('#contenue').style.display = 'none';
        document.querySelector('#onglet h2').innerHTML = '<';
        map.updateSize();
    }
}
