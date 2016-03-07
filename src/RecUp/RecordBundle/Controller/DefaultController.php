<?php

namespace RecUp\RecordBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('RecordBundle:Default:index.html.twig');
    }
}
