<?php

namespace Models;

class Vote extends Model
{
    protected $table = "vote";

    public function checkVote($actorId,$userId) // vérifie si l'utilisateur actuel a déjà ajouté une mention ("je recommande / déconseille")
    {
        $result = $this->db->prepare('SELECT * FROM vote WHERE id_user = :userId AND id_actor = :actorId');
        $result->execute(array('userId' => $userId, 'actorId' => $actorId));
        $data = $result->fetch();
        if (!$data) {
            $voteCurrent = false;
        }
        else {
            $voteCurrent = $data['vote'];
        }
        return $voteCurrent;
    }

    public function countLikes($actorId,$like_state) // Compteur de mention "je recommande" / "Je déconseille" (en fonction de $likestate)
    {
         $result = $this->db->prepare('SELECT COUNT(*) AS like_number FROM vote WHERE id_actor = :actor AND vote = :like_');
        $result->execute(array('actor' => $actorId, 'like_' => $like_state));
        $data = $result->fetch();
        $result->closeCursor();
        $like_number = $data['like_number'];
        if(!$data)
        {
            $like_number = 0;
        }
        return $like_number;
    }

    public function listLikers($actorId,$like_state) // Dresse la liste des utilisateurs qui recommandent ou déconseillent (en fonction de $likestate) l'acteur donné
    {
          $result = $this->db->prepare('SELECT account.id_user, nom, prenom, vote.id_user, id_actor, vote 
                                FROM vote
                                INNER JOIN account
                                ON account.id_user = vote.id_user
                                WHERE id_actor = :actor
                                AND vote = :like_');
        $work = $result->execute(array('actor' => $actorId, 'like_' => $like_state));
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

    public function addVote($actorId,$userId,$voteRequest) // Ajoute la mention (en fonction de $voteRequest)
    {
        $query = $this->db->prepare('INSERT INTO vote(id_user, id_actor, vote) VALUES(:id_user, :actorId, :vote)');
        $query->execute(array('id_user' => $userId, 'actorId' => $actorId, 'vote' => $voteRequest));
    }

    public function updateVote($actorId,$userId,$voteRequest) // Met à jour la mention (en fonction de $voteRequest)
    {
        $query = $this->db->prepare('UPDATE vote SET vote = :vote WHERE id_user = :id_user AND id_actor = :actorId');
        $query->execute(array('id_user' => $userId, 'actorId' => $actorId, 'vote' => $voteRequest));
    }

    public function deleteVote($actorId,$userId) // Supprime la mention
    {
        $query = $this->db->prepare('DELETE FROM vote WHERE id_user = :id_user AND id_actor = :actorId');
        $query->execute(array('id_user' => $userId, 'actorId' => $actorId));
    }
}

