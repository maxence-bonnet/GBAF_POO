<?php

class Renderer
{
	public static function render(string $path, array $var = [])
	{
		extract($var);

		ob_start();

		require('view/' . $path . '.html.php');	
	
		$pageContent = ob_get_clean();

		require('view/template.html.php');
		
	}
}

function actorlist()
{
	$actors_info = listActors();
	require('view/accueilView.php');
}

function actorfull($actor_id,$username)
{
	$existingActor = existActor($actor_id);
	if(!$existingActor)
	{
		header('Location: index.php?action=accueil');
	}
	
	$actor = presentActor($actor_id);
	$actorname = actorname($actor_id);
	$existingUserComment = existUserComment($actor_id,$username);
	$like_number = countLikes($actor_id,'like');
	$dislike_number = countLikes($actor_id,'dislike');
	$like_list = listLikers($actor_id,'like');
	$dislike_list = listLikers($actor_id,'dislike');
	$like_state = checkLike($actor_id,$username);
	// affichage likes
	if(!$like_state)
	{
		$show = false;
	}
	elseif($like_state == 'like')
	{
		$show = 'Vous recommandez ce partenaire';
	}
	elseif($like_state == 'dislike')
	{
		$show = 'Vous déconseillez ce partenaire';	
	}
	// affichage des commentaires
	if(!existComment($actor_id)) // pas de commentaire posté pour cet acteur
	{
		$comments = false;
	}
	else // Il y a des commentaires
	{
		$comments = listComments($actor_id);
	}
	// affichage formulaire d'ajout de commentaire
	if(isset($_GET['add']) AND $_GET['add'] == 1) 
	{
		$showform = true;
	}
	else
	{
		$showform = false;
	}
	require('view/acteurView.php');
}