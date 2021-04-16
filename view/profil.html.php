<?php
	$firstName = $accountInfo['prenom'];
	$lastName = $accountInfo['nom'];
	$photo = $accountInfo['photo'];
	$username = $accountInfo['username'];
?>

<div class ="content profile_content">
	<form enctype="multipart/form-data" class="profile_form" action="index.php?action=profil&amp;update=1" method="post">
		<fieldset>
			<legend>Mon profil</legend>
			<div class="actual_profile">
				<div class="actual_profile_part1">								
					<p>Mon nom (fixe) : <?= $lastName ?></p>
					<p>Mon prénom (fixe) : <?= $firstName ?></p>
					<p>Mon identifiant : <?= $username ?></p>
				</div>
				<div class="actual_profile_part2">								
					<div class="photo">
						<p>Ma photo de profil : </p>
						<img src="public/images/uploads/<?= $photo ?>" alt="Ma photo de profil"/>
					</div>
				</div>
			</div>
			<HR size="3px" width="80%" color="black">
				<div class="update_profile">
					<div class="update_profile_part1">
						<label for="username">Modifier mon identifiant : </label><input type="text" name="username" id="username"/>
							<?php
							if(isset($_SESSION['exist']))
							{
								    echo '<p style=color:red;>Ce nom d\'utilisateur existe déjà, veuillez en saisir un autre.</p>';
								    unset($_SESSION['exist']);
							}
							if(isset($_SESSION['usernamechanged']))
							{
								    echo '<p style=color:red;>Le nom d\'utilisateur a bien été modifié.</p>';
								    unset($_SESSION['usernamechanged']);
							}
							?>
						<h5>Modifier mon mot de passe</h5>
							<?php
							if(isset($_SESSION['passchanged']))
							{
								    echo '<p style=color:red;>La modification du mot de passe a bien été prise en compte.</p>';
								    unset($_SESSION['passchanged']);
							}
							if(isset($_SESSION['unknown']))
							{
								    echo '<p style=color:red;>Erreur inconnue.</p>';
								    unset($_SESSION['unknown']);
							}							
							?>
						<label for="actual_pass">Mon mot de passe actuel : </label><input type="password" name="actual_pass" id="actual_pass">
							<?php
							if(isset($_SESSION['wrongpass']))
							{
								    echo '<p style=color:red;>Le mot de passe saisi est invalide.</p>';
								    unset($_SESSION['wrongpass']);
							}
							?>
						<label for="pass1">Mon nouveau mot de passe : </label><input type="password" name="pass1" id="pass1">
							<?php
							if(isset($_SESSION['invalidpass']))
							{
								    echo '<p style=color:red;>Le mot de passe saisi ne convient pas au format demandé ou ne correspondent pas.</p>';
								    unset($_SESSION['invalidpass']);
							}
							?>
						<label for="pass2">Confirmation du nouveau mot de passe :</label><input type="password" name="pass2" id="pass2">									
					</div>
					<div class="update_profile_part2">
						<input type="hidden" name="MAX_FILE_SIZE" value="2000000"/>
						<label for="photo">Choisir une photo de profil : </label><input type="file" name="photo" id="photo"/>
							<?php
							if(isset($_SESSION['invalid_file']))
							{
								    echo '<p style=color:red;>Le fichier envoyé est invalide, ou supérieur à 2Mo.</p>';
								    unset($_SESSION['invalid_file']);
							}					
							?>	
					</div>							
				</div>
			<input type="submit" name="update_profile_submit" value="Modifier ces paramètres">
		</fieldset>
	</form>
</div>

<?php



?>