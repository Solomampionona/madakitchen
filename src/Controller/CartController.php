<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{   
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager =$entityManager;
    }

    #[Route('/mon-panier', name: 'cart',schemes: ['https'])]
    public function index(Cart $cart): Response
    {   

        return $this->render('cart/index.html.twig',[
            'cart' => $cart->getFull()
        ]);
    }

    #[Route('/cart/add/{id}', name: 'add_to_cart',schemes: ['https'])]
    public function add(Cart $cart,$id): Response
    {
        $cart->add($id);

        return $this->redirectToRoute('cart');
    }
    

    #[Route('/cart/remove', name: 'remove_my_cart',schemes: ['https'])]
    public function remove(Cart $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('products');
    }

    #[Route('/cart/delete{id}', name: 'delete_to_cart',schemes: ['https'])]
    public function delete(Cart $cart,$id): Response
    {
        $cart->delete($id);

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/decrease{id}', name: 'decrease_to_cart',schemes: ['https'])]
    public function decrease(Cart $cart,$id): Response
    {
        $cart->decrease($id);

        return $this->redirectToRoute('cart');
    }


}
