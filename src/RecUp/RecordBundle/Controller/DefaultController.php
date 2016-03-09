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
    $templating = $this->container->get('templating');
    $html = $templating->render('RecordBundle:Default:index.html.twig', array(
        'name' => $wat
    ));

    return new Response($html);
}
}
