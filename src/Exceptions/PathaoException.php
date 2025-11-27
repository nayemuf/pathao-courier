<?php

namespace Nayemuf\PathaoCourier\Exceptions;

use Exception;

class PathaoException extends Exception
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * PathaoException constructor.
     *
     * @param string $message
     * @param int $code
     * @param array $errors
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Get errors array
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

