<?php
declare (strict_types = 1);
namespace App\Model\Manager;

use App\Model\Repository\ArticleRepository;

// use App\Tools\Token;

class ArticleManager
{
    // private $token;
    private $articleRepository;
    private $title;
    private $legende;
    private $description;
    private $date;
    private $posted;
    private $lastArticle;
    private $file;
    private $tmpName;
    private $size;

    /**
     * Fonction constructeur, instanciation de l'articlerepository
     * dans la propriété articleRepository
     */
    public function __construct()
    {
        $this->articleRepository = new ArticleRepository();
        // $this->token = new Token();
        // $this->token->createSessionToken();
    }

    /**
     * Retourne le dernier article dans un tableau
     * ou false si il n'y a rien
     *
     * @return Object
     *
     */
    public function lastArticle(): Object
    {
        return $this->articleRepository->last();
    }

    /**
     * Retourne un article avec un id selectionner
     *
     * @return Object
     */
    public function article(?int $id, ?array $data): Object
    {
        $id = $data['get']['id'] ?? null;
        if ($id === null || empty($id)) {
            // var_dump($this->articleRepository->last());
            // die();
            return $this->articleRepository->last();
        }
        return $this->articleRepository->read((int) $id);
    }

    /**
     * Méthode pour la pagination des articles
     * sur la page blog
     *
     * @param array $data
     * @return array
     */
    public function pagination(array $data): array
    {
        $perPage = 5;
        $total = $this->articleRepository->countArticle();
        $nbPage = ceil($total / $perPage);
        $current = $data['get']['pp'] ?? null;

        if (!isset($data['get']['pp']) || empty($data['get']['pp']) || ctype_digit($data['get']['pp']) === false) {
            $current = 1;
        } else if ($data['get']['pp'] > $nbPage) {
            $current = $nbPage;
        }

        $firstOfPage = ($current - 1) * $perPage;

        return $tabArticle = [
            'current' => (int) $current,
            'nbPage' => (int) $nbPage,
            'articles' => $this->articleRepository->readAll($firstOfPage, $perPage),
        ];

    }

    /**
     * Retourne les articles en fonctions
     *
     * @param array $data
     * @return array
     */
    public function classification(array $data): array
    {
        $years = $data['get']['years'] ?? null;

        if (!isset($data['get']['years']) || empty($data['get']['years']) || ctype_digit($data['get']['years']) === false) {
            $years = 2019;
        }

        return $this->articleRepository->articleDate($years);
    }

    /**
     * Retourne le nombre d'article en bdd
     *
     * @return integer
     */
    public function nbPost(): int
    {
        $nbArticle = $this->articleRepository->countArticle();
        return (int) $nbArticle;
    }

    private function dataFormArticle(array $data): array
    {
        $this->title = htmlentities(trim($data['post']['title'])) ?? null;
        $this->legende = htmlentities(trim($data['post']['legende'])) ?? null;
        $this->description = htmlentities(trim($data['post']['description'])) ?? null;
        $this->date = $data['post']['date'] ?? null;
        $this->posted = (isset($data['post']['posted']) && $data['post']['posted'] === 'on') ? 1 : 0;
        $this->lastArticle = (isset($data['post']['lastArticle']) && $data['post']['lastArticle'] === 'on') ? 1 : 0;
        $this->tmpName = $data['files']['imageArticle']['tmp_name'] ?? null;
        $this->size = $data['files']['imageArticle']['size'] ?? null;

        $tabPost = [
            "title" => $this->title,
            "legende" => $this->legende,
            "description" => $this->description,
            "date" => $this->date,
            "posted" => $this->posted,
            "lastArticle" => $this->lastArticle,
            "tmpName" => $this->tmpName,
            "size" => $this->size,
        ];

        return $tabPost;
    }

    public function verifForm(array $data): ?array
    {

        $submit = $data['post']['submit'] ?? null;
        $action = $data['get']['action'] ?? null;
        $errors = $data['session']['errors'] ?? null;
        unset($data['session']['errors']);

        if ($submit) {

            $tabData = $this->dataFormArticle($data);

            $extentions = ['jpg', 'png', 'gif', 'jpeg'];

            $this->file = $data['files']['imageArticle']['name'];

            if (empty($data['files']['imageArticle']['name'])) {
                $this->file = 'default.png';
            }

            // var_dump($this->file);
            // die();

            $extention = strtolower(substr(strrchr($this->file, '.'), 1));
            $tailleMax = 2097152;

            //Nouvel article
            if ($action === 'newArticle') {

                if (empty($tabData['title']) || empty($tabData['description'])) {
                    $errors['contenu'] = 'Veuillez renseigner un contenu !';
                } else if (empty($tabData['title'])) {
                    $errors['emptyTitle'] = "Veuillez mettre un titre";
                } else if (empty($tabData['description'])) {
                    $errors['emptyDesc'] = "Veuillez mettre un paragraphe";
                } else if (empty($tabData['tmpName'])) {
                    $errors['imageVide'] = 'Image obligatoire pour un article ! ';
                } else if (!in_array($extention, $extentions)) {
                    $errors['image'] = 'Image n\'est pas valide! ';
                } else if ($tabData['size'] > $tailleMax) {
                    $errors['size'] = "Image trop grande, mettre une image en dessous de 2 MO ";
                }

                if (empty($errors)) {
                    if ($this->lastArticle === 1 && isset($lastArticle)) {
                        $this->articleRepository->updateLast();
                    }
                    $this->articleRepository->articleWrite($tabData['title'], $tabData['legende'], $tabData['description'], $tabData['date'], $tabData['posted'], $tabData['lastArticle'], $tabData['tmpName'], $extention);
                    $succes['ok'] = "Article bien enregistré";
                    return $succes;
                }
            }

            //Modification article
            if ($action === "articleModif") {
                $id = (int) $data['get']['id'];
                //$postManager->editChapter($id, $title, $description, $posted);
                if (!isset($file) || empty($file)) {
                    $errors['empty'] = 'Image manquante ! ';
                } else if (!in_array($extention, $extentions)) {
                    $errors['valide'] = 'Image n\'est pas valide! ';
                } else if (empty($errors)) {
                    //$postManager->editImageChapter($id, $title, $description, $tmpName, $extention, $posted);
                }
            }

            return $errors;
        }

        return null;
    }

}