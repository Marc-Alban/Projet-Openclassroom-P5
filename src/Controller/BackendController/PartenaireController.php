<?php
declare (strict_types = 1);
namespace App\Controller\BackendController;

use App\View\View;

class PartenaireController extends View
{
    /**
     * Rendu des partenaires
     *
     * @return void
     */
    public function PartenaireAction(): void
    {
        $this->renderer('Backend', 'partenaire', null);
    }
}