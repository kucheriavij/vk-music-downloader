<?php

namespace App\Controller;


use App\Entity\Audio;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @Route("delete/{id}", name="audio_delete", methods="GET")
     *
     * @param Request $request
     * @param Audio $audio
     * @return Response
     */
    public function delete(Request $request, Audio $audio): Response
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($_SERVER['DOCUMENT_ROOT'] . $audio->getFilePath());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($audio);
        $entityManager->flush();

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("download/{id}", name="audio_download", methods="GET")
     *
     * @param Request $request
     * @param Audio $audio
     * @return Response
     */
    public function download(Request $request, Audio $audio): Response
    {

        $response = new Response();
        $response->headers->set('Content-type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', "{$audio->getArtistName()} - {$audio->getTrackName()}.mp3"));
        $response->setContent(file_get_contents($_SERVER['DOCUMENT_ROOT'].$audio->getFilePath()));
        $response->setStatusCode(200);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
