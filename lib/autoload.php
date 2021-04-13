<?php

// =============== Autoload ===============

// Chargera une classe à chaque fois qu'elle sera nécessaire

spl_autoload_register(function($className){
    $className = str_replace("\\", "/", $className);
    require_once("lib/$className.php");
});