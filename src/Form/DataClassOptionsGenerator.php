<?php

namespace MichaelKaefer\AdminUiBundle\Form;

use MichaelKaefer\AdminUiBundle\Object\Translator;

final readonly class DataClassOptionsGenerator
{
    public function __construct(private  Translator $translator)
    {
    }

    public function generate(string $class): array
    {
        return [
            'label' => $this->translator->transClass($class, false),
        ];
    }
}
