<?php

namespace MichaelKaefer\AdminUiBundle\Object;

use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class Translator
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function transClass(string $class, bool $singular, bool $translate = false): string|TranslatableMessage
    {
        $params = [
            sprintf('objects.%s.%s', $class, $singular ? 'singular' : 'plural'),
            [],
            'MichaelKaeferAdminUiBundle',
        ];

        return $translate ? $this->translator->trans(...$params) : new TranslatableMessage(...$params);
    }

    public function transProperty(string $class, string $property, bool $translate = false): string|TranslatableMessage
    {
        $params = [
            sprintf('objects.%s.properties.%s', $class, $property),
            [],
            'MichaelKaeferAdminUiBundle',
        ];

        return $translate ? $this->translator->trans(...$params) : new TranslatableMessage(...$params);
    }
}
