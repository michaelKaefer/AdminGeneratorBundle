<?php

namespace MichaelKaefer\AdminGeneratorBundle\Object;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

final readonly class MenuItemFactory
{
    public function __construct(private FactoryInterface $factory, private RouteParametersGenerator $routeParametersGenerator)
    {
    }

    public function create(string $translationId, string $route, string|object $classOrObject): ItemInterface
    {
        return $this->factory->createItem($translationId, [
            'route' => $route,
            'routeParameters' => $this->routeParametersGenerator->generate($classOrObject),
        ])->setExtra('translation_domain', 'MichaelKaeferAdminGeneratorBundle');
    }
}
