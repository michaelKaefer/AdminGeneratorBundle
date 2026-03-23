<?php

namespace MichaelKaefer\AdminUiBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use MichaelKaefer\AdminUiBundle\Event\ConfigureMenuEvent;
use MichaelKaefer\AdminUiBundle\Object\MenuItemFactory;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class ListItemMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory,
        private MenuItemFactory $menuItemFactory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function build(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('list_item');

        $item = $options['item'];

        $menu->addChild($this->menuItemFactory->create('menu.list_item.detail', 'admin_generator_detail', $item));

        $this->eventDispatcher->dispatch(new ConfigureMenuEvent('admin_generator_list_item_menu', $menu));

        return $menu;
    }
}
