<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository){
        $this->productRepository = $productRepository;
    }


    /**
    * @Route("/", name="index")
    */
    public function index()
    {
        $plusRecents = $this->productRepository->findPlusRecents(5);
        $moinsCher = $this->productRepository->findMoinsCher(5);
        return $this->render('index.html.twig', [
        'controller_name' => 'index',
        'plusRecents' => $plusRecents,
        'moinsCher' => $moinsCher
        ]);

    }

}

?>