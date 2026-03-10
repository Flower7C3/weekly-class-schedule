<?php

declare(strict_types=1);

namespace WCS4\Exception\Contract;

use Throwable;

/**
 * Marker interface dla wyjątków WCS4 (np. do wspólnego catch).
 */
interface Wcs4ExceptionInterface extends Throwable
{
}
