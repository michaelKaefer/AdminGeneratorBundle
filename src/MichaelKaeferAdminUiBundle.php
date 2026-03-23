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

//    public function prependExtension(ContainerConfigurator $configurator, ContainerBuilder $container): void
//    {
//        $container->prependExtensionConfig('michael_kaefer_table', [
//            'themes' => [
//                '@MichaelKaeferAdminUi/table/theme.html.twig',
//                '@MichaelKaeferTable/table.html.twig',
//            ],
//        ]);
//    }

    public function prependExtension(ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $path = \dirname(new \ReflectionClass(TableRegistry::class)->getFileName()).'/../templates';
        $path = '/home/michael/Meine Dateien/admin-generator/admin-ui-bundle/templates/semantic-html';
        $path = '/home/michael/Meine Dateien/admin-generator/admin-ui-bundle/templates/bootstrap_6';
        $path = '/home/michael/Meine Dateien/admin-generator/admin-ui-bundle/templates/bootstrap_5';

        $container->prependExtensionConfig('twig', ['paths' => [$path => 'MichaelKaeferAdminUi']]);
    }
}
