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

		exit();
	}
}
