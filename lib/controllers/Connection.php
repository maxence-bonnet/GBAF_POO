<?php

namespace Controllers;

class Connection extends Controller
{
	protected $modelName = \Models\Connection::class;

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


