<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(TricksRepository $tricksRepository)
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
