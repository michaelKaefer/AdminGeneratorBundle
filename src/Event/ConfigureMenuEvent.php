<?php

namespace MichaelKaefer\AdminUiBundle\Event;

use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class ConfigureMenuEvent extends Event
{
    public function __construct(private(set) readonly string $alias, private(set) readonly ItemInterface $menu)
    {
    }
}
