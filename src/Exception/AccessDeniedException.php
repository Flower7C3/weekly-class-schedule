<?php

namespace WCS4\Exception;

use WCS4\Exception\Contract\Wcs4ExceptionInterface;

class AccessDeniedException extends \RuntimeException implements Wcs4ExceptionInterface
{

    public function __construct(?string $message = null)
    {
        if (is_null($message)) {
            $message = __('You are no allowed to run this action', 'wcs4');
        }
        parent::__construct($message);
    }
}