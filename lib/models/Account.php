<?php

namespace Models;

class Account extends Models
{
    
}

function testConnectionRequest($username,$password) // traite une demande de connexion
{
	$db = dbConnect();
	$username = htmlspecialchars($username);
	$password = htmlspecialchars($password);
	$result = $db->prepare('SELECT username, password, prenom, nom, photo FROM account WHERE username = :username');
	$result->execute(array('username' => $username));
	$content = $result->fetch();
	if($content)
	{
		$testpass = password_verify($password,$content['password']);
		if($testpass)
		{
			$connection = true;
			$_SESSION['username'] = $username;
			$_SESSION['prenom'] = htmlspecialchars($content['prenom']);
			$_SESSION['nom'] = htmlspecialchars($content['nom']);
			$_SESSION['photo'] = htmlspecialchars($content['photo']);
		}
		else
		{
			$connection = false;
		}
	}
	else
	{
		$connection = false;
	}
	return $connection;
}

function testRegistration($last_name,$first_name,$username,$pass1,$pass2,$question,$answer)
{
	$existing = existUsername($username);
	
	if($existing)
	{
		$error[] = 'exist';
	}
		if(strlen($username) < 3)
	{
		// username trop court
		$error[] = 'short';
	}
	if(!preg_match("#(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\d)(?=.*[^A-Za-z\d])#",$pass1) OR strlen($pass1) < 8)
	{
		// format mot de passe invalide
		$error[] = 'invalidpass';
	}
	if($pass1 != $pass2)
	{
		// les mots de passe ne correspondent pas
		$error[] = 'passnotmatching';
	}

	if(isset($error))
	{
		foreach($error as $value => $key)
		{
			$_SESSION[$key] = 1;
		}
		$work = false;
	}
	else
	{
		$work = true;
	}
	return $work;
}

function registerUser($last_name,$first_name,$username,$password,$question,$answer) // inscrit un utilisateur
{
	$db = dbConnect();
	$password = password_hash($_POST['pass1'],PASSWORD_DEFAULT);
	$answer = password_hash($_POST['answer'],PASSWORD_DEFAULT);
	$query = $db->prepare('INSERT INTO account(nom, prenom, username, password, question, reponse) VALUES(:nom, :prenom, :username, :password, :question, :answer)');
	$work = $query->execute(array('nom' => $last_name, 'prenom' => $first_name, 'username' => $username, 'password' => $password, 'question' => $question, 'answer' => $answer));
	return $work;
}

function getUserId($username) // récupère l'identifiant utilisateur via username
{
	$db = dbConnect();
	$result = $db->prepare('SELECT id_user FROM account WHERE username = :username');
	$result->execute(array('username' => $username));
	$data = $result->fetch();
	$user = $data['id_user'];
	return $user;
}

function existUsername($username) // vérifie l'existance d'un nom d'utilisateur
{
	$db = dbConnect();
	$result = $db->prepare('SELECT username FROM account WHERE username = :username');
	$result->execute(array('username' => $username));
	$existing = $result->fetch();
	return $existing;
}

function getQuestion($username) // Récupère la question secrète de l'utilisateur actuel
{
	$db = dbConnect();
	$result = $db->prepare('SELECT question FROM account WHERE username = :username');
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

function testReinitAns($username,$answer) // Teste la validité de la réponse à la question secrète
{
	$db = dbConnect();
	$result = $db->prepare('SELECT reponse FROM account WHERE username = :username');
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

function reinitPass($username,$pass1) // changement de mot de passe
{
	$db = dbConnect();
	$pass = password_hash($pass1, PASSWORD_DEFAULT);
	$query = $db->prepare('UPDATE account SET password = :pass WHERE username = :username');
	$work = $query->execute(array('pass' => $pass,'username' => $username));
	$query->closeCursor();
	return $work;
}

function testPassword($username,$password) // Vérifie le mot de passe actuel
{
	$db = dbConnect();
	$username = htmlspecialchars($username);
	$password = htmlspecialchars($password);
	$result = $db->prepare('SELECT username, password FROM account WHERE username = :username');
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

function updateUsername($new_username) // Change le nom d'utilisateur
{
	$user = getUserId($_SESSION['username']);
	$existing = existUsername($new_username);
	if($existing)
	{
		$work = false ;
	}
	else
	{
		$db = dbConnect();
		$query = $db->prepare('UPDATE account SET username = :username WHERE id_user = :user');
		$work = $query->execute(array('username' => $new_username, 'user' => $user));
		$query->closeCursor();
	}
	return $work;
}

function updateUserAccount($username,$filename) // Met à jour le lien de l'image de l'utilisateur
{
	$db = dbConnect();
	$query = $db->prepare('UPDATE account SET photo = :filename WHERE username = :username');
	$work = $query->execute(array(':filename' => $filename,'username' => $username));
	$query->closeCursor();
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
