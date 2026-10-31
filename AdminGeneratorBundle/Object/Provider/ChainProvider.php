<?php

namespace MichaelKaefer\AdminGeneratorBundle\Object\Provider;

use MichaelKaefer\AdminGeneratorBundle\Object\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class ChainProvider implements ProviderInterface
{
    private array $classes;

    public function __construct(
        #[AutowireIterator('admin_generator.object_provider')] private readonly iterable $objectProviders,
    ) {
        foreach ($this->objectProviders as $provider) {
            foreach ($provider->provide() as $class) {
                if (isset($this->classes[$class])) {
                    throw new \InvalidArgumentException(sprintf('Class "%s" already exists.', $class));
                }

                $this->classes[$class] = true;
            }
        }
    }

    public function provide(): iterable
    {
        return array_keys($this->classes);
    }

    public function has(string $class): bool
    {
        return isset($this->classes[$class]);
    }
}
