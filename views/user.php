<?php
    ob_start();
?>
<div id="content">

    <div id="left">

        <?php if ( isset($_SESSION['id'])){ ?>
            <p> <a href='/<?php echo $ROOT; ?>/index.php/addLayer' > Ajouter des layers ou des styles ! </a> </p>
        <?php } ?>

        <h2> Gestion des Layers </h2>

        <div class='layers'>

            <form class='formulaire' action="<?php echo $URI; ?>" method="post">
                <input type="submit" name="supprimer" value="Supprimer">
                <br><br>
            </form>

        </div>

        <h2> Gestion des Styles </h2>

        <div class="styles">

            <form class='formulaireStyle' action="<?php echo $URI; ?>" method="post">
                <input type="submit" name="supprimerStyle" value="Supprimer">
                <br><br>
            </form>

        </div>

    </div>

    <div id="layer">
        <?php if ( $action == 'Layer' ) { ?>
            <h2> Détails </h2>
            <div id="details"> </div>

            <div class="style">

                <div class="availableStyle">

                    <h3> Available styles </h3>
                    <div id="availableStyle">
                        <ul> </ul>
                    </div>

                </div>

                <div class="currentStyle">

                    <h3> Current styles </h3>
                    <div id="currentStyle">
                        <ul> </ul>
                    </div>

                </div>

            </div>
        <?php } ?>

    </div>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script type="text/javascript">

    /**
    Cette fonction permet de récupérer et afficher les layers d'un utilisateurs afin quels soit managés et qu'on puisse leur ajouter ou retirer des styles
    */
    function layers() {
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
                <?php if (isset($layer)) {?>
                    document.querySelector('#details').append( document.createElement('p').innerHTML = "layername    |    projection    |    default styles" );
                <?php } ?>
                for (i = 0; i < x.length; i++) {

                    var name = x[i].getElementsByTagName('Name')[0].innerHTML;

                    if ( x[i].getElementsByTagName('Name')[0].innerHTML.indexOf('<?php echo $_SESSION['login'] ?>:') == -1 ) {
                        continue;
                    }

                    <?php if (isset($layer)) {?>
                    if ( name == '<?php echo $layer; ?>' ) {
                        var data = document.createElement('p');
                        if (x[i].getElementsByTagName('SRS').length > 0) {
                            data.innerHTML += x[i].getElementsByTagName('Title')[0].innerHTML + ' | ' +
                            x[i].getElementsByTagName('SRS')[0].innerHTML + ' | ';
                        } else if (x[i].getElementsByTagName('CRS').length > 0){
                            data.innerHTML += x[i].getElementsByTagName('Title')[0].innerHTML + ' | ' +
                            x[i].getElementsByTagName('CRS')[0].innerHTML + ' | ';
                        }
                        var styles = x[i].getElementsByTagName('Style');
                        var unavailableStyle = [];
                        data.innerHTML += styles[0].getElementsByTagName('Name')[0].innerHTML + '  ';
                        for ( elem of styles) {
                            unavailableStyle.push(elem.getElementsByTagName('Name')[0].innerHTML);
                            if (elem.getElementsByTagName('Name')[0].innerHTML != styles[0].getElementsByTagName('Name')[0].innerHTML) {
                                var style = elem.getElementsByTagName('Name')[0].innerHTML;
                                var li = document.createElement('li');
                                var link = document.createElement('a');
                                var js = "current( \"<?php echo $layer; ?>\", \""+elem.getElementsByTagName('Name')[0].innerHTML+"\")";
                                link.setAttribute('href', "javascript:void(0);");
                                link.setAttribute('ondblclick', js);
                                link.innerHTML = style;
                                li.append(link);
                                li.setAttribute('id', style + 'Style');
                                document.querySelector('#currentStyle ul').append(li);
                            }
                        }
                        document.querySelector('#details').append( data );

                        $.ajax({
                            url: 'http://localhost:8080/geoserver/rest/styles.xml',
                            type: 'GET',
                            dataType: 'xml',
                            success: function(res) {
                                var y = res.getElementsByTagName('name');
                                for (j=0; j < y.length; j++) {
                                    if ( !unavailableStyle.includes(y[j].innerHTML) ) {
                                        var li = document.createElement('li');
                                        var link = document.createElement('a');
                                        var js = "available( \"<?php echo $layer; ?>\", \""+y[j].innerHTML+"\")";
                                        link.setAttribute('href', "javascript:void(0);");
                                        link.setAttribute('ondblclick', js);
                                        link.innerHTML = y[j].innerHTML;
                                        li.setAttribute('id', y[j].innerHTML + 'Style');
                                        li.append(link);
                                        document.querySelector('#availableStyle ul').append(li);
                                    }
                                }
                            }
                        });

                        $.ajax({
                            url: 'http://localhost:8080/geoserver/rest/workspaces/<?php echo $_SESSION['login']; ?>/styles.xml',
                            type: 'GET',
                            dataType: 'xml',
                            success: function(res) {
                                var z = res.getElementsByTagName('name');
                                for (j=0; j < z.length; j++) {
                                    if ( !unavailableStyle.includes('<?php echo $_SESSION['login']; ?>:'+z[j].innerHTML) ) {
                                        var li = document.createElement('li');
                                        var link = document.createElement('a');
                                        var js = "available( \"<?php echo $layer; ?>\", \""+'<?php echo $_SESSION['login']; ?>:'+z[j].innerHTML+"\")";
                                        link.setAttribute('href', "javascript:void(0);");
                                        link.setAttribute('ondblclick', js);
                                        link.innerHTML = '<?php echo $_SESSION['login']; ?>:' + z[j].innerHTML;
                                        li.setAttribute('id', z[j].innerHTML + 'Style');
                                        li.append(link);
                                        document.querySelector('#availableStyle ul').append(li);
                                    }
                                }
                            }
                        });
                    }
                    <?php } ?>

                    var layer = document.createElement('input');
                    var layerLabel = document.createElement('a');
                    layerLabel.innerHTML = x[i].getElementsByTagName('Title')[0].innerHTML;
                    href = '/<?php echo $ROOT; ?>/index.php/user?action=Layer&data='+x[i].getElementsByTagName('Name')[0].innerHTML;
                    layerLabel.setAttribute('href', href);
                    layer.type = 'checkbox';
                    layer.name = 'layer[]';
                    layer.value = x[i].getElementsByTagName('Name')[0].innerHTML;
                    layer.setAttribute('id', x[i].getElementsByTagName('Name')[0].innerHTML);
                    var privacy = document.createElement('input');
                    privacy.type = 'button';
                    privacy.name = 'privacy'+name;
                    privacy.onclick = function () { privacySwitch(this) };
                    privacy.setAttribute('id', 'privacy' + x[i].getElementsByTagName('Name')[0].innerHTML );
                    $.ajax({
                        url: "../model.php",
                        type: "POST",
                        async: false,
                        data: {
                            action: "getPrivacy",
                            layer: x[i].getElementsByTagName('Name')[0].innerHTML,
                        },
                        success: function(res) {
                            console.log(res);
                            if ( res == 't' ) {
                                privacy.value = 'public';
                            }
                            else {
                                privacy.value = 'private';
                            }
                        }
                    });
                    document.querySelector('.formulaire').append(layer);
                    document.querySelector('.formulaire').append(layerLabel);
                    document.querySelector('.formulaire').append(privacy);
                    document.querySelector('.formulaire').append(document.createElement('br'));
                }
            }
        });
    }


    /**
    Récupère la liste des styles d'un utilisateur et les affiches sur la page afin qu'ils puissent être managés
    */
    function styles () {
        $.ajax({
            url: "http://localhost:8080/geoserver/rest/workspaces/<?php echo $_SESSION['login']; ?>/styles.xml",
            type: 'GET',
            dataType: 'xml',
            success: function(res) {
                var x = res.getElementsByTagName("name");
                for (i = 0; i < x.length; i++) {
                    var name = x[i].innerHTML;
                    var style = document.createElement('input');
                    var styleLabel = document.createElement('label');
                    styleLabel.innerHTML = name;
                    styleLabel.setAttribute('for', name);
                    style.type = 'checkbox';
                    style.name = 'style[]';
                    style.value = name;
                    style.setAttribute('id', name);
                    document.querySelector('.formulaireStyle').append(style);
                    document.querySelector('.formulaireStyle').append(styleLabel);
                    document.querySelector('.formulaireStyle').append(document.createElement('br'));
                }
            }
        });
    }


    /**
    Bascule un style de la section available à la section current (pour la layer sélectionné)
    */
    function available( layer, style ) {
        $.ajax({
            url: "../model.php",
            type: "POST",
            data: {
                action: "addStyleToLayer",
                layer: layer,
                style: style
            },
            success: function(res) {
                if ( res == 1 ) {
                    document.location.reload();
                }
                else {
                    window.alert("Une erreur est survenue !");
                }
            }
        });
    }


    /**
    Bascule un style de la section current à la section available (pour la layer sélectionné)
    */
    function current( layer, style ) {
        $.ajax({
            url: "../model.php",
            type: "POST",
            data: {
                action: "delStyleToLayer",
                layer: layer,
                style: style.replace("<?php echo $_SESSION['login'] ?>:", "")
            },
            success: function(res) {
                if ( res == 1 ) {
                    document.location.reload();
                }
                else {
                    window.alert("Une erreur est survenue !");
                }
            }
        });
    }


    function privacySwitch( elem ) {
        if ( elem.value == 'private' ) {
            elem.value = 'public';
            $.ajax({
                url: "../model.php",
                type: "POST",
                data: {
                    action: "setPrivacy",
                    layer: elem.name,
                    public: true
                }
            });
        }
        else {
            elem.value = 'private';
            $.ajax({
                url: "../model.php",
                type: "POST",
                data: {
                    action: "setPrivacy",
                    layer: elem.name,
                    public: false
                }
            });
        }
    }

    layers();

    styles();

</script>

<?php if(isset($error)){
    echo "<p class='error'>".$error."</p>";
} ?>

<?php
    $content = ob_get_clean();
    include("layout.php");
?>
