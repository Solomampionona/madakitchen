<?php
 
namespace App\Controller;
 
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    
     #[Route('/commande/create-session/{reference}', name:'stripe_create_session')]
     
    public function index(EntityManagerInterface $entityManager,Cart $cart, $reference)
    {
        $product_for_stripe = [];
        $YOUR_DOMAIN = 'https://www.madakitchen.fr/';
 
        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
        //pour sécuriser en cas de commande inexistante
        //si jamais il trouve pas order
        if (!$order) {
            new JsonResponse(['error'=> 'order']);
            //$this->redirectToRoute('order');
        }
        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }
 
        $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice() ,
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];
 
        Stripe::setApiKey('sk_test_51NVKlJDS2hIcRXzQQNQjiqKjtbCmyjZPLn4OIR6cR4fOHgfbEYDd1eN0iAFyZMTMWcivVNmb87vZsiPKgcfe0o0l00OvbywKXk');
 
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [
                $product_for_stripe
            ],
            'payment_method_types' => [
                'card',
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);
        // $order->setStripeSessionId($checkout_session->id);
        // $entityManager->flush();
        // $response = new JsonResponse(['id'=>$checkout_session->id]);
        // return $response;
        $order->setStripeSessionId($checkout_session->id);
      //ici persist n'est pas utile car l'objet est déjà créé
        $entityManager->flush();
        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
        
        return $this->redirect($checkout_session->url, 303);
       
    }
}