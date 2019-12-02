<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/product", name="product.index")
     */
    public function index(Request $request)             
    {                                                                                                                                                                                                                                                                                                                                                                           
        $products = $this->repository->findAll();
        
        // $pagination = $paginator->paginate($products, $request->query->getInt('page', 1), 10);       
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products
        ]);
    }


     /**
     * @Route("/product/{id}", name="product.show")
     */
    public function show(int $id) {
        $product = $this->repository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('The product does not exist');
        }

        return $this->render('product/show.html.twig', [
            'controller_name' => 'ProductController',
            'product' => $product,  

        ]);

    }
}