<?php

namespace Controllers;

class Vote
{
    
}

function likeManage($actor_id,$username,$like_request)
{
	$existingActor = existActor($actor_id);
	if(!$existingActor)
	{
		header('Location: index.php?action=accueil');
	}
	
	$user = getUserId($username);
	$user_id = $user;
	$like_state = checkLike($actor_id,$username);
	if(!$like_state)
	{
		if($like_request == 1)
		{
			$like_request = 'like';
			$work = addMention($actor_id,$user_id,$like_request);
		}
		elseif($like_request == 2)
		{
			$like_request = 'dislike';
			$work = addMention($actor_id,$user_id,$like_request);
		}

		if(!$work)
		{
			echo 'Erreur pendant l\'ajout';
		}		
	}
	else
	{
		if($like_state == 'like' AND $like_request == 2)
		{
			// Upate de like à dislike
			$work = updateMention($actor_id,$user_id,$like_request);
		}
		elseif($like_state == 'dislike' AND $like_request == 1)
		{
			// Update de dislike à like
			$work = updateMention($actor_id,$user_id,$like_request);
		}
		elseif($like_request == 3)
		{
			// Supprimer la mention like ou dislike
			$work = deleteMention($actor_id,$user_id);
		}

		if(!$work) 
		{
			echo ' /!\ Erreur pendant la mise à jour de la mention /!\ ';
		}		
	}
	header('Location: index.php?action=acteur&act=' . $actor_id);
}