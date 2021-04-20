<?php

namespace Models;

class Account extends Model
{
    protected $table = "account";

    // Réécriture nécessaire parce "id_user" dans la table account (nécessaire de renommer tous les "account" en "user" ou l'inverse pour factoriser correctement)
    public function find($var)
	{
        if (is_numeric($var)) {
            $query = "id_user";
        }
        else {
            $query = "username";
        }
		$result = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$query} = :var");
		$result->execute(['var' => $var]);
		$item = $result->fetch();
		return $item ;
	}

    public function testPassword(int $userId,string $password) : bool // Vérifie le mot de passe actuel
    {
        $result = $this->db->prepare('SELECT password FROM account WHERE id_user = :userId');
        $result->execute(array('userId' => $userId));
        $content = $result->fetch();      
        return password_verify($password,$content['password']);
    }

    public function updateUsername(string $newUsername,int $userId) : void // Change le nom d'utilisateur
    {
        $query = $this->db->prepare('UPDATE account SET username = :username WHERE id_user = :userId');
        $query->execute(['username' => $newUsername, 'userId' => $userId]);
    }

    public function updatePassword(int $userId,string $password) : void // changement de mot de passe
    {
        $query = $this->db->prepare('UPDATE account SET password = :pass WHERE id_user = :userId');
        $query->execute(['pass' => $password,'userId' => $userId]);
    }

    public function updatePhotoName(int $userId,string $fileName) : void // changement de photo de profil
    {
        $query = $this->db->prepare('UPDATE account SET photo = :filename_ WHERE id_user = :userId');
        $query->execute(['filename_' => $fileName,'userId' => $userId]);
    }

    public function registerUser(string $lastname,string $firstname,string $username,string $password,string $question,string $answer) : void// inscrit un utilisateur
    {
        $query = $this->db->prepare('INSERT INTO account(nom, prenom, username, password, question, reponse) VALUES(:nom, :prenom, :username, :password, :question, :answer)');
        $query->execute(array('nom' => $lastname, 'prenom' => $firstname, 'username' => $username, 'password' => $password, 'question' => $question, 'answer' => $answer));
    }
}


