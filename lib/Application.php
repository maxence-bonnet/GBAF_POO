<?php

class Application
{
    public static function process()
    {
        $controllerName = "Account";
        $task = "connexion";   

        if (!empty($_GET['controller']) && !empty($_GET['task'])) {                    
            $controllerName = ucfirst($_GET['controller']);
            $task = $_GET['task'];
            if (!self::testCall($controllerName,$task)) {
                $controllerName = "Account";
                $task = "connexion"; 
            }
        }

        $controllerName = "\Controllers\\" . $controllerName;

        $controller = new $controllerName();

        $controller->$task();
    }

    // Contrôle de l'instruction envoyée (seules les insctruction avec controller & task compatibles seront validées)
    public static function testCall($controllerName,$task) : bool
    {
        switch($controllerName) {
            case 'Contact':
                $controllerValue = 1;
                break;
            case 'Mentions':
                $controllerValue = 2;
                break;
            case 'Vote':
                $controllerValue = 3;
                break;
            case 'Post':
                $controllerValue = 4;
                break;
            case 'Actor':
                $controllerValue = 5;
                break;
            case 'Account':
                $controllerValue = 6;
                break;
            default:
            $controllerValue = -1;  
        }

        switch($task) {
            case 'contact':
                $taskValue = 1;
                break;
            case 'mentions':
                $taskValue = 2;
                break;
            case 'likeManage':
                $taskValue = 3;
                break;
            case 'addComment':
                $taskValue = 4;
                break;
            case 'delComment':
                $taskValue = 4;
                break;
            case 'accueil':
                $taskValue = 5;
                break;
            case 'acteur':
                $taskValue = 5;
                break;
            case 'inscription':
                $taskValue = 6;
                break;
            case 'connexion':
                $taskValue = 6;
                break;
            case 'deconnexion':
                $taskValue = 6;
                break;
            case 'profil':
                $taskValue = 6;
                break;
            case 'update':
                $taskValue = 6;
                break;
            case 'reinit':
                $taskValue = 6;
                break;
            default:
            $taskValue = -2;  
        }

        return $controllerValue === $taskValue ? true : false ;
    }

    /* [Controller] + [task] compatibles:

    Contact + contact

    Mentions + mentions

    Vote + likeManage

    Post + addComment
    Post + delComment

    Actor + accueil
    Actor + acteur

    Account + inscription
    Account + connexion
    Account + deconnexion
    Account + profil
    Account + update
    Account + reinit
    */
}
