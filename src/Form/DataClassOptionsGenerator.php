<?php

namespace MichaelKaefer\AdminUiBundle\Form;

use MichaelKaefer\AdminUiBundle\Object\TranslationIdGenerator;
use Symfony\Component\Translation\TranslatableMessage;

final readonly class DataClassOptionsGenerator
{
    public function __construct(
        private TranslationIdGenerator $translationIdGenerator
    ) {
    }

    public function generate(string $class): array
    {
        return [
            'label' => new TranslatableMessage(
                $this->translationIdGenerator->generate($class, false),
                [],
                'MichaelKaeferAdminUiBundle',
            ),
        ];
    }
}
