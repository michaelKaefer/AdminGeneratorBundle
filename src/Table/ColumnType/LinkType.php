<?php

namespace MichaelKaefer\AdminUiBundle\Table\ColumnType;

use MichaelKaefer\AdminUiBundle\Object\RouteParametersGenerator;
use MichaelKaefer\Table\AbstractColumnType;
use MichaelKaefer\Table\CellView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LinkType extends AbstractColumnType
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
                'route' => 'admin_generator_edit',
                'link_attr' => [],
            ])
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('link_attr', 'array');
    }

    public function getBlockPrefix(): string
    {
        return 'link';
    }

    public function transformData(mixed $data, array $options): array
    {
        $url = $this->urlGenerator->generate(
            $options['route'],
            $this->routeParametersGenerator->generate($data),
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return [
            'target' => $url,
            'label' => (string) $data,
        ];
    }

    public function buildView(CellView $view, array $options): void
    {
        $view->vars['link_attr'] = $options['link_attr'];
    }
}
