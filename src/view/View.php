<?php
declare (strict_types = 1);

namespace App\View;

use App\Tools\GestionGlobal;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View extends Environment
{
    private $loader;
    private $superGlobal;

    /**
     * Fonction constructeur instanciant le constructeur parent
     * qui est twig environement
     */
    public function __construct()
    {
        $this->loader = new FilesystemLoader('../templates');
        $this->superGlobal = new GestionGlobal();
        parent::__construct($this->loader);
    }

/************************************Render View************************************************ */
    /**
     * Retourne la vue en fonction des paramètre passés
     *
     * @param string $path
     * @param string $view
     * @param array|null $data
     * @return void
     */
    public function renderer(string $path, string $view, ?array $data): void
    {
        $this->addGlobal('session', $this->superGlobal->getSession());
        echo $this->render($path . '/' . $view . '.html.twig', ['data' => $data]);
    }
/************************************End Render View************************************************ */
}