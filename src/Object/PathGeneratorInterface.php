<?php

namespace MichaelKaefer\AdminUiBundle\Object;

interface PathGeneratorInterface
{
    public function generate(string $class): ?string;
}
