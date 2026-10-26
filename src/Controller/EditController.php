<?php

namespace MichaelKaefer\AdminUiBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use MichaelKaefer\AdminUiBundle\ArgumentResolver\ObjectResolver;
use MichaelKaefer\AdminUiBundle\Attribute\SingleInstance;
use MichaelKaefer\AdminUiBundle\Object\RouteParametersGenerator;
use MichaelKaefer\AdminUiBundle\Object\TranslationIdGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

final class EditController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly TranslationIdGenerator $translationIdGenerator,
        private readonly RouteParametersGenerator $routeParametersGenerator,
    ) {
    }

    #[Route('/{class}/edit', name: 'admin_generator_edit', methods: ['GET', 'POST'])]
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

        $form = $this->createForm($class, $object, [
            'attr' => [
                'novalidate' => 'novalidate',
            ]])
            ->add('update', SubmitType::class, [
                'label' => 'Update',
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $objectManager = $this->managerRegistry->getManagerForClass($class);

            $objectManager->persist($object);
            $objectManager->flush();

            if (new \ReflectionClass($class)->getAttributes(SingleInstance::class)) {
                return $this->redirectToRoute(
                    'admin_generator_detail',
                    $this->routeParametersGenerator->generate($object),
                );
            }

            return $this->redirectToRoute(
                'admin_generator_list',
                $this->routeParametersGenerator->generate($class),
                Response::HTTP_SEE_OTHER,
            );
        }

        return $this->render('@MichaelKaeferAdminUi/form.html.twig', [
            'class' => $class,
            'object_name_singular' => $this->translationIdGenerator->generate($class, true),
            'form' => $form,
            'object' => $object,
        ]);
    }
}
