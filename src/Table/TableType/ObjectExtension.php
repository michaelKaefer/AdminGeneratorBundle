<?php

namespace MichaelKaefer\AdminUiBundle\Table\TableType;

use MichaelKaefer\AdminUiBundle\Form\Type\DeleteType;
use MichaelKaefer\AdminUiBundle\Menu\ListItemMenuBuilder;
use MichaelKaefer\AdminUiBundle\Object\ObjectUrlGenerator;
use MichaelKaefer\AdminUiBundle\Object\ProviderInterface;
use MichaelKaefer\AdminUiBundle\Object\Translator;
use MichaelKaefer\AdminUiBundle\Table\ColumnType\FormType;
use MichaelKaefer\AdminUiBundle\Table\ColumnType\LinkType;
use MichaelKaefer\AdminUiBundle\Table\ColumnType\MenuLinkType;
use MichaelKaefer\Table\AbstractTableTypeExtension;
use MichaelKaefer\Table\ColumnType\CollectionType;
use MichaelKaefer\Table\TableBuilder;
use Symfony\Component\Form\FormFactoryInterface;

class ObjectExtension extends AbstractTableTypeExtension
{
    public function __construct(private readonly ObjectUrlGenerator $objectUrlGenerator, private readonly FormFactoryInterface $formFactory, private readonly ListItemMenuBuilder $listItemMenuBuilder, private readonly Translator $translator,  private readonly ProviderInterface $objectProvider)
    {
    }

    public function getExtendedTypes(): iterable
    {
        foreach ($this->objectProvider->provide() as $class) {
            yield $class;
        }
    }

    public function buildTable(TableBuilder $builder, array $options): void
    {
        if (!$options['data_class']) {
            return;
        }

        if (!$this->objectProvider->has($options['data_class'])) {
            return;
        }

        $formFactory = $this->formFactory;
        $objectUrlGenerator = $this->objectUrlGenerator;
        $objectProvider = $this->objectProvider;
        $listItemMenu = $this->listItemMenuBuilder;

        $builder
            ->add('object', LinkType::class, [
                'label' => $this->translator->transClass($options['data_class'], true, true),
                'priority' => 10,
            ])
            ->add('links', CollectionType::class, [
                'transformer' => function (mixed $data) use ($objectProvider, $listItemMenu) {
                    if (!is_object($data) || !$objectProvider->has($data::class)) {
                        return $data;
                    }

                    return $listItemMenu->build(['item' => $data])->getIterator();
                },
                'entry_type' => MenuLinkType::class,
            ])
            ->add('delete', FormType::class, [
                'transformer' => function (mixed $data) use ($formFactory, $objectUrlGenerator) {
                    return $formFactory->createBuilder()
                        ->setAction($objectUrlGenerator->generate('admin_generator_delete', $data))
                        ->add('delete', DeleteType::class)
                        ->getForm()
                        ->createView();
                }
            ]);
    }
}
