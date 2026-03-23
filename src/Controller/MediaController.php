<?php

namespace MichaelKaefer\AdminUiBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use MichaelKaefer\AdminUiBundle\ArgumentResolver\ObjectResolver;
use MichaelKaefer\AdminUiBundle\Media\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class MediaController extends AbstractController
{
    public function __construct(
        #[Target('default.storage')] private readonly FilesystemOperator $storage,
        private readonly ManagerRegistry $managerRegistry,
    ) {
    }

    #[Route('/{class}/media', name: 'admin_generator_media', methods: ['GET'])]
    public function __invoke(
        #[ValueResolver(ObjectResolver::class)] string $class,
        Request $request,
    ): Response {
        $repository = $this->managerRegistry->getManagerForClass($class)
            ->getRepository($class);

        /** @var MediaInterface $object */
        $object = $repository->find($request->query->all('identifierValues'));

        $stream = $this->storage->readStream($object->getFilename());

        return new StreamedResponse(
            function () use ($stream) {
                fpassthru($stream);
            },
            200,
            [
                'Content-Type' => $object->getMimeType(),
                'Content-Disposition' => sprintf('inline; filename="%s"', $object->getOriginalFilename()),
            ],
        );
    }
}
