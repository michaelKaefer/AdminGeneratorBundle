<?php

namespace MichaelKaefer\AdminUiBundle\Object\Provider;

use Doctrine\Persistence\ManagerRegistry;
use MichaelKaefer\AdminUiBundle\Object\ProviderInterface;

final readonly class DoctrineOrmProvider implements ProviderInterface
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }

    public function provide(): iterable
    {
        foreach ($this->managerRegistry->getManagers() as $objectManager) {
            foreach ($objectManager->getMetadataFactory()->getAllMetadata() as $classMetadata) {
                yield $classMetadata->getName();
            }
        }
    }

    public function has(string $class): bool
    {
        foreach ($this->provide() as $providedClass => $helper) {
            if ($class === $providedClass) {
                return true;
            }
        }

        return false;
    }
}
