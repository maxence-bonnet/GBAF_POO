<?php

class Application
{
    public static function process()
    {
        $controllerName = "Actor";
        $task = "accueil";
        $controllersCallArray = ['accout','actor','connection','contact','files','mentions','post','vote','account'];      
        $tasksCallArray = ['accueil','acteur','contact','mentions','likeManage','addComment','delComment','profil','update'];

        if (!empty($_GET['controller']) && in_array($_GET['controller'],$controllersCallArray)) {                     
            $controllerName = ucfirst($_GET['controller']);      
        }

        if (!empty($_GET['task']) && in_array($_GET['task'],$tasksCallArray)) {
            $task = $_GET['task']; 
        }
        
        $controllerName = "\Controllers\\" . $controllerName;

        $controller = new $controllerName();
        $controller->$task();
    }
}
