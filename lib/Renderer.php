<?php

class Renderer
{
	public static function render(string $path, array $var = [])
	{
		extract($var);

		ob_start();

		require('view/' . $path . '.html.php');	
	
		$pageContent = ob_get_clean();

		require('view/template.html.php');
		
	}

	public static function connectionPage()
	{
		ob_start();

		require('view/connexion.html.php');

		$pageContent = ob_get_clean();

		$pageTitle = 'Connexion';

		require('view/template.html.php');
	}

	public static function inscriptionPage()
	{
		ob_start();

		require('view/inscription.html.php');

		$pageContent = ob_get_clean();

		$pageTitle = 'Inscription';

		require('view/template.html.php');
	}
}
