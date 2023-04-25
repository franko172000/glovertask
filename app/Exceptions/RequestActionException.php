<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class RequestActionException extends Exception {
    /**
     * OtterException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param array  $errors
     * @param string $actionClass
     *
     * @return static
     */
    public static function withMessages(string $message, string $actionClass): self
    {
        return new self(sprintf(
            '%s. Error thrown in %s',
            $message,
            class_basename($actionClass),
        ));
    }
}
