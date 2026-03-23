<?php

namespace MichaelKaefer\AdminUiBundle\Table\ColumnType;

use MichaelKaefer\AdminUiBundle\Object\RouteParametersGenerator;
use MichaelKaefer\Table\AbstractColumnType;
use MichaelKaefer\Table\CellView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PreviewType extends AbstractColumnType
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RouteParametersGenerator $routeParametersGenerator,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'preview_attr' => [],
            ])
            ->setAllowedTypes('preview_attr', 'array');
    }

    public function getBlockPrefix(): string
    {
        return 'preview';
    }

    public function transformData(mixed $data, array $options): string
    {
        return $this->urlGenerator->generate(
            'admin_generator_media',
            $this->routeParametersGenerator->generate($data),
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }

    public function buildView(CellView $view, array $options): void
    {
        $view->vars['preview_attr'] = $options['preview_attr'];
    }
}
