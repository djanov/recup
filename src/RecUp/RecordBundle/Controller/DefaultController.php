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

    $recentComments = $songs->getComments()
        ->filter(function(RecordComment $comment){
            return $comment->getCreatedAt() > new \DateTime('-3 months');
});
    return $this->render('@Record/Default/show.html.twig', array(
        'name' => $songs,
        'recentCommentCount' => count($recentComments)
    ));
}

    /**
     * @Route("/test/{songName}/notes", name="record_show_notes")
    * @Method("GET")
    */
    public function getNoteAction(Record $record)
    {
        $comments = [];

        foreach($record->getComments() as $comment) {
            $comments[] = [
                'id' => $comment->getId(),
                'username' => $comment->getUsername(),
                'avatarUri' => '/images/'.$comment->getUserAvatarFilename(),
                'comment' => $comment->getComment(),
                'date' => $comment->getCreatedAt()->format('M, d, Y')
            ];
        }

        $data = [
            'notes' => $comments,
        ];

        return new JsonResponse($data);
    }
}
