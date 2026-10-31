<?php

namespace MichaelKaefer\AdminGeneratorBundle\Object;

interface ProviderInterface
{
    public function provide(): iterable;
    public function has(string $class): bool;
}
