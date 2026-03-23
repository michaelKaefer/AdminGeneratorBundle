<?php

namespace MichaelKaefer\AdminUiBundle\Object\PathGenerator;

use MichaelKaefer\AdminUiBundle\Object\PathGeneratorInterface;

use function Symfony\Component\String\u;

final readonly class ShortnamePathGenerator implements PathGeneratorInterface
{
    public function generate(string $class): string
    {
        return u(new \ReflectionClass($class)->getShortName())->snake()->replace('_', '-')->toString();
    }
}
