<?php

namespace WCS4\Exception;

class ValidationException extends \RuntimeException
{

    public function __construct(private array $errors)
    {
        parent::__construct(__('Validation error.', 'wcs4'));
    }

    public function getErrors(): array
    {
        return $this->errors;
    }


}