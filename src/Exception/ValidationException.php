<?php

namespace WCS4\Exception;

use WCS4\Exception\Contract\Wcs4ExceptionInterface;

class ValidationException extends \RuntimeException implements Wcs4ExceptionInterface
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