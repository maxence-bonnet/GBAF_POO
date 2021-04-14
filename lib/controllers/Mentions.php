<?php

namespace Controllers;

class Mentions extends Controller
{
    protected $modelName = \Models\Mentions::class;

    public function mentions()
    {
        $pageTitle = "Mentions-Légales";

        \Renderer::render('mentions-legales', compact('pageTitle'));
    }
}