<?php

namespace MichaelKaefer\AdminUiBundle\Object;

use Doctrine\Persistence\ManagerRegistry;
use MichaelKaefer\AdminUiBundle\Attribute\SingleInstance;

final readonly class RouteParametersGenerator
{
    public function __construct(private PathGeneratorInterface $pathGenerator, private ManagerRegistry $managerRegistry, private SingleInstanceProvider $singleInstanceProvider)
    {
    }

    public function generate(string|object $classOrObject): array
    {
        if (is_string($classOrObject)) {
            $class = $classOrObject;
            $object = null;
            if (new \ReflectionClass($class)->getAttributes(SingleInstance::class)) {
                $object = $this->singleInstanceProvider->provide($class);
            }
        } else {
            $object = $classOrObject;
            $class = $object::class;
        }

        $parameters = ['class' => $this->pathGenerator->generate($class)];

        if ($object) {
            $parameters['identifierValues'] = $this->managerRegistry->getManagerForClass($class)
                ->getClassMetadata($class)->getIdentifierValues($object);
        }

        return $parameters;
    }
}
