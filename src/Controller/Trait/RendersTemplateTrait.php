<?php

declare(strict_types=1);

namespace WCS4\Controller\Trait;

/**
 * Trait do włączania szablonów PHP z przekazaniem zmiennych.
 * Wymaga w klasie metody: public static function getTemplateDir(): string
 * (np. przez implementację ManagesTemplateInterface).
 */
trait RendersTemplateTrait
{
    /**
     * Włącza plik szablonu i przekazuje zmienne do lokalnego scope.
     *
     * @param string $template Nazwa pliku (np. 'admin.php')
     * @param array<string, mixed> $vars Zmienne dostępne w szablonie (np. ['table' => ..., 'search' => ...])
     */
    protected static function renderTemplate(string $template, array $vars = []): void
    {
        $templateDir = static::getTemplateDir();
        extract($vars, EXTR_SKIP);
        include $templateDir . $template;
    }
}
