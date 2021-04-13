<?php

namespace Models;

class Post extends Models
{
    
}

function existUserComment($actor_id,$username) // vérifie l'existance d'un commentaire de l'utilisateur connecté pour un acteur 
{
	$db = dbConnect();
	$result = $db->prepare('SELECT account.id_user, username, post.id_user, id_actor
							FROM account
							INNER JOIN post
							ON account.id_user = post.id_user
							WHERE username = :username
							AND id_actor = :actor');
	$result->execute(array('username' => $username, 'actor' => $actor_id));
	$existingUserComment = $result->fetch();
	$result->closeCursor();
	return $existingUserComment;
}

function existComment($actor_id) // vérifie l'existance d'au moins un commentaire pour l'acteur donné
{
	$db = dbConnect();
	$result = $db->prepare('SELECT id_actor FROM post WHERE id_actor = :actor');
	$result->execute(array('actor' => $actor_id));
	$data = $result->fetch();
	$result->closeCursor();	
	if(!$data)
	{
		return false;
	}
	else
	{
		return true;
	}
}

function listComments($actor_id) // Dresse la liste des commentaires et leurs infos utilisateurs pour un acteur donné
{
	$db = dbConnect();
	$comments = $db->prepare('SELECT account.id_user, nom, prenom, photo, post.id_user, id_actor, date_add, post 
							FROM post
							INNER JOIN account
							ON account.id_user = post.id_user
							WHERE id_actor = :actor
							ORDER BY date_add');
	$comments->execute(array('actor' => $actor_id));
	return $comments;
}

function newComment($user_id,$actor_id,$comment) // ajoute un commentaire
{
	$db = dbConnect();
	$query = $db->prepare('INSERT INTO post(id_user, id_actor, post) VALUES(:id_user, :id_actor, :comment)');
	$work = $query->execute(array('id_user' => $user_id, 'id_actor' => $actor_id, 'comment' => $comment));
	$query->closeCursor();
	return $work;
}

function deleteComment($user_id,$actor_id) // supprime le commentaire existant
{	
	$db = dbConnect();
	$query = $db->prepare('DELETE FROM post WHERE id_user = :id_user AND id_actor = :id_actor');
	$work = $query->execute(array('id_user' => $user_id, 'id_actor' => $actor_id));
	$query->closeCursor();
	return $work;
}