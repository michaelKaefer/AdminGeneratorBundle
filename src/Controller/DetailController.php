<?php

namespace MichaelKaefer\AdminUiBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use MichaelKaefer\AdminUiBundle\ArgumentResolver\ObjectResolver;
use MichaelKaefer\AdminUiBundle\Object\TranslationIdGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final class DetailController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ManagerRegistry $managerRegistry,
        private readonly TranslationIdGenerator $translationIdGenerator,
    ) {
    }

    #[Route('/{class}/detail', name: 'admin_generator_detail', methods: ['GET'])]
    public function __invoke(
        #[ValueResolver(ObjectResolver::class)] string $class,
        Request $request,
    ): Response {
        $repository = $this->managerRegistry->getManagerForClass($class)
            ->getRepository($class);

        return $this->render('@MichaelKaeferAdminUi/detail.html.twig', [
            'class' => $class,
            'object_name_singular' => $this->translationIdGenerator->generate($class, true),
            'object' => $object = $repository->find($request->query->all('identifierValues')),
            'detail' => $this->serializer->normalize($object, null, [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, ?string $format, array $context): string {
                    return $object->__toString();
                },
            ]),
        ]);
    }
}
