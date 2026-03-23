<?php

namespace MichaelKaefer\AdminUiBundle\Table\ColumnType;

use MichaelKaefer\AdminUiBundle\Object\ProviderInterface;
use MichaelKaefer\AdminUiBundle\Object\TranslationIdGenerator;
use MichaelKaefer\Table\AbstractColumnTypeExtension;
use MichaelKaefer\Table\ColumnType\BooleanType;
use MichaelKaefer\Table\ColumnType\ColumnType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\Type\WrappingTypeInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class LabelExtension extends AbstractColumnTypeExtension
{
    public function __construct(private readonly PropertyInfoExtractorInterface $propertyInfoExtractor, private readonly TranslatorInterface $translator, private readonly TranslationIdGenerator $translationIdGenerator,  private readonly ProviderInterface $objectProvider)
    {
    }

    public static function getExtendedTypes(): iterable
    {
        yield ColumnType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->addNormalizer('label', function (Options $options, mixed $value): mixed {
            if (null !== $value) {
                return $value;
            }

            $dataClass = $options['data_class'] ?? null;
            $propertyPath = $options['property_path'] ?? null;

            if (!$dataClass || !$propertyPath) {
                return null;
            }

            $type = $this->propertyInfoExtractor->getType($dataClass, (string) $propertyPath);
            if ($type instanceof CollectionType) {
                $innerType = $type->getCollectionValueType();
                if ($innerType instanceof ObjectType) {
                    foreach ($this->objectProvider->provide() as $class) {
                        if ($class === $innerType->getClassName()) {
                            return $this->translator->trans(
                                $this->translationIdGenerator->generate($class, false),
                                [],
                                'MichaelKaeferAdminUiBundle',
                            );
                        }
                    }
                }
            } elseif ($type instanceof ObjectType) {
                foreach ($this->objectProvider->provide() as $class) {
                    if ($class === $type->getClassName()) {
                        return $this->translator->trans(
                            $this->translationIdGenerator->generate($class, false),
                            [],
                            'MichaelKaeferAdminUiBundle',
                        );
                    }
                }
            }

            return $this->translator->trans(
                $this->translationIdGenerator->generateForProperty($dataClass, (string) $propertyPath),
                [],
                'MichaelKaeferAdminUiBundle',
            );
        });
    }
}
