<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
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

    #[Route('/commande/merci/{stripeSessionId}', name: 'order_validate',schemes: ['https'])]
    public function index(Cart $cart,$stripeSessionId ): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId'=>$stripeSessionId]);
        // si l'order n'esxiste pas et le user est#de la commande
        if (!$order || $order->getUser() != $this->getUser()){
            //donc redirection sur homepage
            return $this->redirectToRoute('home');
        }
        //seulement si ma commande est non payéé
        if (!$order->getIsPaid())
        {   
            //vider la session 'cart'
             $cart->remove();
        
             // modifier le statut IsPaid de la commande en mettant 1
            $order->setIsPaid(1);
            $this->entityManager->flush();

            //Envoyer un email à notre client pour lui confirmer sa commande
            $mail =new Mail();
            $content="Bonjour".''.$order->getUser()->getFirstname().
            " <br/>Merci pour votre commande et à bientôt!.<br/><br/>";
            $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),
            'Votre commande sur MadaKitchen est bien validée',$content);
        }
          
        return $this->render('order_success/index.html.twig',[
            'order'=> $order 
        ]);
    }
}
