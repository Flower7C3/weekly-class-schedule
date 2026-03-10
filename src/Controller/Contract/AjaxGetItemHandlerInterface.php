<?php

declare(strict_types=1);

namespace WCS4\Controller\Contract;

/**
 * Kontrakt dla kontrolerów obsługujących AJAX „pobierz wpis” (get_item).
 */
interface AjaxGetItemHandlerInterface
{
    public static function get_item(): void;
}
