<?php

namespace Controllers;

abstract class Controller
{
    protected $model;
    protected $modelName;

    public function __construct()
    {
        $this->model = new $this->modelName();

        // redirection pour toute instanciation de classe différente de Account/Contact/Mentions si utilisateur non connecté
        // instanciation autorisée de Account au cas par cas ensuite
        $allowed = [\Models\Account::class,\Models\Mentions::class,\Models\Contact::class];
        if (!Account::isConnected() && !in_array($this->modelName,$allowed)) {
			\Http::redirect('index.php?controller=account&task=connexion');
		}
    }
}