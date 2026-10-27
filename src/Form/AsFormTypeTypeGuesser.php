<?php

namespace MichaelKaefer\AdminUiBundle\Form;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\DiscriminatorColumnMapping;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\Mapping\OneToManyAssociationMapping;
use Doctrine\ORM\Mapping\ToOneAssociationMapping;
use Doctrine\Persistence\ManagerRegistry;
use MichaelKaefer\AdminUiBundle\Object\DataClassFormOptionsGenerator;
use MichaelKaefer\AdminUiBundle\Object\PolymorphicCollectionFormOptionsGenerator;
use MichaelKaefer\AdminUiBundle\Object\Translator;
use Symfony\Component\Form\EntryTypeProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PolymorphicCollectionType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

final readonly class AsFormTypeTypeGuesser implements FormTypeGuesserInterface
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private Translator $translator,
    ) {
    }

    public function guessType(string $class, string $property): ?Guess\TypeGuess
    {
        $classMetadata = $this->managerRegistry->getManagerForClass($class)
            ->getClassMetadata($class);

        try {
            $associationMapping = $classMetadata->getAssociationMapping($property);
        } catch (MappingException) {
            return null;
        }

        if (!$associationMapping->isCascadePersist() || !$associationMapping->orphanRemoval) {
            return null;
        }

        if ($associationMapping instanceof ToOneAssociationMapping) {
            return new TypeGuess(
                $associationMapping->targetEntity,
                $this->optionsForDataClassType($associationMapping->targetEntity),
                Guess\Guess::VERY_HIGH_CONFIDENCE,
            );
        }

        if ($associationMapping instanceof OneToManyAssociationMapping) {
            return new TypeGuess(
                PolymorphicCollectionType::class,
                $this->optionsForPolymorphicCollectionType($associationMapping->targetEntity),
                Guess\Guess::VERY_HIGH_CONFIDENCE,
            );
        }

        return new TypeGuess(
            CollectionType::class,
            [
                'entry_type' => $associationMapping->targetEntity,
                'entry_options' => [],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ],
            Guess\Guess::VERY_HIGH_CONFIDENCE,
        );
    }

    public function guessRequired(string $class, string $property): ?Guess\ValueGuess
    {
        return null;
    }

    public function guessMaxLength(string $class, string $property): ?Guess\ValueGuess
    {
        return null;
    }

    public function guessPattern(string $class, string $property): ?Guess\ValueGuess
    {
        return null;
    }

    private function optionsForDataClassType(string $class): array
    {
        return [
            'label' => $this->translator->transClass($class, false),
        ];
    }

    public function optionsForPolymorphicCollectionType(string $class): array
    {
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->managerRegistry->getManagerForClass($class)
            ->getClassMetadata($class);
        $discriminatorMap = $classMetadata->discriminatorMap;
        $discriminatorColumn = $classMetadata->discriminatorColumn;

        return [
            'label' => $this->translator->transClass($class, false),
            'entry_types' => $discriminatorMap,
            'entry_options' => array_map(
                fn(string $v) => [
                    'label' => $this->translator->transClass($v, true),
                ],
                $discriminatorMap,
            ),
            'prototype_options' => array_map(
                fn(string $v) => ['label' => $this->translator->transClass($v, true)],
                $discriminatorMap,
            ),
            'entry_type_provider' => new readonly class($discriminatorMap, $discriminatorColumn) implements EntryTypeProviderInterface
            {
                public function __construct(
                    private array $discriminatorMap,
                    private DiscriminatorColumnMapping $discriminatorColumnMapping,
                ) {
                }

                public function forModelData(mixed $data): int|string
                {
                    return array_search($data::class, $this->discriminatorMap, true);
                }

                public function forSubmittedData(mixed $data): int|string
                {
                    return $data[$this->discriminatorColumnMapping->name];
                }
            },
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ];
    }
}
