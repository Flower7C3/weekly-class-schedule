<?php

declare(strict_types=1);

namespace WCS4\Entity\Contract;

/**
 * Kontrakt dla encji posiadających identyfikator liczbowy.
 */
interface EntityWithIdInterface
{
    public function getId(): int;
}
