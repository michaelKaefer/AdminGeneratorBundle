<?php

namespace MichaelKaefer\AdminUiBundle\Object;

class TranslationIdGenerator
{
    public function generate(string $class, bool $singular): string
    {
        return sprintf('objects.%s.%s', $class, $singular ? 'singular' : 'plural');
    }

    public function generateForProperty(string $class, string $property): string
    {
        return sprintf('objects.%s.properties.%s', $class, $property);
    }
}
