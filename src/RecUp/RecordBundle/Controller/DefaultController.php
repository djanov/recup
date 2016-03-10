<?php

namespace RecUp\RecordBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    /**
     * @Route("/test/{wat}")
     */
    public function indexAction($wat)
{
    $notes = [
        'Octopus asked me a riddle, outsmarted me',
        'I counted 8 legs... as they wrapped around me',
        'Inked!'
    ];
    return $this->render('RecordBundle:Default:index.html.twig', array(
        'name' => $wat,
        'notes' => $notes
    ));
}
}
