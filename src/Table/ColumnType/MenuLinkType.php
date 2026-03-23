<?php

namespace MichaelKaefer\AdminUiBundle\Table\ColumnType;

use Knp\Menu\MenuItem;
use MichaelKaefer\Table\AbstractColumnType;
use MichaelKaefer\Table\CellView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MenuLinkType extends AbstractColumnType
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'link_attr' => [],
            ])
            ->setAllowedTypes('link_attr', 'array');
    }

    public function getBlockPrefix(): string
    {
        return 'menu_link';
    }

    public function transformData(mixed $data, array $options): array
    {
        if (!$data instanceof MenuItem) {
            throw new \InvalidArgumentException();
        }

        $url = $this->urlGenerator->generate(
            $data->getExtra('routes')[0]['route'],
            $data->getExtra('routes')[0]['parameters'],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return [
            'target' => $url,
            'label' => $this->translator->trans($data->getName(), [], 'MichaelKaeferAdminUiBundle'),
        ];
    }

    public function buildView(CellView $view, array $options): void
    {
        $view->vars['link_attr'] = $options['link_attr'];
    }
}
