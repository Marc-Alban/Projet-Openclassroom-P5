<?php
declare (strict_types = 1);
namespace App\Model\Manager;

use App\Model\Repository\PageRepository;
use App\Tools\GestionGlobal;
use App\Tools\Token;
use PDOException;

class PageManager extends PDOException
{

    private $pageRepository;
    private $tmpName;
    private $titlePage;
    private $title;
    private $description;
    private $size;
    private $error;
    private $files;
    private $extention;
    private $token;
    private $maSuperGlobale;

    /**
     * Fonction constructeur, instanciation de l'pageRepository
     * dans la propriété pageRepository
     */
    public function __construct()
    {
        $this->pageRepository = new PageRepository();
        $this->maSuperGlobale = new GestionGlobal();
        $this->token = new Token();
    }
/************************************ Read Page ************************************************* */
    /**
     * Retourne la liste des graphs sur le controller page
     *
     * @return array
     */
    public function readPage(?array $data): ?array
    {
        $title = $data['get']['title'] ?? null;
        $page = $data['get']['p'] ?? null;
        $id = $data['get']['id'] ?? null;
        $pageId = [];
        if (isset($page) && !empty($page) && isset($title) && !empty($title) && isset($id) && !empty($id)) {
            $pageId[] = $this->pageRepository->readPageId($data);
            if ($pageId[0] === null) {
                header('Location:index.php?p=table&liste=listePages');
                exit();
            }
            return $pageId;
        }
        $page = $this->pageRepository->readAll($title);
        return $page;
    }
/************************************End Read Page************************************************* */
/************************************get TitlePage For Link************************************************* */
    public function getTitleData(?string $link, ?string $nav): ?array
    {
        if ($link === 'link' && $nav === null) {
            $page = $this->pageRepository->readAllTitlePage('min', null);
            return $page;
        } else if ($link === null && $nav === 'nav') {
            $page = $this->pageRepository->readAllTitlePage(null, 'maj');
            return $page;
        }
        return null;
    }
/************************************End get Title************************************************* */
/************************************ dataFormPage ************************************************* */
    /**
     * Factorisation des données dans une fonction
     *
     * @return array
     */
    public function dataFormPage(array $data): void
    {
        $this->titlePage = $data['post']['namePage'] ?? null;
        $this->title = $data['post']['titlePage'] ?? null;
        $this->description = $data['post']['bodyPage'] ?? null;
        $this->size = $data['files']['imagePage']['size'] ?? null;
        $this->tmpName = $data['files']['imagePage']['tmp_name'] ?? null;
        $this->files = (empty($data['files']['imagePage']['name'])) ? 'default.png' : $data['files']['imagePage']['name'];
        $this->extention = strtolower(substr(strrchr($this->files, '.'), 1)) ?? null;
    }
/************************************End dataFormPage************************************************* */

/************************************ Verif Update Page ************************************************* */
    /**
     * Verifie le formulaire de mise à jour des pages
     *
     * @return array
     */
    public function verifPageUpdate(): ?string
    {
        $extentions = ['jpg', 'png', 'gif', 'jpeg'];
        $tailleMax = 2097152;

        if (empty($this->titlePage) && empty($this->title) && empty($this->description) && empty($this->tmpName)) {
            $this->error = 'Veuillez renseigner un contenu !';
        } else if (empty($this->titlePage)) {
            $this->error = "Veuillez mettre un titre pour la page";
        } else if (empty($this->title)) {
            $this->error = "Veuillez mettre une Titre";
        } else if (empty($this->description)) {
            $this->error = "Veuillez mettre un contenu dans la description";
        } else if (empty($this->tmpName)) {
            $this->error = 'Image obligatoire pour une page ! ';
        } else if (!in_array($this->extention, $extentions)) {
            $this->error = 'Image n\'est pas valide! ';
        } else if ($this->size > $tailleMax) {
            $this->error = "Image trop grande, mettre une image en dessous de 2 MO ";
        }
        return $this->error;

    }
/************************************End Verif Update Page************************************************* */
/************************************ Update Page ************************************************* */
    /**
     * Met à joour le formulaire des page en bdd
     *
     * @return array
     */
    public function updateBddPage(array $data): ?array
    {
        $action = $data['get']['action'] ?? null;
        $id = $data['get']['id'] ?? null;
        $errors = $data['session']['errors'] ?? null;
        unset($data['session']['errors']);
        if (isset($action) && $action === 'update') {
            $this->dataFormPage($data);
            if ($this->verifPageUpdate()) {
                $errors['errorImage'] = $errors . $this->error;
            }
            if ($this->token->compareTokens($data) !== null) {
                $errors['token'] = "Formulaire incorrect";
            }
            if (empty($errors)) {
                $this->pageRepository->addBddPage($this->title, $this->description, $this->tmpName, $this->extention, $id);
                $this->succesPage();
                header('Location: index.php?p=table&liste=listePages&action=update');
                exit();
            }
            return $errors;
        }
        return null;
    }
/************************************End Update Page************************************************* */
/************************************Return data succes Page************************************************* */
    public function succesPage(): array
    {
        $succes['successPage'] = "Page bien mise à jour";
        return $succes;
    }
/************************************End Return data succes Page************************************************* */

}