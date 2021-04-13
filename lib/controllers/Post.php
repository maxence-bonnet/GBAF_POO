<?php

namespace Controllers;

class Post extends Controller
{
    protected $modelName = \Models\Post::class;
}

function addComment($actor_id,$username,$comment)
{
	$existingActor = existActor($actor_id);
	if(!$existingActor)
	{
		header('Location: index.php?action=accueil');
	}

	$user = getUserId($username);
	$user_id = $user;
	if(existUserComment($actor_id,$username)) // L'utilisateur a déjà commenté
	{
		$_SESSION['existing_comment'] = true;
		header('Location: index.php?action=acteur&act=' . $actor_id);
	}
	else // écriture
	{
		$work = newComment($user_id,$actor_id,$comment);
		if(!$work) // erreur pendant l'écriture
		{
			echo 'Erreur dans l\'ajout du commentaire';
		}
		else // bon déroulement
		{
			$_SESSION['posted'] = true;
			header('Location: index.php?action=acteur&act=' . $actor_id);
		}		
	}
}


function delComment($actor_id,$username)
{
	$existingActor = existActor($actor_id);
	if(!$existingActor)
	{
		header('Location: index.php?action=accueil');
	}
	
	$user = getUserId($username);
	$user_id = $user;
	if(existUserComment($actor_id,$username)) // L'utilisateur a déjà commenté
	{
		$work = deleteComment($user_id,$actor_id);
		if(!$work) // erreur pendant l'écriture
		{
			echo 'Erreur dans la suppression du commentaire';
		}
		else // bon déroulement
		{
			session_start();
			$_SESSION['deleted_post'] = true;
			header('Location: index.php?action=acteur&act=' . $actor_id);
		}	
	}
	else // Pas de commentaire utilisateur existant -> retour (ne devrait pas arriver)
	{
		header('Location: index.php?action=acteur&act=' . $actor_id);
	}	
}