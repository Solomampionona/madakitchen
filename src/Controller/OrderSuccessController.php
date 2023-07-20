<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{   
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager =$entityManager;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'order_validate')]
    public function index(Cart $cart ,$stripeSessionId ): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSeesionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }
        if (!$order->getIsPaid())
        {   
            //vider la session 'cart'
             $cart->remove();
        
             // modifier le statut IsPaid de la commande en mettant 1
            $order->setIsPaid(1);
            $this->entityManager->flush();

        }
       
        //Envoyer un email à notre client pour lui confirmer sa commande  
     

        return $this->render('order_validate/index.html.twig',[
            'order'=> $order 
        ]);
    }
}
