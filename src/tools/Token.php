<?php
declare (strict_types = 1);
namespace App\Tools;

use App\Tools\Database;
use App\Tools\GestionGlobal;

class Token
{
    private $token;
    private $pdo;
    private $pdoStatement;
    private $getTokenBdd;
    private $GestionGlobal;

    public function __construct()
    {
        $this->pdo = Database::getPdo();
        $this->GestionGlobal = new GestionGlobal();
    }
/************************************Create Token Session************************************************ */
/**
 * Créer les tokens
 *
 * @return void
 */
    public function createSessionToken(): string
    {
        $this->token = bin2hex(random_bytes(32));
        return $this->token;
    }
/************************************End Create Token Session************************************************ */
/************************************Compare Token Session************************************************ */
    /**
     * Compare les tokens
     *
     * @param [type] $session
     * @param array $getData
     * @return string|null
     */
    public function compareTokens(array $data): ?string
    {
        if (empty($data['session']['token']) || empty($data['post']['token']) || $data['session']['token'] !== $data['post']['token']) {
            return $errors['formToken'] = "Formulaire incorrect";
        }
        return null;
    }
/************************************End Compare Token Session************************************************ */
}