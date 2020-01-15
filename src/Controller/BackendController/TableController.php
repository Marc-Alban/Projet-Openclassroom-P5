<?php
declare (strict_types = 1);
namespace App\Controller\BackendController;

use App\Model\Manager\PartenaireManager;
use App\View\View;

class TableController
{
    private $view;
    private $partenaireManager;

    public function __construct()
    {
        $this->view = new View();
        $this->partenaireManager = new PartenaireManager();

    }
/************************************Page table************************************************* */

    /**
     * Rendu des listes sous form de tableau
     *
     * @return void
     */
    public function TableAction(): void
    {
        $partenaire = $this->partenaireManager->listePartenaire();
        $this->view->renderer('Backend', 'table', ['partenaire' => $partenaire]);
    }
/************************************End Page Table************************************************* */

}