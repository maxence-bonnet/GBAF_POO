<?php

namespace Controllers;

class Connection
{

}

function connectionRequest()
{
	if(isset($_POST['username']) AND isset($_POST['password']) AND !empty($_POST['username']) AND !empty($_POST['password']))
	{
		$username = htmlspecialchars($_POST['username']);
		$password = htmlspecialchars($_POST['password']);
		$connection = testConnectionRequest($username,$password);
		if($connection)
		{
			header('Location: index.php?action=accueil');
		}
		else
		{
			$_SESSION['wrong'] = 1;
			require('view/connexionView.php');
		}
	}
	else
	{
		$_SESSION['missing_field'] = 1 ;
		require('view/connexionView.php');
	}
}

function deconnection() // deconnexion
{
	session_destroy();
	header('Location: index.php?action=accueil');	
}

function isconnected() // vérifie si une connexion est active
{
	if(isset($_SESSION['username']) AND !empty($_SESSION['username']))
	{
		return true;
	}
	else
	{
		return false;
	}
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
