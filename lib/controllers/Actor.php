<?php

namespace Controllers;

class Actor extends Controller
{
    protected $modelName = \Models\Actor::class;

    public function accueil()
    {
        $actors_info = $this->model->findAll();

        $pageTitle = "Accueil";

        \Renderer::render('accueil',    ['pageTitle' => $pageTitle,
                                        'actors_info' => $actors_info
                                        ]);

    }
    
}