<?php

namespace MichaelKaefer\AdminUiBundle\Form;

use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\Mapping\OneToManyAssociationMapping;
use Doctrine\ORM\Mapping\ToOneAssociationMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PolymorphicCollectionType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

final readonly class AsFormTypeTypeGuesser implements FormTypeGuesserInterface
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private DataClassOptionsGenerator $dataClassOptionsGenerator,
        private PolymorphicCollectionOptionsGenerator $polymorphicCollectionOptionsGenerator,
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
                $this->dataClassOptionsGenerator->generate($associationMapping->targetEntity),
                Guess\Guess::VERY_HIGH_CONFIDENCE,
            );
        }

        if ($associationMapping instanceof OneToManyAssociationMapping) {
            return new TypeGuess(
                PolymorphicCollectionType::class,
                $this->polymorphicCollectionOptionsGenerator->generate($associationMapping->targetEntity),
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
}
