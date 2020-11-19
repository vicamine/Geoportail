<?php
    ob_start();
?>
<?php if ( isset($_SESSION['id'])){ ?>
    <p> <a href='/<?php echo $ROOT; ?>/index.php/addLayer' > Ajouter des layers ou des styles ! </a> </p>
<?php } ?>

<div id="layer">
    <?php if ( $action == 'Layer' ) { ?>
        <h2> Détails </h2>
        <div id="details"> </div>

        <h3> Avaible styles </h3>
        <div id="availableStyle">
            <ul> </ul>
        </div>

        <h3> Current styles </h3>
        <div id="currentStyle">
            <ul> </ul>
        </div>
    <?php } ?>

</div>

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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script type="text/javascript">

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
                            data.innerHTML += name.substr(name.indexOf(':')+1) + ' | ' +
                            x[i].getElementsByTagName('SRS')[0].innerHTML + ' | ';
                        } else if (x[i].getElementsByTagName('CRS').length > 0){
                            data.innerHTML += name.substr(name.indexOf(':')+1) + ' | ' +
                            x[i].getElementsByTagName('CRS')[0].innerHTML + ' | ';
                        }
                        var styles = x[i].getElementsByTagName('Style');
                        for ( elem of styles) {
                            style = elem.getElementsByTagName('Name')[0].innerHTML;
                            data.innerHTML += style + '  ';
                        }
                        document.querySelector('#details').append( data );
                    }
                    <?php } ?>


                    var layer = document.createElement('input');
                    var layerLabel = document.createElement('a');
                    layerLabel.innerHTML = name.substr(name.indexOf(':')+1);
                    href = '/<?php echo $ROOT; ?>/index.php/user?action=Layer&data='+x[i].getElementsByTagName('Name')[0].innerHTML;
                    layerLabel.setAttribute('href', href);
                    layer.type = 'checkbox';
                    layer.name = 'layer[]';
                    layer.value = x[i].getElementsByTagName('Name')[0].innerHTML;
                    layer.setAttribute('id', x[i].getElementsByTagName('Name')[0].innerHTML);
                    document.querySelector('.formulaire').append(layer);
                    document.querySelector('.formulaire').append(layerLabel);
                    document.querySelector('.formulaire').append(document.createElement('br'));
                }
            }
        });
    }

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
