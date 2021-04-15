<?php

namespace Models;

class Post extends Model
{
    protected $table = "post";

    public function existUserComment(int $actorId,int $userId) // vérifie l'existance d'un commentaire de l'utilisateur connecté pour un acteur 
    {
        $result = $this->db->prepare('SELECT * FROM post WHERE id_user = :userId AND id_actor = :actorId');
        $result->execute(array('userId' => $userId, 'actorId' => $actorId));
        $existingUserComment = $result->fetch();
        return $existingUserComment;
    }

    public function listComments(int $actorId) // Dresse la liste des commentaires et les infos utilisateurs liées pour un acteur donné
    {
        $results = $this->db->prepare('SELECT account.id_user, nom, prenom, photo, post.id_user, id_actor, date_add, post 
                                FROM post
                                INNER JOIN account
                                ON account.id_user = post.id_user
                                WHERE id_actor = :actorId
                                ORDER BY date_add');
        $results->execute(array('actorId' => $actorId));
        $comments = $results->fetchAll();
        return $comments;
    }

    public function addComment($userId,$actorId,$comment) // ajoute un commentaire
    {
        $query = $this->db->prepare('INSERT INTO post(id_user, id_actor, post) VALUES(:id_user, :id_actor, :comment)');
        $work = $query->execute(array('id_user' => $userId, 'id_actor' => $actorId, 'comment' => $comment));
        $query->closeCursor();
        return $work;
    }

    public function delComment($userId,$actorId) // supprime le commentaire existant
    {	
        $query = $this->db->prepare('DELETE FROM post WHERE id_user = :id_user AND id_actor = :id_actor');
        $work = $query->execute(array('id_user' => $userId, 'id_actor' => $actorId));
        return $work;
    }
}

