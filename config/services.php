<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use MichaelKaefer\AdminGeneratorBundle\ArgumentResolver\ObjectResolver;
use MichaelKaefer\AdminGeneratorBundle\Controller\CreateController;
use MichaelKaefer\AdminGeneratorBundle\Controller\DeleteController;
use MichaelKaefer\AdminGeneratorBundle\Controller\DetailController;
use MichaelKaefer\AdminGeneratorBundle\Controller\EditController;
use MichaelKaefer\AdminGeneratorBundle\Controller\ListController;
use MichaelKaefer\AdminGeneratorBundle\Controller\MediaController;
use MichaelKaefer\AdminGeneratorBundle\Form\AsFormTypeTypeGuesser;
use MichaelKaefer\AdminGeneratorBundle\Form\Extension\AddGlobalEntryOptionsInPolymorphicCollectionsExtension;
use MichaelKaefer\AdminGeneratorBundle\Form\Extension\AddHtmlDataAttributeContainingTheFormTypeExtension;
use MichaelKaefer\AdminGeneratorBundle\Form\Extension\FilterEntryTypesInPolymorphicCollectionsExtension;
use MichaelKaefer\AdminGeneratorBundle\Form\Extension\LabelExtension;
use MichaelKaefer\AdminGeneratorBundle\Form\Extension\MediaPreviewExtension;
use MichaelKaefer\AdminGeneratorBundle\Form\Extension\UploadMediaExtension;
use MichaelKaefer\AdminGeneratorBundle\Media\Uploader;
use MichaelKaefer\AdminGeneratorBundle\Menu\HeaderLeftMenuBuilder;
use MichaelKaefer\AdminGeneratorBundle\Menu\HeaderRightMenuBuilder;
use MichaelKaefer\AdminGeneratorBundle\Menu\ListItemMenuBuilder;
use MichaelKaefer\AdminGeneratorBundle\Menu\ObjectMenuBuilder;
use MichaelKaefer\AdminGeneratorBundle\Menu\ObjectsMenuBuilder;
use MichaelKaefer\AdminGeneratorBundle\Object\MenuItemFactory;
use MichaelKaefer\AdminGeneratorBundle\Object\ObjectUrlGenerator;
use MichaelKaefer\AdminGeneratorBundle\Object\PathGenerator\ChainPathGenerator;
use MichaelKaefer\AdminGeneratorBundle\Object\PathGenerator\ShortnamePathGenerator;
use MichaelKaefer\AdminGeneratorBundle\Object\PathGeneratorInterface;
use MichaelKaefer\AdminGeneratorBundle\Object\Provider\ChainProvider;
use MichaelKaefer\AdminGeneratorBundle\Object\Provider\DoctrineOrmProvider;
use MichaelKaefer\AdminGeneratorBundle\Object\ProviderInterface;
use MichaelKaefer\AdminGeneratorBundle\Object\RouteParametersGenerator;
use MichaelKaefer\AdminGeneratorBundle\Object\SingleInstanceProvider;
use MichaelKaefer\AdminGeneratorBundle\Object\Translator;
use MichaelKaefer\AdminGeneratorBundle\Table;
use MichaelKaefer\AdminGeneratorBundle\Twig\VersionExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services()
        ->defaults()
        ->private()
        ->instanceof(ProviderInterface::class)->tag('admin_generator.object_provider')
        ->instanceof(PathGeneratorInterface::class)->tag('admin_generator.path_generator')
    ;

    $services
        ->set(Table\ColumnType\FormType::class)
            ->tag('table.column_type')

        ->set(Table\ColumnType\LinkType::class)
            ->autowire()
            ->tag('table.column_type')

        ->set(Table\ColumnType\MenuLinkType::class)
            ->autowire()
            ->tag('table.column_type')

        ->set(Table\ColumnType\PreviewType::class)
            ->autowire()
            ->tag('table.column_type')

        ->set(Table\ColumnType\LabelExtension::class)
            ->autowire()
            ->tag('table.column_type_extension')

        ->set(Table\TableType\ObjectExtension::class)
            ->autowire()
            ->tag('table.table_type_extension')

        ->set(ObjectResolver::class)
            ->autowire()
            ->tag('controller.argument_value_resolver')

        ->set(DoctrineOrmProvider::class)
            ->autowire()

        ->set(ChainProvider::class)
            ->autowire()

        ->alias(ProviderInterface::class, ChainProvider::class)

        ->set(ShortnamePathGenerator::class)
            ->private()
            ->autowire()

        ->set(ChainPathGenerator::class)
            ->autowire()

        ->alias(PathGeneratorInterface::class, ChainPathGenerator::class)

        ->set(VersionExtension::class)
            ->tag('twig.extension')

        ->set(AsFormTypeTypeGuesser::class)
            ->autowire()
            ->tag('form.type_guesser')

        ->set(ObjectsMenuBuilder::class)
            ->arg(0, param('admin_generator.menu.admin_generator_objects_menu.order'))
            ->autowire()
            ->tag('knp_menu.menu_builder', [
                'method' => 'build',
                'alias' => 'admin_generator_objects_menu',
            ])

        ->set(HeaderLeftMenuBuilder::class)
            ->autowire()
            ->tag('knp_menu.menu_builder', [
                'method' => 'build',
                'alias' => 'admin_generator_header_left_menu',
            ])

        ->set(HeaderRightMenuBuilder::class)
            ->autowire()
            ->tag('knp_menu.menu_builder', [
                'method' => 'build',
                'alias' => 'admin_generator_header_right_menu',
            ])

        ->set(ObjectMenuBuilder::class)
            ->autowire()
            ->tag('knp_menu.menu_builder', [
                'method' => 'build',
                'alias' => 'admin_generator_object_menu',
            ])

        ->set(ListItemMenuBuilder::class)
            ->autowire()
            ->tag('knp_menu.menu_builder', [
                'method' => 'build',
                'alias' => 'admin_generator_list_item_menu',
            ])

        ->set(CreateController::class)
            ->autowire()
            ->autoconfigure()
            ->tag('controller.service_arguments')

        ->set(ListController::class)
            ->autowire()
            ->autoconfigure()
            ->tag('controller.service_arguments')

        ->set(DetailController::class)
            ->autowire()
            ->autoconfigure()
            ->tag('controller.service_arguments')

        ->set(MediaController::class)
            ->autowire()
            ->autoconfigure()
            ->tag('controller.service_arguments')

        ->set(EditController::class)
            ->autowire()
            ->autoconfigure()
            ->tag('controller.service_arguments')

        ->set(DeleteController::class)
            ->autowire()
            ->autoconfigure()
            ->tag('controller.service_arguments')

        ->set(AddHtmlDataAttributeContainingTheFormTypeExtension::class)
            ->autowire()
            ->tag('form.type_extension')

        ->set(LabelExtension::class)
            ->autowire()
            ->tag('form.type_extension')

        ->set(UploadMediaExtension::class)
            ->autowire()
            ->tag('form.type_extension')

        ->set(MediaPreviewExtension::class)
            ->autowire()
            ->tag('form.type_extension')

        ->set(FilterEntryTypesInPolymorphicCollectionsExtension::class)
            ->private()
            ->autowire()
            ->tag('form.type_extension')

        ->set(AddGlobalEntryOptionsInPolymorphicCollectionsExtension::class)
            ->private()
            ->autowire()
            ->tag('form.type_extension')

        ->set(RouteParametersGenerator::class)
            ->private()
            ->autowire()

        ->set(MenuItemFactory::class)
            ->private()
            ->autowire()

        ->set(SingleInstanceProvider::class)
            ->private()
            ->autowire()

        ->set(Uploader::class)
            ->private()
            ->autowire()

        ->set(ObjectUrlGenerator::class)
            ->private()
            ->autowire()

        ->set(Translator::class)
            ->private()
            ->autowire()
    ;
};
