<!DOCTYPE html>
<html>
    <head>
    	<title><?= $pageTitle ?></title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width" />
        <link href="./public/css/style.css" rel="stylesheet" />
        <link rel="icon" type="image/png" href="./public/images/logos/gbaf_ico.png" />
    </head>       
    <body>
    	<!-- HEADER -->

    	<div class="header_content">
			<div class="logo_gbaf">
				<a href="index.php"><img src="./public/images/logos/gbaf.png" title="GBAF"alt="GBAF logo"/></a>
			</div>
			<?php
				if (isset($_SESSION['connected'])) { 
					?>
					<div class="user_ref">
						<div class="user_photo">
							<a href="index.php?controller=account&amp;task=profil"><img src="public/images/uploads/<?= $accountInfo['photo'] ?>" alt="Ma photo de profil" title="Voir mon profil"/></a>
						</div>
						<div class="user_name">
							<a href="index.php?controller=account&amp;task=profil" title="Voir mon profil"><p><?= $accountInfo['prenom'] . ' ' . $accountInfo['nom'] ?></p></a>
						</div>

						<form class="deconnection_form" action="index.php?controller=account&amp;task=deconnexion" method="post"><input type="submit" value="deconnexion"/></form>				
					</div>
					<?php
				}
				else // pas de session
				{
					?>
					<div class="inscription_link">
						<a href="index.php?controller=account&task=inscription">S'inscrire</a><p>/</p><a href="index.php?controller=account&task=connexion">Se connecter</a>
					</div>
					<?php
				}
			?>
		</div>

		<!-- CONTENT -->

        <?= $pageContent ?>


        <!-- FOOTER -->

        <div class="footer_content">
        	<p><a href="index.php?controller=mentions&task=mentions"> Mentions légales </a> | <a href="index.php?controller=contact&task=contact">Contact</a></p>
        </div>

    </body>
</html>