<?php

namespace Controllers;

class Post extends Controller
{
    protected $modelName = \Models\Post::class;

	public function addComment()
	{
        // Vérification à globaliser à chaque fois qu'on aura besoin d'un id acteur
        if (!empty($_GET['id']) && ctype_digit($_GET['id'])) {
            $actorId = $_GET['id'];
            //infos acteur
            $actor = $this->model->find($actorId);           
            if (!$actor) {
                // id acteur invalide : retour accueil
                \Http::redirect('index.php');
            }
        }

		$userId = $_SESSION['connected'];

		$comment = $_POST['new_comment']; // AJOUTER + DE VERIFICATIONS

		$existingUserComment = $this->model->existUserComment($actorId,$userId);

		if($existingUserComment) { // L'utilisateur a déjà commenté
			// $_SESSION['existing_comment'] = true;
		}
		else {
			$work = $this->model->addComment($userId,$actorId,$comment);
			if (!$work) {  // erreur pendant l'écriture
				echo 'Erreur dans l\'ajout du commentaire';
			}
			else {
				// $_SESSION['posted'] = true;				
			}		
		}
		\Http::redirect('index.php?controller=actor&task=acteur&id=' . $actorId);
	}

	public function delComment()
	{	
		// Vérification à globaliser à chaque fois qu'on aura besoin d'un id acteur
		if (!empty($_GET['id']) && ctype_digit($_GET['id'])) {
			$actorId = $_GET['id'];
			//infos acteur
			$actor = $this->model->find($actorId);           
			if (!$actor) {
				// id acteur invalide : retour accueil
				\Http::redirect('index.php');
			}
		}

		$userId = $_SESSION['connected'];

		$existingUserComment = $this->model->existUserComment($actorId,$userId);

		if ($existingUserComment) { // L'utilisateur a déjà commenté
			$work = $this->model->delComment($userId,$actorId);
			if (!$work) { // erreur pendant la suppression
				echo 'Erreur dans la suppression du commentaire';
			}
			else {
				// session_start();
				// $_SESSION['deleted_post'] = true;			
			}	
		}
		\Http::redirect('index.php?controller=actor&task=acteur&id=' . $actorId);
	}
}