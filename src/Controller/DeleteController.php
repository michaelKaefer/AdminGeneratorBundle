<?php

namespace MichaelKaefer\AdminUiBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use MichaelKaefer\AdminUiBundle\ArgumentResolver\ObjectResolver;
use MichaelKaefer\AdminUiBundle\Object\PathGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

final class DeleteController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly PathGeneratorInterface $pathGenerator,
    ) {
    }

    #[Route('/{class}/delete', name: 'admin_generator_delete', methods: ['POST'])]
    public function __invoke(
        #[ValueResolver(ObjectResolver::class)] string $class,
        Request $request,
    ): Response {
        $objectManager = $this->managerRegistry->getManagerForClass($class);

        $repository = $objectManager->getRepository($class);

        $object = $repository->find($request->query->all('identifierValues'));

        if (!$object) {
            throw $this->createNotFoundException();
        }

        $objectManager->remove($object);
        $objectManager->flush();

        return $this->redirectToRoute('admin_generator_list', ['class' => $this->pathGenerator->generate($class)]);
    }
}
