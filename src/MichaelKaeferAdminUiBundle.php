<?php

namespace MichaelKaefer\AdminUiBundle;

use MichaelKaefer\Table\TableRegistry;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class MichaelKaeferAdminUiBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->scalarNode('theme')->end()
            ->arrayNode('menu')
                    ->children()
                        ->arrayNode('admin_generator_objects_menu')
                            ->scalarPrototype()->end()
                            ->defaultValue([])
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $container->setParameter('admin_ui.menu.admin_generator_objects_menu.order', $config['menu']['admin_generator_objects_menu']);

        $configurator->import('../config/services.php');
    }

    public function prependExtension(ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $theme = 'semantic-html';
        foreach ($container->getExtensionConfig('michael_kaefer_admin_ui') as $config) {
            $theme = $config['theme'] ?? $theme;
        }

        $path = \dirname(__DIR__).'/templates/'.$theme;

        $container->prependExtensionConfig('twig', ['paths' => [$path => 'MichaelKaeferAdminUi']]);
    }
}
