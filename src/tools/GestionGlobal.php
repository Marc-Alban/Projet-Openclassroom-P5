<?php
declare (strict_types = 1);

namespace App\Tools;

class GestionGlobal
{
/************************************getSession************************************************ */
    public function getSession(): array
    {
        return $_SESSION;
    }
/************************************End getSession************************************************ */
/************************************setParamSession************************************************ */
    public function setParamSession($name, $instance): array
    {
        $_SESSION["$name"] = $instance;
        return $_SESSION;
    }
/************************************End setParamSession*********************************************** */

}