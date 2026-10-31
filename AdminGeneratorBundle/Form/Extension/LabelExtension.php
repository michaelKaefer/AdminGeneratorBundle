<?php

namespace MichaelKaefer\AdminGeneratorBundle\Form\Extension;

use MichaelKaefer\AdminGeneratorBundle\Object\ProviderInterface;
use MichaelKaefer\AdminGeneratorBundle\Object\Translator;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LabelExtension extends AbstractTypeExtension
{
    public function __construct(private readonly Translator $translator, private readonly ProviderInterface $objectProvider)
    {
    }

    public static function getExtendedTypes(): iterable
    {
        yield FormType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->addNormalizer('label', function (Options $options, mixed $value): mixed {
            if (null !== $value) {
                return $value;
            }

            if ($options['data_class'] ?? null) {
                foreach ($this->objectProvider->provide() as $class) {
                    if ($class === $options['data_class']) {
                        return $this->translator->transClass($options['data_class'], true);
                    }
                }
            } elseif ($options['class'] ?? null) {
                foreach ($this->objectProvider->provide() as $class) {
                    if ($class === $options['class']) {
                        return $this->translator->transClass($options['class'], true);
                    }
                }
            } elseif ($options['entry_type'] ?? null) {
            } elseif ($options['entry_types'] ?? null) {
            }

            return null;
        });
    }
}