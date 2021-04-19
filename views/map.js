
var layers = [];
var map;
var view;
var viewNC;


/**
 Cette fonction permet d'initialiser la map d'OpenLayers d'ajouter le fond de carte et préparer l'eventlistener pour le GetFeatureInfo
 */
function initMap () {
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
        view: viewNC
    });

    // Fond de carte

    map.addLayer(new ol.layer.Image({
        source: new ol.source.ImageWMS({
            url: 'https://carto.gouv.nc/public/services/fond_imagerie/MapServer/WMSServer?',
            params: {'LAYERS': '0', 'TILED': true},
            serverType: 'geoserver',
        }),
        name: 'fond_de_carte_georep'
    }));

    map.addLayer(new ol.layer.Tile({
        source: new ol.source.OSM(),
        name: 'fond_de_carte_osm',
        visible: false
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
                map.getView().getProjection(),
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

    var dims = {
        a0: [1189, 841],
        a1: [841, 594],
        a2: [594, 420],
        a3: [420, 297],
        a4: [297, 210],
        a5: [210, 148],
    };

    var exportButton = document.getElementById('exportPDF');
    exportButton.addEventListener('click', function () {
        exportButton.disabled = true;
        document.body.style.cursor = 'progress';

        var format = document.getElementById('format').value;
        var resolution = document.getElementById('resolution').value;
        var dim = dims[format];
        var width = Math.round((dim[0] * resolution) / 25.4);
        var height = Math.round((dim[1] * resolution) / 25.4);
        var size = map.getSize();
        var viewResolution = map.getView().getResolution();

        map.once('rendercomplete', function () {
            var mapCanvas = document.createElement('canvas');
            mapCanvas.width = width;
            mapCanvas.height = height;
            var mapContext = mapCanvas.getContext('2d');
            Array.prototype.forEach.call( document.querySelectorAll('.ol-layer canvas'), function (canvas) {
                if ( canvas.width > 0 ) {
                    var opacity = canvas.parentNode.style.opacity;
                    mapContext.globalAlpha = opacity === '' ? 1 : Number(opacity);
                    var transform = canvas.style.transform;
                    var matrix = transform.match(/^matrix\(([^\(]*)\)$/)[1].split(',').map(Number);
                    CanvasRenderingContext2D.prototype.setTransform.apply( mapContext, matrix );
                    mapContext.drawImage(canvas, 0, 0);
                }
            });
            var pdf = new jsPDF('landscape', undefined, format);
            pdf.addImage( mapCanvas.toDataURL('image/jpeg'), 'JPEG', 0, 0, dim[0], dim[1] );
            pdf.save('map.pdf');
            map.setSize(size);
            map.getView().setResolution(viewResolution);
            exportButton.disabled = false;
            document.body.style.cursor = 'auto';
        });

        var printSize = [width, height];
        map.setSize(printSize);
        var scaling = Math.min(width / size[0], height / size[1]);
        map.getView().setResolution(viewResolution / scaling);
    }, false );
}


/**
 Permet de récupérer un fichier getCapabilities, le parse et l'affiche sous forme d'arborescence.
 */
 function capabilities( user ) {
    
    var theme = ["Mine", "Niveau de vie", "Autres"];
    var themeLower = [];
    theme.forEach(function(elem){
        themeLower.push(elem.toLowerCase().replaceAll(" ", "_"));
    });

    $.ajax({
        url: '../wms_internal.php',
        type: 'GET',
        data: {
            REQUEST: 'capabilities',
            user: user,
        },
        dataType: "xml",
        success: function(res) {
            document.querySelector('#contenue').style.display = 'block';
            var x = res.getElementsByTagName("Layer")[0].getElementsByTagName("Layer");

            if (x.length > 0) {
                var menu = document.createElement('ul');
                menu.setAttribute('id', 'menu');
                document.querySelector('#contenue').append(menu);
            }

            theme.forEach(function(elem){
                var workspace = document.createElement('li');
                workspace.innerHTML = elem;
                workspace.setAttribute('class', 'level1');
                var sousMenu = document.createElement('ul');
                sousMenu.setAttribute('class', 'sousMenu');
                sousMenu.setAttribute('id', elem.toLowerCase().replaceAll(" ", "_"));
                workspace.append(sousMenu);
                document.querySelector('#menu').append(workspace);
            });

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

                link.innerHTML += x[i].getElementsByTagName('Title')[0].innerHTML

                var str = '';
                for ( elem of styles) {
                    if (str == '') {
                        str += elem.getElementsByTagName('Name')[0].innerHTML;
                    } else {
                        str += ', ' + elem.getElementsByTagName('Name')[0].innerHTML;
                    }
                }
                str += '';
                layer.setAttribute('styles', str);
                layer.append(link);

                if (x[i].getElementsByTagName('Keyword').length > 0){
                    var wsName = x[i].getElementsByTagName('Keyword');
                    var append = false;
                    for (var j = 0; j < wsName.length; j++) {
                        var key = wsName[j].innerHTML.toLowerCase().replaceAll(" ", "_");
                        if(themeLower.includes(key)){
                            if (!append) {
                                document.querySelector('#'+key).append(layer);
                                append = true;
                            }
                            else {
                                var lay = layer.cloneNode(true);
                                document.querySelector('#'+key).append(lay);
                            }
                        }
                        else{
                            document.querySelector('#Autres').append(layer);
                        }
                    }
                }
                else{
                    document.querySelector('#Autres').append(layer);
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
            url: '../wms_internal.php',
            params: {'LAYERS': layername, 'TILED': true},
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
                    url: '../wms_internal.php',
                    params: {'LAYERS': layername, 'TILED': true, 'STYLES': style},
                    serverType: 'geoserver',
                });
                layer.setSource(source);
                var url = '../wms_internal.php?REQUEST=GetLegendGraphic&SERVICE=wms&FORMAT=image%2Fpng&WIDTH=20&HEIGHT=20&LAYER='+layername+'&STYLE='+style+'&TRANSPARENT=true';
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
    legende.src = '../wms_internal.php?REQUEST=GetLegendGraphic&SERVICE=wms&FORMAT=image%2Fpng&WIDTH=20&HEIGHT=20&LAYER='+layer+'&TRANSPARENT=true';
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


/**
 Permet de faire apparaitre la liste des layers dans un onglet rétractable
 */
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
