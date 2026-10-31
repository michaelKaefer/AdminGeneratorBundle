<?php

namespace MichaelKaefer\AdminGeneratorBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use MichaelKaefer\AdminGeneratorBundle\ArgumentResolver\ObjectResolver;
use MichaelKaefer\AdminGeneratorBundle\Attribute\SingleInstance;
use MichaelKaefer\AdminGeneratorBundle\Object\Translator;
use MichaelKaefer\Table\TableFactory;
use MichaelKaefer\Table\TableViewFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

final class ListController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly Translator $translator,
        private readonly TableFactory $tableFactory,
        private readonly TableViewFactory $tableViewFactory,
        private readonly PaginatorInterface $paginator,
    ) {
    }

    #[Route('/{class}', name: 'admin_generator_list', methods: ['GET'])]
    public function __invoke(
        #[ValueResolver(ObjectResolver::class)] string $class,
        Request $request,
    ): Response {
        if (new \ReflectionClass($class)->getAttributes(SingleInstance::class)) {
            throw $this->createNotFoundException();
        }

        $repository = $this->managerRegistry->getManagerForClass($class)
            ->getRepository($class);

        $query = $repository->createQueryBuilder('o')->getQuery();

        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10,
        );

        $table = $this->tableViewFactory->create(
            $this->tableFactory->create($class),
            $query->toIterable(),
        );

        return $this->render('@MichaelKaeferAdminGenerator/list.html.twig', [
            'class' => $class,
            'object_name_singular' => $this->translator->transClass($class, true),
            'list' => $repository->findAll(),
            'table' => $table,
            'pagination' => $pagination,
        ]);
    }
}
