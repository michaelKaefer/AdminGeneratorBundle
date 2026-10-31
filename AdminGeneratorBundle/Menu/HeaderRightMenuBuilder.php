<?php

namespace MichaelKaefer\AdminGeneratorBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use MichaelKaefer\AdminGeneratorBundle\Event\ConfigureMenuEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class HeaderRightMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory,
        private EventDispatcherInterface $eventDispatcher,
        private Security $security,
    ) {
    }

    public function build(): ItemInterface
    {
        $menu = $this->factory->createItem('menu');

        $user = $this->security->getUser();
        $menu->addChild($user->getUserIdentifier());

        $menu->addChild($this->factory->createItem('Logout', ['route' => 'app_logout']));

        $this->eventDispatcher->dispatch(new ConfigureMenuEvent('admin_generator_header_right_menu', $menu));

        return $menu;
    }
}
