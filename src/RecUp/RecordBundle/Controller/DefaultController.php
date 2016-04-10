<?php

namespace RecUp\RecordBundle\Controller;

use RecUp\RecordBundle\Entity\Record;
use RecUp\RecordBundle\Entity\RecordComment;
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

        $comment = new RecordComment();
        $comment->setUsername('Daniel');
        $comment->setUserAvatarFilename('ryan.jpeg');
        $comment->setComment('I think ths song is amazing');
        $comment->setCreatedAt(new \DateTime('-1 month'));
        $comment->setRecord($record);

        $em = $this->getDoctrine()->getManager();
        $em->persist($record);
        $em->persist($comment);
        $em->flush();

        return new Response('<html><body>song created!</body></html>');
    }

    /**
     * @Route("/songs")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

//        dump($em->getRepository('RecordBundle:Record'));die;

        $songs = $em->getRepository('RecordBundle:Record')
            ->findAllPublished();

        return $this->render('@Record/song/list.html.twig', [
           'songs' => $songs
        ]);
    }

    /**
     * @Route("/test/{track}", name="record_show")
     */
    public function showAction($track)
{
    $em = $this->getDoctrine()->getManager();

    $songs = $em->getRepository('RecordBundle:Record')
        ->findOneBy(['songName' => $track]);

        if(!$songs) {
          throw $this->createNotFoundException('song not found');
        }
    /*
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
    */
    $this->get('logger')
        ->info('Showing records: '.$track);

    return $this->render('@Record/Default/show.html.twig', array(
        'name' => $songs,
    ));
}

    /**
     * @Route("/test/{songName}/notes", name="record_show_notes")
    * @Method("GET")
    */
    public function getNoteAction(Record $record)
    {
        dump($record);
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
