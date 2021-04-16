<?php

namespace Models;

class Account extends Model
{
    protected $table = "account";

    // Réécriture nécessaire parce "id_user" dans la table account (nécessaire de renommer tous les "account" en "user" ou l'inverse pour factoriser correctement)
    public function find(int $id)
	{
		$result = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_user = :id");
		$result->execute(['id' => $id]);
		$item = $result->fetch();
		return $item ;
	}

    public function registerUser($last_name,$first_name,$username,$password,$question,$answer) // inscrit un utilisateur
    {
        $password = password_hash($_POST['pass1'],PASSWORD_DEFAULT);
        $answer = password_hash($_POST['answer'],PASSWORD_DEFAULT);
        $query = $this->db->prepare('INSERT INTO account(nom, prenom, username, password, question, reponse) VALUES(:nom, :prenom, :username, :password, :question, :answer)');
        $work = $query->execute(array('nom' => $last_name, 'prenom' => $first_name, 'username' => $username, 'password' => $password, 'question' => $question, 'answer' => $answer));
        return $work;
    }

    public function getUserId($username) // récupère l'identifiant utilisateur via username
    {
        $result = $this->db->prepare('SELECT id_user FROM account WHERE username = :username');
        $result->execute(array('username' => $username));
        $data = $result->fetch();
        $user = $data['id_user'];
        return $user;
    }

    public function existUsername($username) // vérifie l'existance d'un nom d'utilisateur
    {
        $result = $this->db->prepare('SELECT username FROM account WHERE username = :username');
        $result->execute(array('username' => $username));
        $existing = $result->fetch();
        return $existing;
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

    public function reinitPass($username,$pass1) // changement de mot de passe
    {
        $pass = password_hash($pass1, PASSWORD_DEFAULT);
        $query = $this->db->prepare('UPDATE account SET password = :pass WHERE username = :username');
        $work = $query->execute(array('pass' => $pass,'username' => $username));
        $query->closeCursor();
        return $work;
    }

    public function testPassword($username,$password) // Vérifie le mot de passe actuel
    {
        $username = htmlspecialchars($username);
        $password = htmlspecialchars($password);
        $result = $this->db->prepare('SELECT username, password FROM account WHERE username = :username');
        $result->execute(array('username' => $username));
        $content = $result->fetch();
        if($content)
        {
            $actual_password = htmlspecialchars($content['password']);
            $testpass = password_verify($password,$actual_password);
        }
        else // ne devrait pas arriver
        {
            $testpass = false;
        }
        return $testpass;
    }

    public function updateUsername($new_username) // Change le nom d'utilisateur
    {
        $user = getUserId($_SESSION['username']);
        $existing = existUsername($new_username);
        if($existing)
        {
            $work = false ;
        }
        else
        {
            $query = $this->db->prepare('UPDATE account SET username = :username WHERE id_user = :user');
            $work = $query->execute(array('username' => $new_username, 'user' => $user));
        }
        return $work;
    }

    public function updateUserAccount($username,$filename) // Met à jour le lien de l'image de l'utilisateur
    {
        $query = $this->db->prepare('UPDATE account SET photo = :filename WHERE username = :username');
        $work = $query->execute(array(':filename' => $filename,'username' => $username));
        if(!$work)
        {
            $work = false;
        }
        else
        {
            $work = true;
        }
        return $work;
    }
}


