<?php

declare(strict_types=1);

namespace WCS4\Controller\Contract;

/**
 * Kontrakt dla kontrolerów zarządzających stronami z szablonami (admin).
 */
interface ManagesTemplateInterface
{
    public static function getTemplateDir(): string;
}
