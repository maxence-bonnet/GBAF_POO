<?php 
if ($step == 1) {
?>

<div class="content reinit_content">
    <form class="connection_form" action="index.php?controller=account&amp;task=reinit&amp;step=2" method="post">
        <fieldset>
        <legend class="long_legend">Réinitialiser le mot de passe :</legend>

        <label for="username">Veuillez saisir votre nom d'utilisateur :</label><input type="text" name="username" id="username"/>

        <?php
        if (isset($_SESSION['invalid_user'])) {
            echo '<p style=color:red;text-align:center;>L\'identifiant saisi n\'existe pas.</p>';
            unset($_SESSION['invalid_user']);
        }
        ?>

        <input type="submit" name="submit" value="Valider">					
        </fieldset>			
    </form>
</div>
<?php
}
elseif ($step == 2) {
?>
<div class="content reinit_content">
    <form class="connection_form" action="index.php?controller=account&amp;task=reinit&amp;step=3" method="post">
        <fieldset>
            <legend class="long_legend">Réinitialiser le mot de passe :</legend>

                <?php
                if (isset($_SESSION['missing_field'])) {
                    echo '<p style=color:red;text-align:center;>Certains champs n\'ont pas été remplis';
                    unset($_SESSION['missing_field']);
                }		
                elseif (isset($_SESSION['invalid_answer'])) {
                    echo '<p style=color:red;text-align:center;>Mauvaise Réponse</p>';
                    unset($_SESSION['invalid_answer']);
                }
                elseif (isset($_SESSION['invalid_pass_format'])) {
                    echo '<p style=color:red;text-align:center;>Bonne réponse mais le format des mots de passe de convient pas, veuillez recommencer.</p>';
                    unset($_SESSION['invalid_pass_format']);
                }
                elseif (isset($_SESSION['pass_not_matching'] )) {
                    echo '<p style=color:red;text-align:center;>les deux mots de passe ne correspondent pas, veuillez recommencer.</p>';
                    unset($_SESSION['pass_not_matching']);
                }
                ?>

                <label for="answer"><?= $question ?></label><input type="text" name="answer" id="answer" required/>

                <label for="pass1">Nouveau mot de passe <span class="lower_italic">(8 caractères, une majuscule, un chiffre et un caractère spécial au minimum)</span> :</label><input type="password" name="pass1" id="pass1" required/>

                <label for="pass2">Confirmation du mot de passe :</label><input type="password" name="pass2" id="pass2" required/>

                <input type="submit" name="submit" value="Changer le mot de passe">
        </fieldset>			
    </form>
</div>
<?php } ?>

