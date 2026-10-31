<?php

namespace MichaelKaefer\AdminGeneratorBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use MichaelKaefer\AdminGeneratorBundle\Event\ConfigureMenuEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class HeaderLeftMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function build(): ItemInterface
    {
        $menu = $this->factory->createItem('menu');

        $this->eventDispatcher->dispatch(new ConfigureMenuEvent('admin_generator_header_left_menu', $menu));

        return $menu;
    }
}
