<?php

namespace MichaelKaefer\AdminGeneratorBundle\Object\PathGenerator;

use MichaelKaefer\AdminGeneratorBundle\Object\PathGeneratorInterface;

use function Symfony\Component\String\u;

final readonly class ShortnamePathGenerator implements PathGeneratorInterface
{
    public function generate(string $class): string
    {
        return u(new \ReflectionClass($class)->getShortName())->snake()->replace('_', '-')->toString();
    }
}
