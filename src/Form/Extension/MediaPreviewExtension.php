<?php

namespace MichaelKaefer\AdminUiBundle\Form\Extension;

use Doctrine\Persistence\ManagerRegistry;
use MichaelKaefer\AdminUiBundle\Media\MediaInterface;
use MichaelKaefer\AdminUiBundle\Object\PathGeneratorInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class MediaPreviewExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly PathGeneratorInterface $pathGenerator,
        private readonly ManagerRegistry $managerRegistry,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public static function getExtendedTypes(): iterable
    {
        yield FormType::class;
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            $this->preview(...),
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $dataClass = $form->getConfig()->getDataClass();

        if (!is_subclass_of($dataClass, MediaInterface::class)) {
            return;
        }

        $view->vars['attr']['data-preview'] = 'true';
    }

    private function preview(FormEvent $event): void
    {
        $media = $event->getData();

        if (!$media instanceof MediaInterface) {
            return;
        }

        $url = $this->urlGenerator->generate('admin_generator_media', [
            'class' => $this->pathGenerator->generate($media::class),
            'identifierValues' => $this->managerRegistry->getManagerForClass($media::class)
                ->getClassMetadata($media::class)->getIdentifierValues($media),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $event->getForm()->add('preview', HiddenType::class, [
            'mapped' => false,
            'disabled' => true,
            'data' => $url,
            'attr' => [
                'data-mime-type' => $media->getMimeType(),
            ],
        ]);
    }
}
