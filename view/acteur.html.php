<div class ="content actor_content">
	<div class="actor_full">
    	<div class="actor_full_logo">
    		<img src="public/images/logos/<?= $actor['logo']; ?>" alt="logo <?= $actor['actor']; ?>">
    	</div>

		<div class="actor_full_description">
    		<h3><?= $actor['actor']; ?></h3>
    		<p><?= nl2br($actor['description']); ?></p>
    		<p>Vers le site de <a class="actor_external_link" href="#"><?= $actor['actor']?></a></p>			    		
    	</div>
    </div>

	<div class="actor_like_management">
		<?php
			if (!$existingUserComment) { // pas encore de commentaire de l'utilisateur pour cet acteur -> on propose l'ajout de commentaire
				?>
					<a href="index.php?controller=acteur&amp;task=acteur&amp;id=<?= $actorId ?>&amp;add=1#new_comment">Ajouter un commentaire public</a>
				<?php
			}
			else { // déjà commenté cet acteur -> Mention + lien pour supprimer le commentaire existant
				?>
					<div class="case_commented"><div class="case_commented_sub"><p>Vous avez commenté ce partenaire</p><p class="splitter"> | </p><a href="index.php?controller=post&amp;task=delComment&amp;id=<?= $actorId ?>">Supprimer mon commentaire</a></div></div>
				<?php
			}
		?>
		<div class="actor_like">
			<div class="actor_like_sub">
    			<a href="index.php?controller=vote&amp;task=likeManage&amp;id=<?= $actorId ?>&amp;vote=1" title="<?php 
    			if (!empty($likersList)) {
	    			foreach ($likersList as $name) {
	    				echo $name . '&#013;';
	    			}					    				
    			}	
    			?>">
    				<?= '(' . $likeNumber . ') ' ?>Je recommande <img src="public/images/logos/like.png" class="like_button" alt="like_button"/></a>
    			<p class="splitter"> | </p> 
    			<a href="index.php?controller=vote&amp;task=likeManage&amp;id=<?= $actorId ?>&amp;vote=2" title="<?php
    			if (!empty($dislikersList)) {
	    			foreach ($dislikersList as $name) {
	    				echo $name . '&#013;';
	    			}					    				
    			}			    			
    			?>">
    				<?= '(' . $dislikeNumber . ') ' ?>Je déconseille<img src="public/images/logos/dislike.png" class="dislike_button" alt="dislike_button"/></a>
			</div>
		</div>

		<?php 
			if($show) // On affiche si l'utilisateur aime ou non le partenaire avec possibilité de réinitialiser
			{ 
				?>
					<div class="actor_like_mention">
						<div class="actor_like_mention_sub">
							<p><?= $show ?></p>
							<p class="splitter"> |  </p>
							<a href="index.php?controller=vote&amp;task=likeManage&amp;id=<?= $actorId ?>&amp;vote=3">Réinitialiser</a>
						</div>
					</div>
				<?php
			}
		?>			    	
	</div>

	<div class="post_section">
		
	<h4>Commentaires :</h4>

		<?php 
		// if (isset($_SESSION['posted'])) {
		// 	    echo '<p style=color:red;>Votre commentaire a bien été ajouté.</p>';
		// 	    unset($_SESSION['posted']);
		// }
		// if (isset($_SESSION['deleted_post'])) {
		// 	    echo '<p style=color:red;>Votre commentaire a bien été supprimé.</p>';
		// 	    unset($_SESSION['deleted_post']);
		// }
		// if (isset($_SESSION['existing_comment'])) {
		// 	    echo '<p style=color:red;>Vous avez déjà commenté cet acteur, pour commenter à nouveau, supprimez votre précédent commentaire.</p>';
		// 	    unset($_SESSION['existing_comment']);
		// }
		if ($comments) {										
			foreach ($comments as $comment) {
				$com_nom = htmlspecialchars($comment['nom']);
				$com_prenom = htmlspecialchars($comment['prenom']);
				$com_date = preg_replace("#([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}:[0-9]{2}:[0-9]{2})#","Le $3/$2/$1",$comment['date_add']);
				$com_post = htmlspecialchars($comment['post']);
				$com_photo = htmlspecialchars($comment['photo']);
				?>
				<div class="post">
					<div class="post_photo"><img src="./public/images/uploads/<?= $com_photo ; ?>" alt="photo"/></div>
					<p class="user_post_ref"><?= $com_date; ?>, <?= $com_prenom; ?> <?= $com_nom; ?> a commenté :</p>
					<p><?= nl2br($com_post); ?></p>
				</div>
				<?php
			}
		}
		else
		{
			echo '<p> Pas encore de commentaire publié pour ce partenaire.</p>' ;							
		}
		?>
	</div>

	<?php
	if ($showForm) { ?>
		<form class="add_comment" action="index.php?controller=post&amp;task=addComment&amp;id=<?= $actorId ?>" method="post">
			<label for="new_comment">Votre commentaire : </label><textarea name="new_comment" id="new_comment"></textarea>
			<input type="submit" name="new_comment_submit" value="Publier"/>
		</form>
	<?php } ?>
</div>
