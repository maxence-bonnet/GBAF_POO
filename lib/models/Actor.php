<?php

namespace Models;

class Actor extends Model
{
    protected $table = "actor";

    // public function listActors() // récupère toutes les informations de tous les acteurs
    // {
    //     $actors_info = $this->db->query('SELECT * FROM actor');
    //     return $actors_info;
    // }

    public function existActor($actor_id) // vérifie l'existance de l'acteur
    {
        $result = $this->db->prepare('SELECT id_actor FROM actor WHERE id_actor = :actor');
        $result->execute(array(':actor' => $actor_id));
        $existingActor = $result->fetch();
        return $existingActor;
    }

    public function actorname($actor_id) // récupère le nom de l'acteur en fonction de son id
    {
        $result = $this->db->prepare('SELECT actor FROM actor WHERE id_actor = :actor');
        $result->execute(array(':actor' => $actor_id));
        $actorname = $result->fetch();
        $actorname = $actorname['actor'];
        return $actorname;
    }

    public function presentActor($actor_id) //récupère toutes les informations d'un acteur donné
    {
        $result = $this->db->prepare('SELECT * FROM actor WHERE id_actor = :actor');
        $result->execute(array('actor' => $actor_id));
        $actor = $result->fetch();
        $result->closeCursor();
        return $actor;
    }
}