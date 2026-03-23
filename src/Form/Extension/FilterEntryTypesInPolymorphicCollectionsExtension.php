<?php

namespace MichaelKaefer\AdminUiBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\PolymorphicCollectionType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterEntryTypesInPolymorphicCollectionsExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield PolymorphicCollectionType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['available_entry_types' => []]);

        foreach (['entry_types', 'entry_options', 'prototype_options', 'prototype_data'] as $o) {
            $resolver->addNormalizer($o, static function (Options $options, array $value) {
                if (!$options['available_entry_types']) {
                    return $value;
                }

                foreach ($value as $k => $v) {
                    if (!in_array($k, $options['available_entry_types'])) {
                        unset($value[$k]);
                    }
                }

                return $value;
            }, true);
        }
    }
}
