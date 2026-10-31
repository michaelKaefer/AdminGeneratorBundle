<?php

namespace MichaelKaefer\AdminGeneratorBundle\Media;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class Uploader
{
    public function __construct(
        #[Target('default.storage')] private FilesystemOperator $storage,
        private SluggerInterface $slugger,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function upload(UploadedFile $file): array
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename)->lower();
        $extension = $file->guessExtension() ?? 'bin';
        $storageFilename = sprintf('%s-%s.%s', $safeFilename, uniqid(), $extension);

        $stream = fopen($file->getPathname(), 'r');
        $this->storage->writeStream($storageFilename, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }

        return [
            'filename' => $storageFilename,
            'originalFilename' => $file->getClientOriginalName(),
            'mimeType' => $file->getMimeType() ?? 'application/octet-stream',
            'size' => $file->getSize() ?? 0,
        ];
    }

    public function delete(MediaInterface $media): void
    {
        if ($this->storage->fileExists($media->getFilename())) {
            $this->storage->delete($media->getFilename());
        }
    }

    public function getPublicUrl(Media $media): string
    {
        return $this->urlGenerator->generate('media_show', [
            'id' => $media->getId(),
            'filename' => $media->getFilename(),
        ]);
    }
}
