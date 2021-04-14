<?php

namespace Controllers;

class Contact extends Controller
{
    protected $modelName = \Models\Contact::class;

    public function contact()
    {
        $pageTitle = "Contact";

        \Renderer::render('contact', compact('pageTitle'));
    }  
}