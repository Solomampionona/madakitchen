<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AproposController extends AbstractController
{
    #[Route('/a-propos', name: 'apropos',schemes: ['https'])]
    public function index(): Response
    {
        return $this->render('apropos/index.html.twig', );
    }
}
