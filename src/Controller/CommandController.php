<?php

namespace App\Controller;


use App\Repository\CommandRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandController extends AbstractController
{
    private $repository;

    public function __construct(CommandRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/command", name="command.index")
     */
    public function index(Request $request)             
    {                                                                                                                                                                                                                                                                                                                                                                           
        $commands = $this->repository->findAll();
        
        return $this->render('command/index.html.twig', [
            'controller_name' => 'CommandController',
            'commands' => $commands
        ]);
    }


     /**
     * @Route("/command/{id}", name="command.show")
     */
    public function show(int $id) {
        $command = $this->repository->find($id);

        if (!$command) {
            throw $this->createNotFoundException('The command does not exist');
        }

        $products = $command->getProducts();

        $prixTotal = 0;
        foreach($products as $product) {
            $prixTotal += $product->getPrice();
        }
        return $this->render('command/show.html.twig', [
            'controller_name' => 'CommandController',
            'command' => $command,
            'products' => $products,
            'prixTotal' => $prixTotal

        ]);

    }
}