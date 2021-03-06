<?php
declare (strict_types = 1);
namespace App\Model\Repository;

use App\Model\Entity\Article;
use App\Tools\Database;
use \PDO;

class ArticleRepository
{

    private $pdo;
    private $pdoStatement;
    private $article;

    /**
     * Fonction constructeur, instanciation de la bdd
     * dans la propriété pdo
     */
    public function __construct()
    {
        $this->pdo = Database::getPdo();
        $this->article = new Article;
    }
/************************************GetArticle in BDD************************************************* */
    public function getIdBddArticle(string $id): ?array
    {
        $this->pdoStatement = $this->pdo->query('SELECT id FROM article WHERE id =' . $id);
        $executeIsOk = $this->pdoStatement->execute();
        if ($executeIsOk) {
            $idBdd = $this->pdoStatement->fetch();
            if ($idBdd === false) {
                return null;
            }
            return $idBdd;
        } else if ($executeIsOk === false) {
            return null;
        }
        return null;
    }
/************************************last Article************************************************* */
    /**
     * Récupère le dernier article
     *
     * @return bool|Article|null
     * false si l'objet n'a pu être inséré, objet Article si une
     * correspondance est trouvé, NULL s'il n'y a aucune correspondance
     */
    public function last(): ?Object
    {

        $this->pdoStatement = $this->pdo->query('SELECT * FROM article WHERE lastArticle = 1 AND posted = 1 ORDER BY date DESC LIMIT 1');
        //execution de la requête
        $executeIsOk = $this->pdoStatement->execute();
        if ($executeIsOk) {
            //$article = $this->pdoStatement->fetchObject('App\Model\Entity\Article');
            $this->article = $this->pdoStatement->fetchObject(Article::class);
            if ($this->article) {
                return $this->article;
            } else if (!$this->article) {
                return null;
            }
        } else if (!$executeIsOk) {
            return null;
        }
        die();
    }
/************************************End last Article************************************************* */
/************************************ Update last Article with 0 for 1************************************************* */
    /**
     * Met à 0 le dernier article
     *
     * @return void
     */
    public function updateLast(array $data): void
    {
        $action = $data['get']['action'];
        if ($action === 'articleModif') {
            $requete = $this->pdo->prepare("SELECT * FROM article WHERE lastArticle = 1");
            $executeIsOk = $requete->execute();
            if ($executeIsOk === true) {
                $idRequete = $requete->fetchObject(Article::class);
                $this->pdoStatement = $this->pdo->prepare("UPDATE article SET lastArticle = 0 WHERE id = " . $idRequete->getId());
                $this->pdoStatement->execute();
            }
        }

    }
/************************************End Update last Article with 0 for 1************************************************* */
/************************************Read Article with id************************************************* */
    public function readId(int $id): ?object
    {
        //Liaison paramètres
        $this->pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
        //execution de la requête
        $executeIsOk = $this->pdoStatement->execute();
        if ($executeIsOk) {
            //$article = $this->pdoStatement->fetchObject('App\Model\Entity\Article');
            $this->article = $this->pdoStatement->fetchObject(Article::class);
            if ($this->article === false) {
                $articleFake = (object) [
                    'id' => '1',
                    'title' => 'Article inconnu',
                    'legende' => 'Défault',
                    'description' => 'Article inconnu',
                    'image' => 'default.png',
                    'date' => '',
                    'posted' => '1',
                    'lastArticle' => '0',
                ];
                return $articleFake;
            }
            return $this->article;
        }

        if (!$executeIsOk) {
            return null;
        }
    }
/************************************End Read Article with id************************************************* */
/************************************ReadAllPost with id************************************************* */
    /**
     * Récupère un objet Article à partir de son identifiant et l'envoie sur le back
     *
     * @param int $id identifiant d'un article
     * @return bool|Article
     * false si l'objet n'a pu être inséré, objet Article si une
     * correspondance est trouvé, NULL s'il n'y a aucune correspondance
     */
    public function readBack(int $id): ?object
    {
        $this->pdoStatement = $this->pdo->prepare('SELECT * FROM article WHERE id=:id');
        return $this->readId($id);

    }
/************************************End ReadAllPost with id************************************************* */
/************************************Read Post with id************************************************* */
    /**
     * Récupère un objet Article à partir de son identifiant et l'envoie sur le front
     *
     * @param int $id identifiant d'un article
     * @return bool|Article
     * false si l'objet n'a pu être inséré, objet Article si une
     * correspondance est trouvé, NULL s'il n'y a aucune correspondance
     */
    public function read(int $id): ?object
    {
        $this->pdoStatement = $this->pdo->prepare('SELECT * FROM article WHERE posted = 1 AND id=:id');
        return $this->readId($id);
    }
/************************************End Read Post with id************************************************* */
/************************************Not Repeat Read All************************************************* */
    public function articleReadAll(int $firstOfPage, int $perPage, string $side): array
    {
        if (isset($side) && !empty($side) && $side !== null) {
            if ($side === "readAll") {
                $this->pdoStatement = $this->pdo->query("SELECT * FROM article ORDER BY date LIMIT $firstOfPage,$perPage");
            } else if ($side === "readArticleAll") {
                $this->pdoStatement = $this->pdo->query("SELECT * FROM article WHERE id!=(SELECT max(id) FROM article WHERE lastArticle = 1) AND posted = 1 ORDER BY id LIMIT $firstOfPage,$perPage");
            }
        }
        $this->article = [];
        $articles = 1;
        while ($articles = $this->pdoStatement->fetchObject(Article::class)) {
            $this->article[] = $articles;
            $articles++;
        }
        if ($this->article === false) {
            $articleFake[] = [
                'id' => '1',
                'title' => 'Que le dernier article en bdd',
                'legende' => 'Défault',
                'description' => 'Que le dernier article en bdd',
                'image' => 'default.png',
                'date' => '',
                'posted' => '1',
                'lastArticle' => '1',
            ];
            return $articleFake;
        };
        return $this->article;
    }
/************************************End Not Repeat Read All************************************************* */
/************************************Write Post************************************************* */
    /**
     * insert en bdd
     *
     * @param string $title
     * @param string $legende
     * @param string $description
     * @param integer $posted
     * @param integer $lastArticle
     * @param string $tmpName
     * @param string $extention
     * @return void
     */
    public function articleWrite(string $title, string $legende, string $description, string $date, int $posted, int $lastArticle, string $tmpName, string $extention, ?string $id): void
    {
        if ($id === null && empty($id)) {
            $this->pdoStatement = $this->pdo->query('SELECT MAX(id) FROM article ORDER BY date = NOW()');
            $response = $this->pdoStatement->fetch();
            $id = $response['MAX(id)'] + 1;
            $p = [
                ':title' => $title,
                ':legende' => $legende,
                ':description' => $description,
                ':image' => $id . "." . $extention,
                ':date' => $date,
                ':posted' => $posted,
                ':lastArticle' => $lastArticle,
            ];
            $sql = "
            INSERT INTO article(title, legende, description, image, date, posted, lastArticle)
            VALUES(:title, :legende, :description, :image, :date, :posted, :lastArticle)
            ";
        } else if ($id !== null && !empty($id)) {
            $p = [
                ':title' => $title,
                ':legende' => $legende,
                ':description' => $description,
                ':image' => $id . "." . $extention,
                ':date' => $date,
                ':posted' => $posted,
                ':lastArticle' => $lastArticle,
            ];
            $sql = "
        UPDATE `article` SET `title`=:title,`legende`=:legende,`description`=:description, `image`=:image,`date`=:date, `posted`=:posted,`lastArticle`=:lastArticle where id = $id
        ";
        }

        $query = $this->pdo->prepare($sql);
        $query->execute($p);
        move_uploaded_file($tmpName, "img/article/" . $id . '.' . $extention);
    }
/************************************End Write Post************************************************* */
/************************************Delete Post Bdd With ID************************************************* */
    public function deleteArticle(int $id): void
    {
        $this->pdoStatement = $this->pdo->query("SELECT id, image FROM article WHERE id = $id");
        $article = $this->pdoStatement->fetch();
        $extention = explode('.', $article['image']);
        $sql = "
        DELETE FROM `article` WHERE id = $id
        ";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        unlink("img/article/" . $article['id'] . "." . $extention[1]);
    }
/************************************End Delete Post Bdd With ID************************************************* */
/************************************Not repeat Count************************************************* */
    public function count(string $side): ?string
    {
        if (isset($side) && !empty($side) && $side !== null) {
            if ($side === 'front') {
                $this->pdoStatement = $this->pdo->query("SELECT count(*) AS total FROM article WHERE id!=(SELECT max(id) FROM article WHERE lastArticle = 1) AND posted = 1 ");
            } else if ($side === 'back') {
                $this->pdoStatement = $this->pdo->query("SELECT count(*) AS total FROM article WHERE posted = 1 ");
            }
        }
        $req = $this->pdoStatement->fetch();
        if ($req) {
            $total = $req['total'];
            return $total;
        }
        return null;
    }
/************************************End Not repeat Count************************************************* */
/************************************Return Post With Year************************************************* */
    /**
     * Retourne les articles en fonctions de la date données dans l'url
     * sinon  si c'est false ou null alors par défaults ce sera 2019
     *
     * @param [type] $years
     * @return array
     */
    public function articleDate($years): array
    {
        $this->pdoStatement = $this->pdo->query("SELECT * FROM article WHERE YEAR( date ) = $years");
        return $this->pdoStatement->fetchAll();
    }
/************************************End Return Post With Year************************************************* */

}