<?php

namespace Controllers;

class Files extends Controller
{

}

function profileUpdatePhoto($username,$photo)// changement de photo
{
	$size = $photo['size'];
	$infos = pathinfo($photo['name']);
	$upload_ext = $infos['extension'];
	$test = testFile($upload_ext,$size);
	if($test)
	{
		$result = addPhoto($username,$photo,$upload_ext);
		if(!$result[0])
		{
			echo 'Erreur' . '<br/>';
		}
		else
		{
			$_SESSION['photo'] = $result[1];
		}
	}
	else
	{
		$_SESSION['invalid_file'] = 1;
	}
}

function testFile($upload_ext,$size) // Vérifie les caractéristiques du fichier reçu
{
	$allowed_extensions = array('jpg', 'jpeg', 'png');
	if($size <= 2000000 AND in_array($upload_ext,$allowed_extensions))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function addPhoto($username,$photo,$upload_ext) // Ajoute la photo dans uploads
{
	$db = dbConnect();
	delPhoto($username);
	$uploaddir = 'public/images/uploads/';
	$filename = basename($photo['name']);
	$filename = rand(0,99999999999) . preg_replace("#\s#","_",$filename);
	$uploadfile = $uploaddir . $filename;
	$work = move_uploaded_file($photo['tmp_name'], $uploadfile);
	if(!$work)
	{
		$work = false;
	}
	else
	{
		if($upload_ext == 'jpeg' OR $upload_ext == 'jpg')
		{
			jpegtoMini($filename);
			$work = updateUserAccount($username,$filename);
		}
		elseif($upload_ext == 'png' )
		{
			pngtoMini($filename);
			$work = updateUserAccount($username,$filename);
		}
		else // ne devrait pas arriver
		{
			$work = false;
		}
	}
	return array($work, $filename);
}

function jpegtoMini($filename) // jpeg vers miniature
{
	$source = imagecreatefromjpeg('public/images/uploads/' . $filename);
	$target = imagecreatetruecolor(150, 150);
	$source_width= imagesx($source);
	$source_height = imagesy($source);
	$target_width = imagesx($target);
	$target_height = imagesy($target);
	imagecopyresampled($target, $source, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);
	imagejpeg($target,'public/images/uploads/' . $filename);	
}

function pngtoMini($filename) // png vers miniature
{
	$source = imagecreatefrompng('public/images/uploads/' . $filename);
	$target = imagecreatetruecolor(150, 150);
	$source_width= imagesx($source);
	$source_height = imagesy($source);
	$target_width = imagesx($target);
	$target_height = imagesy($target);
	imagecopyresampled($target, $source, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);
	imagepng($target,'public/images/uploads/' . $filename);
}