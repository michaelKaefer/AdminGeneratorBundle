<?php

namespace MichaelKaefer\AdminGeneratorBundle\Object;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class ObjectUrlGenerator
{
    public function __construct(private UrlGeneratorInterface $urlGenerator, private RouteParametersGenerator $routeParametersGenerator)
    {
    }

    public function generate(string $route, object|string $object): string
    {
        return $this->urlGenerator->generate(
            $route,
            $this->routeParametersGenerator->generate($object),
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }
}
