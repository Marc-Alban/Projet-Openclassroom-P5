<?php
declare (strict_types = 1);
namespace App\Controller\FrontendController;

use App\Model\Manager\ArticleManager;
use App\View\View;

class ArticleController
{

    private $articleManager;
    private $view;

    /**
     * Fonction constructeur:
     * Récupère  la fonction parent construct "Twig/Environement"
     * de sa fille qui est la classe view, et instancie l'objet
     * Articlemanager dans une propriété
     *
     */
    public function __construct()
    {
        $this->articleManager = new ArticleManager();
        $this->view = new View();
    }

    /************************************Page Article************************************************* */
    /**
     * Retourne un article sur la page article cibler par l'id passé en paramètre
     *
     * @return void
     */
    public function ArticleAction(array $data): void
    {
        $id = $data['get']['id'] ?? null;
        $post = $this->articleManager->getPost((int) $id, $data);
        $this->view->renderer('Frontend', 'article', ['post' => $post]);
    }
    /************************************End Page Article************************************************* */

}