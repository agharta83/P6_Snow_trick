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
        $tricks = $tricksRepository->findBy([], ['created_at' => 'DESC'], 6, 0);

        return $this->render('main/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }

    /**
     * @Route("/{offset}", name="older_tricks")
     */
    public function moreTricks(TricksRepository $tricksRepository, $offset)
    {
        $tricks = $tricksRepository->findBy([], ['created_at' => 'DESC'], 6, $offset);

        return $this->render('main/older_tricks.html.twig', [
            'tricks' => $tricks,
        ]);
    }
}
