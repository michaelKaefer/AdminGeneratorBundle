<?php

namespace MichaelKaefer\AdminUiBundle\Form\Extension;

use MichaelKaefer\AdminUiBundle\Media\MediaInterface;
use MichaelKaefer\AdminUiBundle\Media\Uploader;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadMediaExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly Uploader $uploader,
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
            FormEvents::POST_SUBMIT,
            $this->upload(...),
        );
    }

    private function upload(FormEvent $event): void
    {
        $form = $event->getForm();

        if (!$form->isValid()) {
            return;
        }

        $media = $form->getData();

        if (!$media instanceof MediaInterface) {
            return;
        }

        if (!$form->has('file')) {
            throw new \InvalidArgumentException('The media form must have a field named "file".');
        }

        $file = $form->get('file')->getData();

        if (!$file instanceof UploadedFile) {
            return;
        }

        $result = $this->uploader->upload($file);

        $media->setFilename($result['filename']);
        $media->setOriginalFilename($result['originalFilename']);
        $media->setMimeType($result['mimeType']);
        $media->setSize($result['size']);
    }
}
