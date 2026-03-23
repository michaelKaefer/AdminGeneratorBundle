<?php

namespace MichaelKaefer\AdminUiBundle\Form;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\DiscriminatorColumnMapping;
use Doctrine\Persistence\ManagerRegistry;
use MichaelKaefer\AdminUiBundle\Object\TranslationIdGenerator;
use Symfony\Component\Form\EntryTypeProviderInterface;
use Symfony\Component\Translation\TranslatableMessage;

final readonly class PolymorphicCollectionOptionsGenerator
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private TranslationIdGenerator $translationIdGenerator
    ) {
    }

    public function generate(string $class): array
    {
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->managerRegistry->getManagerForClass($class)
            ->getClassMetadata($class);
        $discriminatorMap = $classMetadata->discriminatorMap;
        $discriminatorColumn = $classMetadata->discriminatorColumn;

        return [
            'label' => new TranslatableMessage(
                $this->translationIdGenerator->generate($class, false),
                [],
                'MichaelKaeferAdminUiBundle',
            ),
            'entry_types' => $discriminatorMap,
            'entry_options' => array_map(
                fn(string $v) => [
                    'label' => new TranslatableMessage(
                        $this->translationIdGenerator->generate($v, true),
                        [],
                        'MichaelKaeferAdminUiBundle',
                    ),
                ],
                $discriminatorMap,
            ),
            'prototype_options' => array_map(fn(string $v) => ['label' => new TranslatableMessage(
                $this->translationIdGenerator->generate($v, true),
                [],
                'MichaelKaeferAdminUiBundle',
            )], $discriminatorMap),
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
