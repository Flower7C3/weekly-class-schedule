<?php

declare(strict_types=1);

namespace WCS4\Repository\Contract;

/**
 * Kontrakt dla repozytoriów tworzących tabele w bazie przy aktywacji pluginu.
 */
interface SchemaCreatableInterface
{
    public static function create_db_tables(): void;
}
