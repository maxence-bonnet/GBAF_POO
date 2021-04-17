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

	public function connexion()
	{
		if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
			$accountInfo = $this->model->find($_POST['username']);
			if (password_verify($_POST['password'],$accountInfo['password'])) {
				$_SESSION[$accountInfo['id_user']] = 1;
				\Http::redirect('index.php?controller=acteur&task=accueil');
			}
			else {
				$_SESSION['wrong'] = 1;
				\Http::redirect('index.php?controller=account&task=connexion');
			}
		}
		else {
			\Renderer::connectionPage();
		}	
	}

	public function inscription()
	{
		if(!empty($_POST['last_name']) && !empty($_POST['first_name']) && !empty($_POST['username']) && !empty($_POST['pass1']) && !empty($_POST['pass2']) && !empty($_POST['question']) &&
		!empty($_POST['answer'])) {
			if ($this->model->find($_POST['username']) || strlen($_POST['username']) < 3) { // identifiant déjà existant ou trop court
				$error[] = 'exist';
			}
			if (!preg_match("#(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\d)(?=.*[^A-Za-z\d])#",$_POST['pass1']) OR strlen($_POST['pass1']) < 8) { // format incorrect
				$error[] = 'invalidpass';
			}
			if ($_POST['pass1'] != $_POST['pass2']) { // les mots de passe ne correspondent pas
				$error[] = 'passnotmatching';
			}

			if (isset($error)) {
				foreach($error as $value => $key)
				{
					$_SESSION[$key] = 1;
				}
				$test = false;
			}
			else {
				$test = true;
			}

			if ($test) {
				$password = password_hash($_POST['pass1'],PASSWORD_DEFAULT);
				$this->model->registerUser($_POST['last_name'],$_POST['first_name'],$_POST['username'],$password,$_POST['question'],$_POST['answer']);
				$_SESSION['success'] = 1;
				\Http::redirect('index.php?controller=account&task=connexion');
			}
		}
		elseif (!empty($_POST['last_name']) && !empty($_POST['first_name']) && !empty($_POST['username']) && !empty($_POST['pass1']) && !empty($_POST['pass2']) && !empty($_POST['question']) && !empty($_POST['answer'])) {
			$_SESSION['missing_field'] = 1 ;
		}
		\Renderer::inscriptionPage();
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

	function deconnection()
	{
		session_destroy();
		\Http::redirect('index.php?controller=account&task=connexion');
	}

}