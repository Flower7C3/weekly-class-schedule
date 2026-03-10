<?php

declare(strict_types=1);

namespace WCS4\Repository\Contract;

/**
 * Kontrakt dla repozytoriów z możliwością wyczyszczenia tabeli (TRUNCATE).
 */
interface TruncatableRepositoryInterface
{
    public static function truncate(): void;
}
