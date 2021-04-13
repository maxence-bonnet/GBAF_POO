<?php

namespace Models;

class Vote extends Models
{
    
}

function checkLike($actor_id,$username) // vérifie si l'utilisateur actuel a déjà ajouté une mention ("je recommande / déconseille")
{
	$db = dbConnect();
	$result = $db->prepare('SELECT account.id_user, username, vote.id_user, id_actor, vote 
							FROM account
							INNER JOIN vote
							ON account.id_user = vote.id_user
							WHERE username = :username
							AND id_actor = :actor');
	$result->execute(array('username' => $username, 'actor' => $actor_id));
	$data = $result->fetch();
	$result->closeCursor();
	if(!$data)
	{
		$like_state = false;
	}
	else
	{
		$like_state = $data['vote'];
	}
	return $like_state;
}

function countLikes($actor_id,$like_state) // Compteur de mention "je recommande" / "Je déconseille" (en fonction de $likestate)
{
	$db = dbConnect();
	$result = $db->prepare('SELECT COUNT(*) AS like_number FROM vote WHERE id_actor = :actor AND vote = :like_');
	$result->execute(array('actor' => $actor_id, 'like_' => $like_state));
	$data = $result->fetch();
	$result->closeCursor();
	$like_number = $data['like_number'];
	if(!$data)
	{
		$like_number = 0;
	}
	return $like_number;
}

function listLikers($actor_id,$like_state) // Dresse la liste des utilisateurs qui recommandent ou déconseillent (en fonction de $likestate) l'acteur donné
{
	$db = dbConnect();
	$result = $db->prepare('SELECT account.id_user, nom, prenom, vote.id_user, id_actor, vote 
							FROM vote
							INNER JOIN account
							ON account.id_user = vote.id_user
							WHERE id_actor = :actor
							AND vote = :like_');
	$work = $result->execute(array('actor' => $actor_id, 'like_' => $like_state));
	if(!$work)
	{
		$like_list[] = '';
	}
	else
	{
		$like_list[] = '';
		while($data = $result->fetch())
		{
			$like_list[] = $data['nom'] . ' ' . $data['prenom'] ;
		}	
	}																							
	$result->closeCursor();
	return $like_list;
}

function addMention($actor_id,$user_id,$like_request) // Ajoute la mention (en fonction de $likestate)
{
	$db = dbConnect();
	$query = $db->prepare('INSERT INTO vote(id_user, id_actor, vote) VALUES(:id_user, :actor, :vote)');
	$work = $query->execute(array('id_user' => $user_id, 'actor' => $actor_id, 'vote' => $like_request));
	$query->closeCursor();
	return $work;
}

function updateMention($actor_id,$user_id,$like_request) // Met à jour la mention (en fonction de $likestate)
{
	$db = dbConnect();
	$query = $db->prepare('UPDATE vote SET vote = :vote WHERE id_user = :id_user AND id_actor = :actor');
	$work = $query->execute(array('vote' => $like_request, 'id_user' => $user_id, 'actor' => $actor_id));
	$query->closeCursor();
	return $work;	
}

function deleteMention($actor_id,$user_id) // Supprime la mention
{
	$db = dbConnect();
	$query = $db->prepare('DELETE FROM vote WHERE id_user = :id_user AND id_actor = :actor');
	$work = $query->execute(array('id_user' => $user_id, 'actor' => $actor_id));
	$query->closeCursor();	
	return $work;
}