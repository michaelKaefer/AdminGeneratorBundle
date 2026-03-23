<?php

namespace MichaelKaefer\AdminUiBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use MichaelKaefer\AdminUiBundle\Attribute\AsMenuItem;
use MichaelKaefer\AdminUiBundle\Attribute\SingleInstance;
use MichaelKaefer\AdminUiBundle\Event\ConfigureMenuEvent;
use MichaelKaefer\AdminUiBundle\Object\MenuItemFactory;
use MichaelKaefer\AdminUiBundle\Object\ProviderInterface;
use MichaelKaefer\AdminUiBundle\Object\TranslationIdGenerator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class ObjectsMenuBuilder
{
    public function __construct(
        private array $order,
        private FactoryInterface $factory,
        private ProviderInterface $objectProvider,
        private TranslationIdGenerator $translationIdGenerator,
        private MenuItemFactory $menuItemFactory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function build(): ItemInterface
    {
        $menu = $this->factory->createItem('objects');

        $order = [];

        foreach ($this->objectProvider->provide() as $class) {
            $reflector = new \ReflectionClass($class);

            if (!$attributes = $reflector->getAttributes(AsMenuItem::class, \ReflectionAttribute::IS_INSTANCEOF)) {
                continue;
            }

            foreach ($attributes as $reflectionAttribute) {
                /** @var AsMenuItem $instance */
                $instance = $reflectionAttribute->newInstance();

                if ('admin_generator_objects_menu' === $instance->menu) {
                    if (!$instance->child) {
                        $parent = $menu->getRoot();
                    } else {
                        $parent = $menu->getChild($instance->child) ?? $menu->addChild($instance->child)->setExtra('translation_domain', 'MichaelKaeferAdminUiBundle');
                    }

                    $menuItem = $this->menuItemFactory->create(
                        $this->translationIdGenerator->generate($class, true),
                        $reflector->getAttributes(SingleInstance::class, \ReflectionAttribute::IS_INSTANCEOF)
                            ? 'admin_generator_edit'
                            : 'admin_generator_list',
                        $class,
                    );

                    $order[$parent->getName()][$instance->priority ?? 0][] = $menuItem->getName();

                    $parent->addChild($menuItem);
                }
            }
        }

        $menu->reorderChildren($this->order);

        $this->reorderChildren($menu, $order);

        $this->eventDispatcher->dispatch(new ConfigureMenuEvent('admin_generator_objects_menu', $menu));

        return $menu;
    }

    private function reorderChildren(ItemInterface $menu, array $orderConfig): void
    {
        foreach ($menu->getChildren() as $child) {
            if (!$child->hasChildren()) {
                continue;
            }

            $orderByPriority = $orderConfig[$child->getName()];

            krsort($orderByPriority);

            $childOrder = [];
            foreach ($orderByPriority as $names) {
                $childOrder = array_merge($childOrder, $names);
            }

            $child->reorderChildren($childOrder);
        }
    }
}
