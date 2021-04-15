<?php

namespace Controllers;

class Actor extends Controller
{
    protected $modelName = \Models\Actor::class;

    public function accueil()
    {
        $actorsInfo = $this->model->findAll();

        $pageTitle = "Accueil";

        \Renderer::render('accueil', compact('pageTitle','actorsInfo'));

    }

    public function acteur()
    {
        $actorId = null;
        $existingUserComment = false;
        $show = false;
        $showForm = false;

        // Vérification à globaliser à chaque fois qu'on aura besoin d'un id acteur
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            \Http::redirect('index.php');
        }
       
        $actorId = $_GET['id'];

        //infos acteur
        $actor = $this->model->find($actorId);           
        if (!$actor) { // id acteur invalide : retour accueil           
            \Http::redirect('index.php');
        }

        $userId = '2'; // Par défaut -> Jean Dujardin comme utilisateur avant de mettre le système de connexion
        
        //titre de page
        $pageTitle = $actor['actor'];

        //infos commentaires
        $commentModel = new \Models\Post();
        
        $comments = $commentModel->listComments($actorId);
            //infos concernant la personne connectée (si elle a déjà mis un commentaire)
        if($comments) {
            $existingUserComment = $commentModel->existUserComment($actorId,$userId);
        }
        

        //infos Likes
        $voteModel = new \Models\Vote();

            //infos compte des likes/dislikes
        $likeNumber = $voteModel->countLikes($actorId,'like');
        $dislikeNumber = $voteModel->countLikes($actorId,'dislike');

            //infos liste les likers/dislikers
        $likersList= $voteModel->listLikers($actorId,'like');
        $dislikersList = $voteModel->listLikers($actorId,'dislike');

            //infos concernant la personne connectée (si elle a déjà mis une mention)
        $voteCurrent = $voteModel->checkVote($actorId,$userId);

        if ($voteCurrent == 'like') {
            $show = 'Vous recommandez ce partenaire';
        }
        elseif ($voteCurrent == 'dislike') {
            $show = 'Vous déconseillez ce partenaire';	
        }

        //formulaire pour commenter demandé ?
        if (isset($_GET['add']) AND $_GET['add'] == 1) {
            $showForm = true;
        }
        
        // Render
        \Renderer::render('acteur', compact('actor',
                                        'pageTitle',
                                        'actorId',
                                        'comments',
                                        'existingUserComment',
                                        'likeNumber',
                                        'dislikeNumber',
                                        'likersList',
                                        'dislikersList',
                                        'show',
                                        'showForm'
        ));

    }
    
}