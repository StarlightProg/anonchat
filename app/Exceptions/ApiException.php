<?php

namespace App\Exceptions;

use Exception;
use ReflectionClass;

class ApiException extends Exception
{
    public const TEMPORARY_REDIRECT = 307;
    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const METHOD_NOT_ALLOWED = 405;
    public const CONFLICT = 409;
    public const VALIDATION_ERROR = 422;
    public const TOO_EARLY = 425;
    public const TOO_MANY_REQUESTS = 429;
    public const RETRY = 449;
    public const INTERNAL_ERROR = 500;
    public const SERVICE_UNAVAILABLE = 503;
    
    private $error_code;
    private $reason;

    public function __construct(string $reason, int $code = 500, int $error_code = 500, Exception $previous = null)
    {
        parent::__construct($reason, $code, $previous);
        $this->error_code = $error_code;
        $this->reason = $reason;
    }

    public function getErrorCode()
    {
        return $this->error_code;
    }

    public static function getErrorMessage($code)
    {
        $self = new ReflectionClass(__CLASS__);
        $constants = array_flip($self->getConstants());
        return $constants[$code];
    }

    public function getReason()
    {
        return $this->reason;
    }
}
