<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 5/20/2016
 * Time: 7:19 PM
 */

namespace RecUp\UserBundle\Controller;


use FOS\UserBundle\Form\Type\ProfileFormType;
use RecUp\UserBundle\Entity\UserProfile;
use RecUp\UserBundle\Service\FindCurrentUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

        $dataByUser =  $this->get('recup_current_user')->getUserProfileDataByUser();

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('UserBundle:UserProfile')
            ->findOneBy(['id' => $dataByUser]);

        $username = $user->getUsername();

        $form = $this->createFormBuilder($document)
            ->add('file')
            ->add('username')
            ->add('name', TextType::class, array(
                'data' => $username
            ))
            ->add('country', TextType::class)
            ->add('gender', ChoiceType::class, array(
                'choices' => array('0' => 'not known', '1' => 'Male', '2' => 'Female', '9' => 'not applicable'),
            ))
            ->add('birth', 'birthday', array(
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                )
            ) )
            ->add('genre', ChoiceType::class, array(

                'choices' => array(
                     'Classical' =>   'Classical',
                     'Experimental' =>   'Experimental',
                     'Flamenco' =>   'Flamenco',
                     'Fingerstyle' =>  'Fingerstyle',
                     'Folk' =>  'Folk',
                     'Jazz' =>  'Jazz',
                     'Metal' =>  'Metal',
                     'Rock' =>  'Rock'
                    ) ,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('website', TextType::class)
            ->add('about', TextareaType::class)
            ->add('save', SubmitType::class, array('label' => 'Save'))
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

    
    /**
     * @Route("/test", name="user")
     */
    public function userAction()
    {

        $dataByUser =  $this->get('recup_current_user')->getUserProfileDataByUser();

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('UserBundle:UserProfile')
            ->findOneBy(['id' => $dataByUser]);

//        dump($user);die;
        if(!$user) {
            throw $this->createNotFoundException('user not found');
        }

//        $markdownTransformer =  $this->get('app.markdown_transformer');
        $country = $user->getCountry();
        $username = $user->getUsername();
        $birth = $user->getBirth();
        $about = $user->getAbout();
        $genres = $user->getGenre();
        $gender = $user->getGender();

        //dump($dataByUser);die;


        return $this->render('@Record/Default/index.html.twig', array(
        'name' => $dataByUser,
        'country' => $country,
        'username' => $username,
            'birth' => $birth,
            'about' => $about,
            'genres' => $genres,
            'gender' => $gender,
    ));
    }


}