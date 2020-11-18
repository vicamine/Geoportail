<?php
    ob_start();
?>
<?php if ( isset($_SESSION['id'])){ ?>
    <p> <a href='/<?php echo $ROOT; ?>/index.php/addLayer' > Ajouter des layers ou des styles ! </a> </p>
<?php } ?>

<div id="layer"> </div>

<h2> Gestion des Layers </h2>

<div class='layers'>

    <form class='formulaire' action="<?php echo $URI; ?>" method="post">
        <input type="submit" name="supprimer" value="Supprimer">
        <br><br>
    </form>

</div>

<h2> Gestion des Styles </h2>

<div class="styles"> </div>

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
                            data.innerHTML += elem.getElementsByTagName('Name')[0].innerHTML + ' ';
                        }
                        document.querySelector('#layer').append(data);
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

    layers();
</script>

<?php if(isset($error)){
    echo "<p class='error'>".$error."</p>";
} ?>

<?php
    $content = ob_get_clean();
    include("layout.php");
?>
