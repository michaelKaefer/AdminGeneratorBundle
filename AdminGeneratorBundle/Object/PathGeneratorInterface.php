<?php

namespace MichaelKaefer\AdminGeneratorBundle\Object;

interface PathGeneratorInterface
{
    public function generate(string $class): ?string;
}
