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
        
        if (!empty($_GET['id']) && ctype_digit($_GET['id'])) {
            $actorId = $_GET['id'];
            //infos acteur
            $actor = $this->model->find($actorId);           
            if (!$actor) {
                // id acteur invalide : retour accueil
                \Http::redirect('index.php');
            }
        }

        if (!$actorId) {
            die("Pas de paramètre `id` dans l'URL !");
        }

        //titre de page
        $pageTitle = $actor['actor'];

        //infos commentaires
        $commentModel = new \Models\Post();
        
        $comments = $commentModel->listComments($actorId);
            //infos concernant la personne connectée (si elle a déjà mis un commentaire)
            if($comments) {
                $existingUserComment = $commentModel->existUserComment($actorId,'2');
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
        $likeState = $voteModel->checkVote($actorId,'2');

        if ($likeState == 'like') {
            $show = 'Vous recommandez ce partenaire';
        }
        elseif ($likeState == 'dislike') {
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
                                        'likeState',
                                        'show',
                                        'showForm'
        ));

    }
    
}