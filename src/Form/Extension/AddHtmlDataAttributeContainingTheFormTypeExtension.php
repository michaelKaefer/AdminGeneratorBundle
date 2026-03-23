<?php

namespace MichaelKaefer\AdminUiBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\FormType;

use function Symfony\Component\String\u;

class AddHtmlDataAttributeContainingTheFormTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield FormType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $resolvedType = $form->getConfig()->getType();

        $type = $resolvedType->getInnerType();

        $view->vars['row_attr']['data-form-type'] = u(new \ReflectionClass($type)->getShortName())
            ->beforeLast('Type')
            ->snake()
            ->toString();
    }
}