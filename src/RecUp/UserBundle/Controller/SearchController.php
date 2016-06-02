<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 6/2/2016
 * Time: 5:54 PM
 */

namespace RecUp\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

use RecUp\UserBundle\Entity\UserProfile;


class SearchController extends Controller
{
    /**
     * @Route("/search", name="search")
     */
    public function liveSearchAction(Request $request)
    {
        $string = $this->getRequest()->request->get('searchText');
//        $string = 'Da';
        
        $users = $this->getDoctrine()
                      ->getRepository('UserBundle:UserProfile')
                      ->findByLetters($string);
        // return users on json format
        
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);


        $jsonContent = $serializer->serialize($users, 'json');

        $response = new Response($jsonContent);
        return $response;
    }

}