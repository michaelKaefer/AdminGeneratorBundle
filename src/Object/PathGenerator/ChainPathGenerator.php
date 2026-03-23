<?php

namespace MichaelKaefer\AdminUiBundle\Object\PathGenerator;

use MichaelKaefer\AdminUiBundle\Object\PathGeneratorInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsAlias(PathGeneratorInterface::class)]
final readonly class ChainPathGenerator implements PathGeneratorInterface
{
    public function __construct(
        #[AutowireIterator('admin_generator.path_generator')] private iterable $pathGenerators,
    ) {
    }

    public function generate(string $class): string
    {
        foreach ($this->pathGenerators as $pathGenerator) {
            if ($path = $pathGenerator->generate($class)) {
                return $path;
            }
        }

        throw new \InvalidArgumentException(sprintf('No path generator found for class "%s".', $class));
    }
}
