<?php

namespace Models;

class Account extends Model
{
    protected $table = "account";

    // Réécriture nécessaire parce "id_user" dans la table account (nécessaire de renommer tous les "account" en "user" ou l'inverse pour factoriser correctement)
    public function find($var)
	{
        if (is_numeric($var)) {
            $q = "id_user";
        }
        else {
            $q = "username";
        }
		$result = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$q} = :var");
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

    public function updatePhotoName(int $userId,string $fileName) : void // changement de photo
    {
        $query = $this->db->prepare('UPDATE account SET photo = :filename_ WHERE id_user = :userId');
        $query->execute(['filename_' => $fileName,'userId' => $userId]);
    }

    public function registerUser($last_name,$first_name,$username,$password,$question,$answer) // inscrit un utilisateur
    {
        $password = password_hash($_POST['pass1'],PASSWORD_DEFAULT);
        $answer = password_hash($_POST['answer'],PASSWORD_DEFAULT);
        $query = $this->db->prepare('INSERT INTO account(nom, prenom, username, password, question, reponse) VALUES(:nom, :prenom, :username, :password, :question, :answer)');
        $work = $query->execute(array('nom' => $last_name, 'prenom' => $first_name, 'username' => $username, 'password' => $password, 'question' => $question, 'answer' => $answer));
        return $work;
    }



    public function getQuestion($username) // Récupère la question secrète de l'utilisateur actuel
    {
        $result = $this->db->prepare('SELECT question FROM account WHERE username = :username');
        $result->execute(array('username' => $username));
        $data = $result->fetch();
        $result->closeCursor();
        if(!$data) // ne devrait pas arriver
        {
            $question = '[...]';
        }
        else
        {
            $question = preg_replace("#(\?)#"," ",htmlspecialchars($data['question']));
            $question = 'Votre question secrète : ' . $question . ' ?';
        }
        return $question;
    }

    public function testReinitAns($username,$answer) // Teste la validité de la réponse à la question secrète
    {
         $result = $this->db->prepare('SELECT reponse FROM account WHERE username = :username');
        $result->execute(array('username' => $username));
        $data = $result->fetch();
        $result->closeCursor();	
        if(!$data) // ne devrait pas arriver
        {
            $test = false;
        }
        else
        {
            $user_answer = htmlspecialchars($data['reponse']);
            $test = password_verify($answer,$data['reponse']);
            echo 'réponse : ' . $answer . '<br/>';
            echo 'data[reponse] (hash) : ' . $data['reponse'] . '<br/>';
            echo 'user_answer (hash) : ' . $user_answer . '<br/>';

            if($test)
            {
                echo 'Oui <br/>' ;
            }
            else
            {
                echo 'Non <br/>' ;
            }
        }	
        return $test;
    }
}


