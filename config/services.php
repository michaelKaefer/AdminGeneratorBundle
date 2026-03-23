<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use MichaelKaefer\AdminUiBundle\ArgumentResolver\ObjectResolver;
use MichaelKaefer\AdminUiBundle\Controller\CreateController;
use MichaelKaefer\AdminUiBundle\Controller\DeleteController;
use MichaelKaefer\AdminUiBundle\Controller\DetailController;
use MichaelKaefer\AdminUiBundle\Controller\EditController;
use MichaelKaefer\AdminUiBundle\Controller\ListController;
use MichaelKaefer\AdminUiBundle\Controller\MediaController;
use MichaelKaefer\AdminUiBundle\Form\AsFormTypeTypeGuesser;
use MichaelKaefer\AdminUiBundle\Form\DataClassOptionsGenerator;
use MichaelKaefer\AdminUiBundle\Form\Extension\AddGlobalEntryOptionsInPolymorphicCollectionsExtension;
use MichaelKaefer\AdminUiBundle\Form\Extension\AddHtmlDataAttributeContainingTheFormTypeExtension;
use MichaelKaefer\AdminUiBundle\Form\Extension\FilterEntryTypesInPolymorphicCollectionsExtension;
use MichaelKaefer\AdminUiBundle\Form\Extension\MediaPreviewExtension;
use MichaelKaefer\AdminUiBundle\Form\Extension\LabelExtension;
use MichaelKaefer\AdminUiBundle\Form\Extension\UploadMediaExtension;
use MichaelKaefer\AdminUiBundle\Form\PolymorphicCollectionOptionsGenerator;
use MichaelKaefer\AdminUiBundle\Media\Uploader;
use MichaelKaefer\AdminUiBundle\Menu\HeaderLeftMenuBuilder;
use MichaelKaefer\AdminUiBundle\Menu\HeaderRightMenuBuilder;
use MichaelKaefer\AdminUiBundle\Menu\ListItemMenuBuilder;
use MichaelKaefer\AdminUiBundle\Menu\ObjectMenuBuilder;
use MichaelKaefer\AdminUiBundle\Menu\ObjectsMenuBuilder;
use MichaelKaefer\AdminUiBundle\Object\MenuItemFactory;
use MichaelKaefer\AdminUiBundle\Object\ObjectUrlGenerator;
use MichaelKaefer\AdminUiBundle\Object\PathGenerator\ChainPathGenerator;
use MichaelKaefer\AdminUiBundle\Object\PathGenerator\ShortnamePathGenerator;
use MichaelKaefer\AdminUiBundle\Object\PathGeneratorInterface;
use MichaelKaefer\AdminUiBundle\Object\Provider\ChainProvider;
use MichaelKaefer\AdminUiBundle\Object\Provider\DoctrineOrmProvider;
use MichaelKaefer\AdminUiBundle\Object\ProviderInterface;
use MichaelKaefer\AdminUiBundle\Object\RouteParametersGenerator;
use MichaelKaefer\AdminUiBundle\Object\SingleInstanceProvider;
use MichaelKaefer\AdminUiBundle\Object\TranslationIdGenerator;
use MichaelKaefer\AdminUiBundle\Table;
use MichaelKaefer\AdminUiBundle\Twig\VersionExtension;

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

        ->set(TranslationIdGenerator::class)

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
            ->arg(0, param('admin_ui.menu.admin_generator_objects_menu.order'))
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

        ->set(DataClassOptionsGenerator::class)
            ->private()
            ->autowire()

        ->set(PolymorphicCollectionOptionsGenerator::class)
            ->private()
            ->autowire()

        ->set(Uploader::class)
            ->private()
            ->autowire()

        ->set(ObjectUrlGenerator::class)
            ->private()
            ->autowire()
    ;
};
