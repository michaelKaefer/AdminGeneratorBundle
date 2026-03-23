<?php

namespace MichaelKaefer\AdminUiBundle\Form\Extension;

use MichaelKaefer\AdminUiBundle\Object\ProviderInterface;
use MichaelKaefer\AdminUiBundle\Object\TranslationIdGenerator;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class LabelExtension extends AbstractTypeExtension
{
    public function __construct(private readonly TranslationIdGenerator $translationIdGenerator, private readonly ProviderInterface $objectProvider)
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
                        return new TranslatableMessage(
                            $this->translationIdGenerator->generate($options['data_class'], true),
                            [],
                            'MichaelKaeferAdminUiBundle',
                        );
                    }
                }
            } elseif ($options['class'] ?? null) {
                foreach ($this->objectProvider->provide() as $class) {
                    if ($class === $options['class']) {
                        return new TranslatableMessage(
                            $this->translationIdGenerator->generate($options['class'], true),
                            [],
                            'MichaelKaeferAdminUiBundle',
                        );
                    }
                }
            } elseif ($options['entry_type'] ?? null) {
            } elseif ($options['entry_types'] ?? null) {
            }

            return null;
        });
    }
}