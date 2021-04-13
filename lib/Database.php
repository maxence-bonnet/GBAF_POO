<?php

class Database
{
	public static function dbConnect() // connexion à la base de données
	{
		try
		{
			$db = new PDO('mysql:host=localhost;dbname=gbaf;charset=utf8', 'root', '');
		}
		catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		}
		return $db;
	}
}

