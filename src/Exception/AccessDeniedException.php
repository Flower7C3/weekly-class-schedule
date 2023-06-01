<?php

namespace WCS4\Exception;

class AccessDeniedException extends \RuntimeException
{

    public function __construct(?string $message = null)
    {
        if (is_null($message)) {
            $message = __('You are no allowed to run this action', 'wcs4');
        }
        parent::__construct($message);
    }
}