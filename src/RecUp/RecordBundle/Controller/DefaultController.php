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
                'genre' => $record->getGenre(),
                'latest' => $record->getUpdatedAt(),
            ];
        }

//        dump($recentSongs);die;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////// TESTING LATEST SONG ONLY TO SHOW /////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////      
//        $data = [
//            'latestSong' => $recentSongs,
//        ];
////        dump($recentSongs);die;
//
////        dump($user);die;
////        dump($data);die;
//        $length = count($recentSongs);
////        dump($recentSongs);die;
////        dump($recentSongs[5]['latest']);die;
//        $test = array();
//        for($x = 0; $x < $length; $x++){
//            if($recentSongs[$x]['latest']){
//              $recentLatest =  $recentSongs[$x]['latest']->getUpdatedAt();
//                $test = $recentLatest;
////                var_dump($test);
//                $val = get_object_vars($test);
////                $valDate = strtotime($val['date']);
////                $usort =  usort($val, $valDate['date']);
//                };
//            }
//        var_dump($val);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////// END => TESTING LATEST SONG ONLY TO SHOW //////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////         
        
        
        
        
//        function latestDate($val, $b){
//            var_dump(strtotime($val['date'])<strtotime($b['date'])?1:-1);
//            uasort($val, 'cmp');
//            print_r($val);
//        };
//    dump($recentLatest);die;
//        dump($recentOne);die;
//            dump($recentSongs[7]['latest']->getUpdatedAt());die;
//        $allSortedSongs = usrot($data, function($a, $b){
//           if($a['latest']['updatedAt'] == $b['latest']['updatedAt'])
//           {
//               return
//           }
//        });

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
//
//        $users = $em->getRepository('UserBundle:UserProfile')
//            ->findAll();
//
//
//        $allusers = [];
////        dump($user);die;
//        foreach ($users as $user)
//        {
//            $allusers[] = [
//                'picture' => $user->getWebPath()
//            ];
//        }

//        dump($records);die;
        $recentSongs = [];


        foreach ($records as $record)
        {
            $recentSongs[] = [
//                'user' => $record->getUsername(),
//                 'user' => $user->getUsername(),
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

//    /**
//     * @Route("/test/{username}/record", name="songs_show")
//     * @Method("GET")
//     */
//    public function getSongsAction(UserProfile $userProfile)
//    {
//        $songs = [];
//
//        foreach ($userProfile->getSongs() as $song) {
////            dump($song);die;
//            $songs[] = [
//              'id' => $song->getId(),
//              'songname' => $song->getSongName(),
//               'artist' => $song->getArtist(),
//                'genre' => $song->getGenre(),
//                'about' => $song->getAbout(),
//                'updatedat' => $song->getUpdatedAt()
//            ];
//            $data = [
//                'songs' => $songs,
//            ];
//            return new JsonResponse($data);
//        }
//    }
}
