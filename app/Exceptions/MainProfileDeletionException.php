<?php

namespace App\Exceptions;

use Exception;

class MainProfileDeletionException extends Exception
{
    public function __construct(string $message = 'No podés eliminar tu perfil principal')
    {
        parent::__construct($message);
    }
}
