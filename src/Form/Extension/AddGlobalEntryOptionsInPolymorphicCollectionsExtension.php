<?php

namespace MichaelKaefer\AdminUiBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\PolymorphicCollectionType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddGlobalEntryOptionsInPolymorphicCollectionsExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield PolymorphicCollectionType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['global_entry_options' => []]);

        $resolver->addNormalizer('entry_options', static function (Options $options, array $value) {
            if (!$options['global_entry_options']) {
                return $value;
            }

            foreach ($value as $k => $v) {
                foreach ($options['global_entry_options'] as $blockEntryOptionKey => $blockEntryOptionValue) {
                    $value[$k][$blockEntryOptionKey] = $blockEntryOptionValue;
                }
            }

            return $options['global_entry_options'] + $value;
        });
    }
}
