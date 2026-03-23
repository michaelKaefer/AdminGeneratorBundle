<?php

namespace MichaelKaefer\AdminUiBundle\ArgumentResolver;

use MichaelKaefer\AdminUiBundle\Object\ProviderInterface;
use MichaelKaefer\AdminUiBundle\Object\PathGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class ObjectResolver implements ValueResolverInterface
{
    public function __construct(
        private ProviderInterface $objectProvider,
        private PathGeneratorInterface $pathGenerator,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $value = $request->attributes->get($argument->getName());

        if (!is_string($value)) {
            return [];
        }

        foreach ($this->objectProvider->provide() as $class) {
            if ($this->pathGenerator->generate($class) === $value) {
                yield $class;
            }
        }
    }
}
