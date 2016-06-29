<?php

namespace RecUp\RecordBundle\Controller;

use RecUp\RecordBundle\Entity\Record;
use RecUp\RecordBundle\Entity\RecordComment;
use RecUp\RecordBundle\Form\RecordFormType;
use RecUp\RecordBundle\Service\MarkdownTransformer;
use RecUp\UserBundle\Entity\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
        $records = $em->getRepository('RecordBundle:Record')
            ->findBy(array(), array('updatedAt' => 'DESC'));

//        dump($records);die;
        $recentSongs = [];


        foreach ($records as $record)
        {
            $recentSongs[] = [
//                'id' => $user->getId(),
//                'username' => $user->getName(),
//                'profilePicture' => $user->getWebPath(),
//                'songs' => $user->getSongs()->toArray(),
//                'avatarUri' => '/images/'.$user->getUserAvatarFilename(),
//                'username' => $record->getUsername(),
                'user' => $record->getUsername(),
                'songname' => $record->getSongName(),
                'about' => $record->getAbout(),
                'artitst' => $record->getArtist(),
//                'genre' => $record->getGenre(),
                'latest' => $record->getUpdatedAt(),
                'likes' => $record->getLikes(),
                'isDownloadable' => $record->getIsDownloadable(),
            ];
        }
        

        return $this->render('@Record/Default/index.html.twig', array(
            'users' => $users,
            'recentSongs' => $recentSongs
        ));
    }

    /**
     * @Route("/songs/all", name="get_all_songs")
     */
    public function songsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $records = $em->getRepository('RecordBundle:Record')
            ->findBy(array(), array('updatedAt' => 'DESC'));
        
        $recentSongs = [];


        foreach ($records as $record)
        {
            $recentSongs[] = [
                'user' => $record->getUsername()->getWebPath(),
                'songname' => $record->getSongName(),
                'about' => $record->getAbout(),
                'artitst' => $record->getArtist(),
                'genre' => $record->getGenre(),
                'latest' => $record->getUpdatedAt(),
            ];
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($recentSongs));
        return $response;
    }
    
    /**
     * @Route("/record/new", name="record_new")
     */
    public function newAction(Request $request)
    {

        $record = new Record();

        $dataByUser = $this->get('recup_current_user')->getUserProfileDataByUser();
        $em = $this->getDoctrine()->getManager();
        $id = $em->getRepository('UserBundle:UserProfile')
            ->findOneBy(['id' => $dataByUser]);
        $dataId = $id->getId();

        $form = $this->createForm(RecordFormType::class, $record , array(
            'username' =>$dataId
            
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $record = $form->getData();


            $em = $this->getDoctrine()->getManager();

            $em->persist($record);

 ////            $em->refresh($document);
////            $this->addFlash(
////                'success',
////                'User details have been updated'
////            );
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('RecordBundle:song:new.html.twig', array(
            'form' => $form->createView(),
        ));
//        return array('form' => $form->createView());
    }

    /**
     * @Route("/songs/{id}", defaults={"id" = null}, name="record_songs")
     */
    public function listAction()
    {
        $dataByUser = $this->get('recup_current_user')->getUserProfileDataByUser();
        $em = $this->getDoctrine()->getManager();
        $dataId = $em->getRepository('UserBundle:UserProfile')
            ->findOneBy(['id' => $dataByUser]);

        if(!$dataId)
        {
            throw $this->createNotFoundException('yo are retarded you don\'t have any songs so upload it');
        }
        $id = $dataId->getId();

        $em = $this->getDoctrine()->getManager();
        $songs = $em->getRepository('RecordBundle:Record')
            ->findBy(['username' => $id]);
//            ->findAllPublishedOrderedByRecentlyActive();


        return $this->render('@Record/song/list.html.twig', [
           'songs' => $songs
        ]);
    }

    /**
     * @Route("/song/{track}", name="record_show")
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

    /**
     * @Route("/song/{id}/like.{format}", name="record_likes",
     *  defaults={"format" = "html"}, requirements={"format" = "json"})
     */
    public function likeAction($id, $format)
    {
        $em = $this->getDoctrine()->getManager();

//        dump($id);die;
        $getLikes = $em->getRepository('RecordBundle:Record')
            ->findOneBy(['songName' => $id]);

        if(!$getLikes->hasLikes($this->getUser())){
            $getLikes->getLikes()->add($this->getUser());
        }


//    dump($getLikes);die;
        $em->persist($getLikes);
        $em->flush();

        if($format == 'json'){
            $data = array(
              'like' => true
            );
            $response = new JsonResponse($data);

            return $response;
        }
        return $this->redirect($this->generateUrl('record_show', array('track' => $id)));
    }
//
    /**
     * @Route("/song/{id}/unlike.{format}", name="record_unlikes",
     *     defaults={"format" = "html"}, requirements={"format" = "json"})
     */
    public function unlikeAction($id, $format)
    {
        $em = $this->getDoctrine()->getManager();

//        dump($id);die;
        $getLikes = $em->getRepository('RecordBundle:Record')
            ->findOneBy(['songName' => $id]);

//        if(!$getLikes) {
//            throw $this->createNotFoundException('No song found for '. $id);
//        }

        if($getLikes->hasLikes($this->getUser())){
            $getLikes->getLikes()->removeElement($this->getUser());
        }


//    dump($getLikes);die;
        $em->persist($getLikes);
        $em->flush();

        if($format == 'json') {
            $data = array(
                'like' => false
            );
            $response = new JsonResponse($data);

            return $response;
        }

        return $this->redirect($this->generateUrl('record_show', array('track' => $id)));
    }

    /**
     * @Route("/song/{id}/add", name="record_add_favorites")
     */
    public function addFavoritesAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $favorite = $em->getRepository('RecordBundle:Record')
            ->findOneBy(['songName' => $id]);

        
        if(!$favorite->hasFavorites($this->getUser())){
            $favorite->getFavorites()->add($this->getUser());
        }
        
        
        $em->persist($favorite);
        $em->flush();
        
        return $this->redirect($this->generateUrl('record_show', array('track' => $id)));
    }

    /**
     * @Route("/song/{id}/remove", name="record_remove_favorites")
     */
    public function removeFavoritesAction($id)
    {
        $em = $this->getDoctrine()->getManager();

//        dump($id);die;
        $favorite = $em->getRepository('RecordBundle:Record')
            ->findOneBy(['songName' => $id]);

//        if(!$getLikes->hasLikes($this->getUser())){
//            $getLikes->getLikes()->add($this->getUser());
//        }
        if($favorite->hasFavorites($this->getUser())){
            $favorite->getFavorites()->removeElement($this->getUser());
        }

        $em->persist($favorite);
        $em->flush();

        return $this->redirect($this->generateUrl('record_show', array('track' => $id)));
    }



    /**
     * @Route("/favorites/{id}", defaults={"id" = null}, name="record_favorites")
     */
    public function favoriteAction()
    {


        $id = $this->getUser()->getId();

//        dump($id);die;

        $em = $this->getDoctrine()->getManager();
        $songs = $em->getRepository('RecordBundle:Record');
        $query = $songs->createQueryBuilder('u')
            ->innerJoin('u.favorites', 'g')
            ->where('g.id = :user_id')
            ->setParameter('user_id', $id)
            ->getQuery()->getResult();

//            ->findBy(['favorites' => $id]);

//        $test = $songs->getFavorites();
//        dump($query);die;
//
//        $favoriteSongs = [];
//
//
//        foreach ($query as $favorite)
//        {
//            $favoriteSongs[] = [
//                'user' => $favorites->g
//                'songname' => $record->getSongName(),
//                'about' => $record->getAbout(),
//                'artitst' => $record->getArtist(),
//                'genre' => $record->getGenre(),
//                'latest' => $record->getUpdatedAt(),
//            ];
//        }


        return $this->render('@Record/song/favorite.html.twig', [
            'songs' => $query
        ]);
    }

    /**
     * @Route("download/song/{record}", name="download", defaults={"record" = null})
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downLoadSongAction($record)
    {
        $em = $this->getDoctrine()->getManager();
        $getId= $em->getRepository('RecordBundle:Record')
            ->findOneBy(['id' => $record]);
        $getName = $getId->getSongName();

        $path = $this->get('kernel')->getRootDir(). "/Resources/songs/" . $getName;

//        dump($path);die;
//        $content = file_get_contents($path);

//        $response = new Response();
//
//        $response->headers->set('Content-Type', 'audio/mpeg3');
//        $response->headers->set('Content-Disposition', '');
//
//        $response->setContent($content);

        if($record){
            $response = new BinaryFileResponse($path);

            $em = $this->getDoctrine()->getManager();
            $download = $em->getRepository('RecordBundle:Record')
                ->findOneBy(['id' => $record]);
            if($download->getIsDownloadable() == true)
            {
                return $response;
            } else
            $response->trustXSendfileTypeHeader();
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $record
            );
            return $response;
        }

//        dump($response);die;

//        return $response;
    }
}
