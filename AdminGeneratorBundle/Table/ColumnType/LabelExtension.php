<?php

namespace MichaelKaefer\AdminGeneratorBundle\Table\ColumnType;

use MichaelKaefer\AdminGeneratorBundle\Object\ProviderInterface;
use MichaelKaefer\AdminGeneratorBundle\Object\Translator;
use MichaelKaefer\Table\AbstractColumnTypeExtension;
use MichaelKaefer\Table\ColumnType\ColumnType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\ObjectType;

final class LabelExtension extends AbstractColumnTypeExtension
{
    public function __construct(private readonly Translator $translator, private readonly PropertyInfoExtractorInterface $propertyInfoExtractor, private readonly ProviderInterface $objectProvider)
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
                            return $this->translator->transClass($class, false, true);
                        }
                    }
                }
            } elseif ($type instanceof ObjectType) {
                foreach ($this->objectProvider->provide() as $class) {
                    if ($class === $type->getClassName()) {
                        return $this->translator->transClass($class, false, true);
                    }
                }
            }

            return $this->translator->transProperty($dataClass, (string) $propertyPath, true);
        });
    }
}
