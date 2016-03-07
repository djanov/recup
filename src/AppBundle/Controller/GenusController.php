<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 3/6/2016
 * Time: 8:32 PM
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class GenusController
{
    /**
     * @Route("/genus/{genusName}")
     */
    public function showAction($genusName)
    {
        return new Response('The genus:'.$genusName);
    }

}