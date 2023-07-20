<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{   
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager =$entityManager;
    }

    #[Route('/commande/erreur/{stripeSessionId}', name: 'order_cancel')]
    public function index(Cart $cart,$stripeSessionId): Response
    {
    $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSeesionId($stripeSessionId);

    if (!$order || $order->getUser() != $this->getUser()){
        return $this->redirectToRoute('home');
    }
   

    //Envoyer un email à notre client pour lui indiquer l'echec de paiement  
 

    return $this->render('order_cancel/index.html.twig',[
        'order'=> $order 
    ]);
}
}