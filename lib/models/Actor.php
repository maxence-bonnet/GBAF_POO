<?php

namespace Models;

class Actor extends Models
{
    
}


function listActors() // récupère toutes les informations de tous les acteurs
{
	$db = dbConnect();
	$actors_info = $db->query('SELECT * FROM actor');
	return $actors_info;
}

function existActor($actor_id) // vérifie l'existance de l'acteur
{
	$db = dbConnect();
	$result = $db->prepare('SELECT id_actor FROM actor WHERE id_actor = :actor');
	$result->execute(array(':actor' => $actor_id));
	$existingActor = $result->fetch();
	return $existingActor;
}

function actorname($actor_id) // récupère le nom de l'acteur en fonction de son id
{
	$db = dbConnect();
	$result = $db->prepare('SELECT actor FROM actor WHERE id_actor = :actor');
	$result->execute(array(':actor' => $actor_id));
	$actorname = $result->fetch();
	$actorname = $actorname['actor'];
	return $actorname;
}

function presentActor($actor_id) //récupère toutes les informations d'un acteur donné
{
	$db = dbConnect();
	$result = $db->prepare('SELECT * FROM actor WHERE id_actor = :actor');
	$result->execute(array('actor' => $actor_id));
	$actor = $result->fetch();
	$result->closeCursor();
	return $actor;
}