<?php
    ob_start();
?>

<section class="login">
    <h2>Se connecter</h2>
    <form method="POST" action="<?php echo $URI; ?>">
        <label> Login:
            <input name="login" type="text" placeholder="Votre login" required>
        </label>

        <label> Password:
            <input name="password" type="password" placeholder="Password" required>
        </label>
        
        <?php if(isset($error)){
            echo "<p class='error'>".$error."</p>";
        } ?>

        <input type="submit" value="Connecter">
    </form>
</section>

<?php
    $content = ob_get_clean();
    include('layout.php');
?>
