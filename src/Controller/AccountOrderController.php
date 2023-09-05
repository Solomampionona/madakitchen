<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{   
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)

    {
        $this->entityManager= $entityManager;
    }
    #[Route('/compte/mes-commandes', name: 'account_order',schemes: ['https'])]
    public function index(): Response
    {
        $orders=$this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());
        return $this->render('account/order.html.twig',[
            'orders'=> $orders
        ]);

    }
    #[Route('/compte/mes-commandes/{reference}', name: 'account_order_show')]
    public function show($reference): Response
    {
        $order=$this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('accoubt_order');
        }
        return $this->render('account/order_show.html.twig',[
            'order'=> $order
        ]);

    }
}
