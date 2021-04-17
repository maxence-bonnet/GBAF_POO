<?php

namespace Controllers;

class Files extends Controller
{
	protected $modelName = \Models\Files::class;

	public function testFile($uploadExtension,$size) // Vérifie les caractéristiques du fichier reçu
	{
		$allowedExtensions = array('jpg', 'jpeg', 'png');
		if ($size <= 2000000 AND in_array($uploadExtension,$allowedExtensions)) {
			return true;
		}
		return false;
	}

	public function addPhoto($username,$photo,$fileExtension) // Ajoute la photo dans uploads
	{
		$uploaddir = 'public/images/uploads/';
		$fileName = basename($photo['name']);
		$fileName = rand(0,99999999999) . preg_replace("#\s#","_",$fileName);
		$uploadFile = $uploaddir . $fileName;
		move_uploaded_file($photo['tmp_name'], $uploadFile);
		if ($fileExtension == 'jpeg' OR $fileExtension == 'jpg') {
				$fileExtension = 'jpeg';
		}
		$this->imgtoMini($fileName,$fileExtension);
		return $fileName;
	}

	public function delPhoto($fileName)
	{
		unlink(realpath('C:/xampp/htdocs/GBAF_POO/public/images/uploads/' . $fileName));
	}

	public static function imgtoMini($fileName,$fileExtension) // jpeg ou png vers miniature
	{
		// fonction à nom dynamique
		$imagecreatefrom = 'imagecreatefrom' . $fileExtension;
		$image = 'image' . $fileExtension;
		$source = $imagecreatefrom('public/images/uploads/' . $fileName);
		$target = imagecreatetruecolor(150, 150);
		$source_width= imagesx($source);
		$source_height = imagesy($source);
		$target_width = imagesx($target);
		$target_height = imagesy($target);
		imagecopyresampled($target, $source, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);
		$image($target,'public/images/uploads/' . $fileName);	
	}
}