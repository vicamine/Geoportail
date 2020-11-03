<?php
    ob_start();
?>
<?php if ( isset($_SESSION['id'])){ ?>
    <p> <a href='/<?php echo $ROOT; ?>/index.php/addLayer' > Ajouter des layers ! </a> </p>
<?php } ?>

<h2> Gestion des Layers </h2>

<div class='layers'>

    <form class='formulaire' action="<?php echo $URI; ?>" method="post">
        <input type="submit" name="supprimer" value="Supprimer">
        <br><br>
    </form>

</div>

<script type="text/javascript">
    function layers() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Typical action to be performed when the document is ready:
                //document.getElementById("contenue").innerHTML = xhttp.responseText;
                var xmlData = xhttp.responseXML;
                var x = xmlData.getElementsByTagName("Layer")[0].getElementsByTagName("Layer");
                for (i = 0; i < x.length; i++) {

                    if ( x[i].getElementsByTagName('Name')[0].innerHTML.indexOf('<?php echo $_SESSION['login'] ?>:') == -1 ) {
                        continue;
                    }

                    var layer = document.createElement('input');
                    var layerLabel = document.createElement('label');
                    layerLabel.setAttribute('for', x[i].getElementsByTagName('Name')[0].innerHTML);
                    layerLabel.innerHTML = x[i].getElementsByTagName('Name')[0].innerHTML;
                    layer.type = 'checkbox';
                    layer.name = 'layer[]';
                    layer.value = x[i].getElementsByTagName('Name')[0].innerHTML;
                    layer.setAttribute('id', x[i].getElementsByTagName('Name')[0].innerHTML);
                    document.querySelector('.formulaire').append(layer);
                    document.querySelector('.formulaire').append(layerLabel);
                    document.querySelector('.formulaire').append(document.createElement('br'));
                }
            }
        };
        var request = "http://localhost:8080/geoserver//wms?service=wms&version=1.1.1&request=GetCapabilities"
        xhttp.open("GET", request, true);
        xhttp.overrideMimeType('text/xml');
        xhttp.send();
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
