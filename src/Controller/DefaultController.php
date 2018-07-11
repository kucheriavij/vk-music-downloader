<?php

namespace App\Controller;


use App\Entity\Audio;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $audio = $entityManager->getRepository(Audio::class)->findAll();

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'audio' => $audio,
        ]);
    }
}
