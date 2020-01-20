<?php
declare (strict_types = 1);
namespace App\Model\Repository;

use App\Model\Entity\Partenaire;
use App\Tools\Database;

class PartenaireRepository
{

    private $pdo;
    private $pdoStatement;

    /**
     * Fonction constructeur, instanciation de la bdd
     * dans la propriété pdo
     */
    public function __construct()
    {
        $this->pdo = Database::getPdo();
    }

    /************************************Read All Partenaire************************************************* */
    /**
     * Récupère tous les objets Partenaire
     *
     * @return bool|Partenaire|null
     * false si l'objet n'a pu être inséré, objet Partenaire si une
     * correspondance est trouvé, NULL s'il n'y a aucune correspondance
     */
    public function readAll(): array
    {
        $this->pdoStatement = $this->pdo->query("SELECT * FROM partenaire WHERE posted = 1");
        $partenaire = [];
        while ($partenaires = $this->pdoStatement->fetchObject(Partenaire::class)) {
            $partenaire[] = $partenaires;
            $partenaire++;
        }
        return (array) $partenaire;
    }
/************************************End Read All Partenaire************************************************* */
/************************************Count partenaire************************************************* */
    public function countpartenaire(): ?string
    {
        $this->pdoStatement = $this->pdo->query("SELECT count(*) AS total FROM partenaire WHERE posted = 1");
        $req = $this->pdoStatement->fetch();
        if ($req) {
            $total = $req['total'];
            return $total;
        }
        return null;
    }
/************************************End Count partenaire************************************************* */

}