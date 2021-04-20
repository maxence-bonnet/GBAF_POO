<?php

namespace Controllers;

class Account extends Controller
{
	protected $modelName = \Models\Account::class;

	public static function isConnected()
	{
		return isset($_SESSION['connected']);
	}

	public function inscription()
	{
		if (Account::isConnected()) {
			\Http::redirect('index.php?controller=actor&task=accueil');
		}

		if(!empty($_POST['last_name']) && !empty($_POST['first_name']) && !empty($_POST['username']) && !empty($_POST['pass1']) && !empty($_POST['pass2']) && !empty($_POST['question']) &&
		!empty($_POST['answer'])) {
			if ($this->model->find($_POST['username']) || !preg_match("#^[a-z]{3,}$#i",$_POST['username'])) { // identifiant déjà existant ou trop court / format incorrect
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
				$answer = password_hash($_POST['answer'],PASSWORD_DEFAULT);
				$this->model->registerUser($_POST['last_name'],$_POST['first_name'],$_POST['username'],$password,$_POST['question'],$answer);
				$_SESSION['success'] = 1;
				\Http::redirect('index.php?controller=account&task=connexion');
			}
		}
		elseif (!empty($_POST['last_name']) && !empty($_POST['first_name']) && !empty($_POST['username']) && !empty($_POST['pass1']) && !empty($_POST['pass2']) && !empty($_POST['question']) && !empty($_POST['answer'])) {
			$_SESSION['missing_field'] = 1 ;
		}
		$pageTitle = "Inscription";
		\Renderer::render('inscription', compact('pageTitle'));
	}

	public function connexion()
	{
		if (Account::isConnected()) {
			\Http::redirect('index.php?controller=actor&task=accueil');
		}

		if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
			$accountInfo = $this->model->find($_POST['username']);
			if (password_verify($_POST['password'],$accountInfo['password'])) {			
				$_SESSION['connected'] = $accountInfo['id_user'];
				\Http::redirect('index.php?controller=actor&task=accueil');
			}
			else {
				$_SESSION['wrong'] = 1;
				\Http::redirect('index.php?controller=account&task=connexion');
			}
		}
		else {
			$pageTitle = "Connexion";
			\Renderer::render('connexion', compact('pageTitle'));
		}	
	}

	public function deconnexion()
	{
		if (isset($_SESSION['connected'])) {
			unset($_SESSION['connected']);
		}
		\Http::redirect('index.php?controller=account&task=connexion');
	}

    public function profil()
    {
		if (!Account::isConnected()) {
			\Http::redirect('index.php?controller=account&task=connexion');
		}

        $userId = $_SESSION['connected'];

        $accountInfo = $this->model->find($userId);

        $pageTitle = "Profil de " . $accountInfo['nom'] . ' ' . $accountInfo['nom'];

        \Renderer::render('profil', compact('pageTitle','accountInfo'));
	}

	public function update()
	{
		if (!Account::isConnected()) {
			\Http::redirect('index.php?controller=account&task=connexion');
		}

		$userId = $_SESSION['connected'];

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

	public function reinit() // Gestion de la réinitialisation du mot de passe via question secrète
	{
		if (Account::isConnected()) {
			\Http::redirect('index.php?controller=actor&task=accueil');
		}

		$allowedSteps = [1,2,3];
		if (!isset($_GET['step']) || !in_array($_GET['step'],$allowedSteps)) {
			$step = 1;
			$pageTitle = "Réinitialisation du mot de passe (1)";
			\Renderer::render('reinit', compact('pageTitle','step'));
		}

		$step = $_GET['step'];
		if($step == 1) {
			if (isset($_SESSION['usertemp'])) {
				unset($_SESSION['usertemp']);
			}
			$pageTitle = "Réinitialisation du mot de passe (1)";
			\Renderer::render('reinit', compact('pageTitle','step'));
		}
		elseif($step == 3) {
			if(isset($_POST['answer']) && isset($_POST['pass1']) && isset($_POST['pass2']) && isset($_SESSION['usertemp']))
			{
				$accountInfo = $this->model->find($_SESSION['usertemp']);
				
				$answer = $_POST['answer'];

				if (!password_verify($_POST['answer'],$accountInfo['reponse'])) {	
					$_SESSION['invalid_answer'] = 1 ;
					\Http::redirect('index.php?controller=account&task=reinit&step=2');
					// mauvaise réponse à la question secrète
				}
				else {
					$error = false;
					if (!preg_match("#(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\d)(?=.*[^A-Za-z\d])#",$_POST['pass1']) || strlen($_POST['pass1']) < 8) { // format incorrect
						$error = 1;
						$_SESSION['invalid_pass_format'] = 1 ;
					}
					if ($_POST['pass1'] != $_POST['pass2']) { // les mots de passe ne correspondent pas
						$error = 1;
						$_SESSION['pass_not_matching'] = 1 ;
					}
					if ($error) {
						\Http::redirect('index.php?controller=account&task=reinit&step=2');
					}
					else {
						$newPassword = password_hash($_POST['pass1'],PASSWORD_DEFAULT);

						$this->model->updatePassword($accountInfo['id_user'],$newPassword);

						unset($_SESSION['usertemp']);

						$_SESSION['passchanged'] = 1 ;

						\Http::redirect('index.php?controller=account&task=connexion');

						// succès dans la réinitialisation -> retour à la page de connexion
					}
				}
			}
			elseif (isset($_SESSION['usertemp'])) {
				$_SESSION['missing_field'] = 1 ;
				\Http::redirect('index.php?controller=account&task=reinit&step=2');
			}
			else {
				\Http::redirect('index.php?controller=account&task=reinit&step=1');
			}
		}
		elseif($step == 2) {
			if (isset($_POST['username'])) {
				$username = $_POST['username'];	
			}
			elseif (isset($_SESSION['usertemp'])) { // cas où il y a eu un retour d'erreur à l'étape qui suit
				$username = $_SESSION['usertemp'];
			}
			else {
				\Http::redirect('index.php?controller=account&task=reinit&step=1');
			}

			$accountInfo = $this->model->find($username);

			if (!$accountInfo) { // Identifiant inexistant
				$_SESSION['invalid_user'] = 1 ;
				$step = 1;
				$pageTitle = "Réinitialisation du mot de passe (1)";
				\Renderer::render('reinit', compact('pageTitle','step'));
			}
			else {
				$_SESSION['usertemp'] = $accountInfo['id_user'];
				$question = $accountInfo['question'];
				$step = 2;
				$pageTitle = "Réinitialisation du mot de passe (2)";
				\Renderer::render('reinit', compact('pageTitle','step','question'));
			}			
		}		
		else {
			$step == 1;
			$pageTitle = "Réinitialisation du mot de passe (1)";
			\Renderer::render('reinit', compact('pageTitle','step'));
		}
	}
}