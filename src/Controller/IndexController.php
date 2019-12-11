<?php

namespace App\Controller;

use App\Model\Manager\IndexManager;
use App\View\View;

class IndexController extends View
{

    private $indexManager;

    public function __construct()
    {
        $this->indexManager = new IndexManager;
        parent::__construct();
    }

    public function homeRenderAction($id)
    {
        $graph = $this->indexManager->listeGraph();
        $modal = $this->indexManager->modalGraph($id);
        $this->renderer('Frontend', 'home', ['graph' => $graph, 'modal' => $modal]);
    }

    public function errorAction()
    {
        $this->renderer('Frontend', '404', null);
    }
}