<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 5/20/2016
 * Time: 7:19 PM
 */

namespace RecUp\UserBundle\Controller;


use RecUp\UserBundle\Entity\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/edit_profile", name="profile")
     * @Template()
     */
    public function uploadAction(Request $request)
    {
        $document = new UserProfile();

        $form = $this->createFormBuilder($document)
            ->add('name')
            ->add('file')
            ->add('genre', ChoiceType::class, array(

                'choices' => array(
                     'chck1' =>   'Classical',
                     'chck2' =>   'Experimental',
                     'chck3' =>   'Flamenco',
                     'chck4' =>  'Fingerstyle',
                     'chck5' =>  'Folk',
                     'chck6' =>  'Jazz',
                     'chck7' =>  'Metal',
                     'chck8' =>  'Rock'
                    ) ,
                'expanded' => true,
                'multiple' => true,
            ))
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ... perform some action, such as saving the task to the database
            $em = $this->getDoctrine()->getManager();

            $em->persist($document);
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('@User/Registration/update_profile.html.twig', array(
            'form' => $form->createView(),
        ));
//        return array('form' => $form->createView());
    }
    

}