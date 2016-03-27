<?php

namespace RecUp\RecordBundle\Controller;

use RecUp\RecordBundle\Entity\Record;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/record/new")
     */
    public function newAction()
    {
        $record = new Record();
        $record->setSongName('the best of '.rand(1,100));
        $record->setArtist('Lenny');
        $record->setGenre('rock');

        $em = $this->getDoctrine()->getManager();
        $em->persist($record);
        $em->flush();

        return new Response('<html><body>song created!</body></html>');
    }

    /**
     * @Route("/songs")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $songs = $em->getRepository('RecordBundle:Record')
            ->findAll();

        return $this->render('@Record/song/list.html.twig', [
           'songs' => $songs
        ]);
    }

    /**
     * @Route("/test/{wat}")
     */
    public function indexAction($wat)
{
    $funFact = 'Octopuses can change the color of their body in just *three-tenths* of a second!';

    $cache = $this->get('doctrine_cache.providers.my_markdown_cache');
    $key = md5($funFact);
    if ($cache->contains($key)) {
        $funFact = $cache->fetch($key);
    } else {
        sleep(1); // fake how slow this could be
        $funFact = $this->get('markdown.parser')
            ->transform($funFact);
        $cache->save($key, $funFact);
    }

    return $this->render('RecordBundle:Default:index.html.twig', array(
        'name' => $wat,
        'funFact' => $funFact,
    ));
}

    /**
 * @Route("/test/{wat}/notes", name="record_show_notes")
 * @Method("GET")
 */
    public function getNoteAction()
    {
        $notes = [
            ['id' => 1, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Octopus asked me a riddle, outsmarted me', 'date' => 'Dec. 10, 2015'],
            ['id' => 2, 'username' => 'AquaWeaver', 'avatarUri' => '/images/ryan.jpeg', 'note' => 'I counted 8 legs... as they wrapped around me', 'date' => 'Dec. 1, 2015'],
            ['id' => 3, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked!', 'date' => 'Aug. 20, 2015'],
        ];

        $data = [
            'notes' => $notes,
        ];

        return new JsonResponse($data);
    }
}
