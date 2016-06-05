<?php

namespace RecUp\RecordBundle\Controller;

use RecUp\RecordBundle\Entity\Record;
use RecUp\RecordBundle\Entity\RecordComment;
use RecUp\RecordBundle\Service\MarkdownTransformer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
//        To see what what fields have the daniel user (FOSUserBundle)

//        $user = $this->container->get('fos_user.user_manager')->findUserByUsername('daniel');
//
//        dump($user);die;

        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('UserBundle:UserProfile')
            ->findAll();

//        dump($user);die;
        ;


        return $this->render('@Record/Default/index.html.twig', array(
            'users' => $users,
        ));
    }
    
    /**
     * @Route("/record/new")
     */
    public function newAction(Request $request)
    {
//        $record = new Record();
//        $record->setSongName('the best of '.rand(1,100));
//        $record->setArtist('Lenny');
//        $record->setGenre('rock');
//
//        $comment = new RecordComment();
//        $comment->setUsername('Daniel');
//        $comment->setUserAvatarFilename('ryan.jpeg');
//        $comment->setComment('I think ths song is amazing');
//        $comment->setCreatedAt(new \DateTime('-1 month'));
//        $comment->setRecord($record);
//
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($record);
//        $em->persist($comment);
//        $em->flush();
//
//        return new Response('<html><body>song created!</body></html>');
        
        $document = new Record();

        $form = $this->createFormBuilder($document)
            ->add('songName')
            ->add('artist')
            ->add('about')
            ->add('genre')
            ->add('songFile', 'vich_file', array(
                'required'      => false,
                'allow_delete'  => false, // not mandatory, default is true
                'download_link' => false, // not mandatory, default is true
            ))
            ->add('save', SubmitType::class, array('label' => 'Save'))

            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ... perform some action, such as saving the task to the database
            $em = $this->getDoctrine()->getManager();

            $em->persist($document);
//            $em->refresh($document);
//            $this->addFlash(
//                'success',
//                'User details have been updated'
//            );
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('RecordBundle:song:new.html.twig', array(
            'form' => $form->createView(),
        ));
//        return array('form' => $form->createView());
    }

    /**
     * @Route("/songs", name="record_songs")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

//        dump($em->getRepository('RecordBundle:Record'));die;

        $songs = $em->getRepository('RecordBundle:Record')
            ->findAllPublishedOrderedByRecentlyActive();

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

        $markdownTransformer =  $this->get('app.markdown_transformer');
        $about = $markdownTransformer->parse($songs->getAbout());
    
    $this->get('logger')
        ->info('Showing records: '.$track);

    $recentComments = $em->getRepository('RecordBundle:RecordComment')
        ->findAllRecentCommentsForRecord($songs);

    return $this->render('@Record/Default/show.html.twig', array(
        'name' => $songs,
        'recentCommentCount' => count($recentComments),
        'about' => $about,
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
