<?php

namespace Controllers;

class Account extends Controller
{
	protected $modelName = \Models\Account::class;

    public function profil()
    {
        $userId = '2'; // Par défaut -> Jean Dujardin comme utilisateur avant de mettre le système de connexion

        $accountInfo = $this->model->find($userId);

        $pageTitle = "Profil de " . $accountInfo['nom'] . ' ' . $accountInfo['nom'];

        \Renderer::render('profil', compact('pageTitle','accountInfo'));
	}

	public function update()
	{
		$userId = '2'; // Par défaut -> Jean Dujardin comme utilisateur avant de mettre le système de connexion

		if (isset($_POST['username']) && !empty($_POST['username'])) {
			if (preg_match("#^[a-z]{3,}$#i",$_POST['username'])) {
				if (!$this->model->find($_POST['username'])) {
					$this->model->updateUsername($_POST['username'],$userId);
					$_SESSION['usernamechanged'] = 1 ; // username bien modifié
				}
				else {
					$_SESSION['exist'] = 1; // ce username existe déjà
				}
			}
			else {
				$_SESSION['username_format'] = 1; // mauvais format
			}
		}

		if (isset($_POST['actual_pass']) && !empty($_POST['actual_pass'])
			 && isset($_POST['pass1']) && !empty($_POST['pass1'])
			 && isset($_POST['pass2']) && !empty($_POST['pass2'])) {
			
			$currentPassword = $_POST['actual_pass'];
			$newPassword1 = $_POST['pass1'];
			$newPassword2 = $_POST['pass2'];

			if(preg_match("#(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\d)(?=.*[^A-Za-z\d])#",$newPassword1) && $newPassword1 == $newPassword2 ) {
				if($this->model->testPassword($userId,$currentPassword)) {
					$newPassword1 = password_hash($newPassword1,PASSWORD_DEFAULT);
					$this->model->updatePassword($userId,$newPassword1);
					$_SESSION['passchanged'] = 1 ; // écriture effectuée
				}
				else {
					$_SESSION['wrongpass'] = 1 ; // mauvais mdp
				}
			}
			else {
				$_SESSION['invalidpass'] = 1 ;// mauvais format
			}	
		}

		if(is_uploaded_file($_FILES['photo']['tmp_name']))
		{
			$fileSize = $_FILES['photo']['size'];
			$filePathInfo = pathinfo($_FILES['photo']['name']);
			$fileExtension = $filePathInfo['extension'];

			$filesModel = new \Controllers\Files();

			if ($filesModel->testFile($fileExtension,$fileSize)) {

				$userAccount = $this->model->find($userId);

				$userCurrentPhotoName = $userAccount['photo'];

				if ($userCurrentPhotoName != 'default.png') {
					$filesModel->delPhoto($userCurrentPhotoName);
				}

				$newFileName = $filesModel->addPhoto($userId,$_FILES['photo'],$fileExtension);

				$this->model->updatePhotoName($userId,$newFileName);
			}
			else {
				$_SESSION['invalid_file'] = 1;
			}

		}	

		\Http::redirect('index.php?controller=account&task=profil');
	}

	public function testRegistration($last_name,$first_name,$username,$pass1,$pass2,$question,$answer)
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
	
	public function inscription($post) // Inscription
	{
		if(!empty($post['last_name']) AND
		!empty($post['first_name']) AND 
		!empty($post['username']) AND 
		!empty($post['pass1']) AND 
		!empty($post['pass2']) AND 
		!empty($post['question']) AND 
		!empty($post['answer'])) // Tous les champs sont remplis
		{
			foreach($post as $value => $key) // htmlspecialchars pour tout le monde
			{
				$post[$value] = htmlspecialchars($_POST[$value]);
			}
			$work = testRegistration($post['last_name'],$post['first_name'],$post['username'],$post['pass1'],$post['pass2'],$post['question'],$post['answer']);
			if($work)
			{
				$work = registerUser($post['last_name'],$post['first_name'],$post['username'],$post['pass1'],$post['question'],$post['answer']);
				if(!$work)
				{
					$_SESSION['unknown_error'] = 1 ;
				}
				else
				{
					$_SESSION['success'] = 1 ;
					header('Location: index.php?action=accueil');
				}			
			}
		}
		else
		{
			$_SESSION['missing_field'] = 1 ;
		}
		require('view/inscriptionView.php');
	}

	public function reinit($step) // Gestion de la réinitialisation du mot de passe via question secrète
	{
		if($step == 1)
		{
			$content = getReinitContent(1);
			require('view/reinitView.php');
		}
		elseif($step == 3)
		{
			if(isset($_POST['answer']) AND isset($_POST['pass1']) AND isset($_POST['pass2']) AND isset($_SESSION['usertemp']))
			{
				$username = $_SESSION['usertemp'];
				$answer = htmlspecialchars($_POST['answer']);		
				$test = testReinitAns($username,$answer);
				if(!$test)
				{
					$_SESSION['invalid_answer'] = 1 ;
					header('Location: index.php?action=reinit&fgt=2');
					// mauvaise réponse à la question secrète
				}
				else
				{
					$pass1 = htmlspecialchars($_POST['pass1']);
					$pass2 = htmlspecialchars($_POST['pass2']);
					$test = testReinitPass($pass1,$pass2);
					if(!$test)
					{
						$_SESSION['invalid_pass_format'] = 1 ;
						header('Location: index.php?action=reinit&fgt=2');
						// mauvais format de mot de passe (mais bonne réponse)
					}
					else
					{
						$work = reinitPass($username,$pass1);
						if(!$work)
						{
							$_SESSION['update_error'] = 1 ;
							header('Location: index.php?action=reinit&fgt=2');
							// erreur pendant l'écriture
						}
						else
						{
							unset($_SESSION['usertemp']);
							$_SESSION['passchanged'] = 1 ;
							header('Location: index.php?action=connexion');
							// succès dans la réinitialisation -> retour à la page de connexion
						}
					}
				}
			}
			else
			{
				$_SESSION['missing_field'] = 1 ;
				header('Location: index.php?action=reinit&amp;fgt=2');
				// manque certains champs
			}
		}
		elseif($step == 2 AND isset($_POST['username']) OR isset($_SESSION['usertemp']))
		{
			if(isset($_POST['username']))
			{
				$username = htmlspecialchars($_POST['username']);	
			}
			else // cas où il y a eu un précédent retour d'erreur
			{
				$username = htmlspecialchars($_SESSION['usertemp']);
			}

			$existing = existUsername($username);

			if(!$existing)
			{
				$_SESSION['invalid_user'] = 1 ;
				header('Location: index.php?action=reinit&amp;fgt=1');
				// utilisateur inexistant
			}
			else
			{
				$_SESSION['usertemp'] = $username;
				$question = getQuestion($username);
				$content = getReinitContent(2,$question);
				require('view/reinitView.php');
			}			
		}		
		else
		{
			$step = 1;
			require('view/reinitView.php');
		}
	}
}