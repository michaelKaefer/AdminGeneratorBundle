<?php

namespace MichaelKaefer\AdminUiBundle\Object;

use Doctrine\Persistence\ManagerRegistry;

final readonly class SingleInstanceProvider
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }

    public function provide(string $class): object
    {
        $object = $this->managerRegistry->getManagerForClass($class)
            ->getRepository($class)
            ->createQueryBuilder('o')
            ->getQuery()
            ->getOneOrNullResult();

        if (!$object) {
            $object = new $class();
            $this->managerRegistry->getManagerForClass($class)->persist($object);
            $this->managerRegistry->getManagerForClass($class)->flush();
        }

        return $object;
    }
}
