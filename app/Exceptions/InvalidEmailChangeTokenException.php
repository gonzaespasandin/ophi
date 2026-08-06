<?php

namespace App\Exceptions;

use Exception;

class InvalidEmailChangeTokenException extends Exception
{
    public function __construct(string $message = 'El enlace no es válido o expiró. Solicitá el cambio nuevamente.')
    {
        parent::__construct($message);
    }
}
