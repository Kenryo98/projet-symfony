<?php

namespace App\Controller;

use App\Entity\Command;
use App\Form\CommandType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{

    private $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }
    

    /**
     * @Route("/panier", name="panier.index")
     */
    public function index(Request $request, SessionInterface $session) {

        $command = new Command();
        $commandForm = $this->createForm(CommandType::class, $command);
        $panier = $session->get('panier', []);
        $manager = $this->getDoctrine()->getManager();

        $products = [];
        $prixTotal = 0;
        foreach($panier as $id => $quantity) {
            $product = $this->repository->find($id);
            $products[$id] = [
                'produit' => $product,
                'quantite' => $quantity];
            $prixTotal += $product->getPrice();
        }

        $commandForm->handleRequest($request);

        if ($commandForm->isSubmitted()) {
            $command->setCreatedAt(new \DateTime('now'));
            
            foreach($panier as $id => $quantity) {
                $p =$this->repository->find($id);
                $command->addProduct($p);
            }
            $session->clear();
            $manager->persist($command);
            $manager->flush();

            return $this->redirectToRoute('panier.index');
        }


        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'products' => $products,
            'prixTotal' => $prixTotal,
            'commandForm' => $commandForm->createView()
        ]);
    }
    
    /**
     * @Route("/panier/add/{id}", name="panier.add")
     */
    public function add(int $id, SessionInterface $session)
    {
        $product = $this->repository->find($id);

        if (!$product) {
            return $this->json('nok', 404);
        } else {
            $panier = $session->get('panier', []);
        
            $panier[$id] = 1;
            // ...
            $session->set('panier', $panier);

            $this->addFlash('success', "Le produit {$product->getName()} a bien été ajouté.");
            return $this->json('ok', 200);
        }
    }

    /**
     * @Route("/panier/delete/{id}", name="panier.delete")
     */
    public function delete(int $id, Request $request, SessionInterface $session) {
        $product = $this->repository->find($id);
        $panier = $session->get('panier', []);
        $csrfToken = $request->request->get('token');

        if (!$product) {
            throw $this->createNotFoundException("Le produit n'existe pas !");
        }
        if (!isset($panier)) {
            $this->addFlash('error', "Le {$product->getName()} n'existe pas !");
        } else {
            if(isset($panier[$id])) {
                if ($this->isCsrfTokenValid('delete-item', $csrfToken)) {
                    unset($panier[$id]);
                    $session->set('panier', $panier);
                    $this->addFlash('success', "Le produit {$product->getName()} a bien été supprimé !");
                } else {
                    throw $this->InvalidCsrfTokenException('Token invalide');
                }    
            }
            
        }
        return $this->redirectToRoute('panier.index');
    }

}