<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController

{   
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager=$entityManager;
    }
    #[Route('/inscription', name: 'register')]
    public function index(Request $request,UserPasswordHasherInterface $encoder)
    {   
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class,$user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
        $user=$form->getData(); 
        
        $search_email=$this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
        if (!$search_email){
        $password = $encoder->hashPassword($user ,$user->getPassword());
        $user->setPassword($password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $mail =new Mail();
        $content="Bonjour".''.$user->getFirstname()." <br/>Bienvenue sur le site de MadaKitchen 
        cuisine faites avec amour.<br/><br/>";
        $mail->send($user->getEmail(),$user->getFirstname(),'Bienvenue sur MadaKitchen',$content);
        $notification ="Votre inscription s'est correctement déroulée.Vous pouvez dès àpésent vous connecter à
        votre compte";
        }else{

        $notification="L'email que avez renseigner existe déja.";
        }
        
    
        }
        return $this->render('register/index.html.twig',[
            'form'=> $form ->createView(),
            'notification'=>$notification
        ]);
    }
}

