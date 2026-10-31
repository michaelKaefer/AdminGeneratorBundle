<?php

namespace MichaelKaefer\AdminGeneratorBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use MichaelKaefer\AdminGeneratorBundle\ArgumentResolver\ObjectResolver;
use MichaelKaefer\AdminGeneratorBundle\Object\PathGeneratorInterface;
use MichaelKaefer\AdminGeneratorBundle\Object\Translator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

final class CreateController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly PathGeneratorInterface $pathGenerator,
        private readonly Translator $translator,
    ) {
    }

    #[Route('/{class}/create', name: 'admin_generator_create', methods: ['GET', 'POST'])]
    public function __invoke(
        #[ValueResolver(ObjectResolver::class)] string $class,
        Request $request,
    ): Response {
        $object = new ($class)();

        $form = $this->createForm($class, $object, [
            'attr' => [
                'novalidate' => 'novalidate',
            ]])
            ->add('create', SubmitType::class, [
                'label' => 'Create',
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $objectManager = $this->managerRegistry->getManagerForClass($class);

            $objectManager->persist($object);
            $objectManager->flush();

            return $this->redirectToRoute(
                'admin_generator_list',
                ['class' => $this->pathGenerator->generate($class)],
                Response::HTTP_SEE_OTHER,
            );
        }

        return $this->render('@MichaelKaeferAdminGenerator/form.html.twig', [
            'class' => $class,
            'object_name_singular' => $this->translator->transClass($class, true),
            'form' => $form,
        ]);
    }
}
