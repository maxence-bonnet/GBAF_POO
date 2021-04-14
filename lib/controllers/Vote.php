<?php

namespace Controllers;

class Vote extends Controller
{
    protected $modelName = \Models\Vote::class;

	public function likeManage()
	{
		$voteCallArray = [1,2,3];

		$actorModel = new \Models\Actor();

		$actor = $actorModel->find($_GET['id']);

		if (empty($_GET['id']) || empty($_GET['vote']) || !in_array($_GET['vote'],$voteCallArray) || !$actor) {
			// si éléments $_GET vides ou incorrects / acteur inexistant dans base de données
			\Http::redirect('index.php');
		}

		$voteRequest = $_GET['vote'];

		$actorId = $actor['id_actor'];

		$userModel = new \Models\Account();

		$userId = $userModel->getUserId('Jean');

		$voteCurrent = $this->model->checkVote($actorId,$userId);

		if (!$voteCurrent) {
			if ($voteRequest == 1 || $voteRequest == 2) { // pas encore de like / dislike pour l'utilisateur actuel
				if ($voteRequest == 1) {
					$voteRequest = 'like';
				}
				elseif ($voteRequest == 2) {
					$voteRequest = 'dislike';
				}
				$this->model->addVote($actorId,$userId,$voteRequest);
			}
		}
		elseif ($voteCurrent && $voteRequest == 3) {
				// Supprimer la mention like ou dislike
				$this->model->deleteVote($actorId,$userId);
		}
		elseif (($voteRequest == 1 && $voteCurrent = 'dislike') || ($voteRequest == 2 && $voteCurrent = 'like')) {
			// mise à jour de la mention (permutation)
			$this->model->updateVote($actorId,$userId,$voteRequest);
		}
		\Http::redirect('index.php?controller=acteur&task=acteur&id=' . $actorId);
	}
	
}


