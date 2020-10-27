<?php
    ob_start();
?>

<section class="register">
    <h2> S'enregistrer </h2>
    <form method="POST" action="<?php echo $URI; ?>">

        <label>Nom:
            <input name="nom" type="text" placeholder="Votre nom" required>
        </label>
        <br>
        <label>Prénom:
            <input name="prenom" type="text" placeholder="Votre prénom" required>
        </label>
        <br>
        <label> Login:
            <input name="login" type="text" placeholder="Votre login" required>
        </label>
        <br>
        <label> Password:
            <input name="password" type="password" placeholder="Password" required>
        </label>

        <?php if(isset($error)){
            echo "<p class='error'>".$error."</p>";
        } ?>

        <input type="submit" value="Confirmer">
    </form>
</section>

<?php
    $content = ob_get_clean();
    include('layout.php');
?>
