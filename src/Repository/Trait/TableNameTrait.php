<?php

declare(strict_types=1);

namespace WCS4\Repository\Trait;

/**
 * Trait zwracający nazwę tabeli z prefiksem WordPress (wcs4_).
 */
trait TableNameTrait
{
    protected static function tableName(string $suffix): string
    {
        global $wpdb;

        return $wpdb->prefix . 'wcs4_' . $suffix;
    }
}
