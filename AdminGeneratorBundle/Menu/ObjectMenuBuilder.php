<?php

namespace MichaelKaefer\AdminGeneratorBundle\Menu;

use Doctrine\Persistence\ManagerRegistry;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use MichaelKaefer\AdminGeneratorBundle\Event\ConfigureMenuEvent;
use MichaelKaefer\AdminGeneratorBundle\Object\MenuItemFactory;
use MichaelKaefer\AdminGeneratorBundle\Object\PathGeneratorInterface;
use MichaelKaefer\AdminGeneratorBundle\Object\RouteParametersGenerator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class ObjectMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory,
        private MenuItemFactory $menuItemFactory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function build(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('object');

        $object = $options['object'];

        if (is_string($object)) {
            $class = $object;
            $object = null;
        } else {
            $class = $object::class;
        }

        switch ($options['current_route']) {
            case 'admin_generator_list':
                $menu->addChild($this->menuItemFactory->create('menu.object.create', 'admin_generator_create', $class));
                break;
            case 'admin_generator_detail':
                $menu->addChild($this->menuItemFactory->create('menu.object.edit', 'admin_generator_edit', $object));
                break;
            case 'admin_generator_edit':
                $menu->addChild($this->menuItemFactory->create('menu.object.detail', 'admin_generator_detail', $object));
        }

        $this->eventDispatcher->dispatch(new ConfigureMenuEvent('admin_generator_object_menu', $menu));

        return $menu;
    }
}
