<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 5/20/2016
 * Time: 7:19 PM
 */

namespace RecUp\UserBundle\Controller;


use Doctrine\ORM\EntityRepository;
use Faker\Provider\Text;
use FOS\UserBundle\Form\Type\ProfileFormType;
use RecUp\UserBundle\Entity\UserProfile;
use RecUp\UserBundle\Form\UserFormType;
use RecUp\UserBundle\Service\FindCurrentUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/edit", name="profile")
     * @Template()
     */
    public function uploadAction(Request $request)
    {
        $document = new UserProfile();

        $form = $this->createFormBuilder($document)
            ->add('file')
            ->add('username', EntityType::class, array(
                'class' => 'RecUp\UserBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.id LIKE :user')
                        ->setParameter('user', $this->get('security.token_storage')->getToken()->getUser()->getId());
                },
                'attr' => array('style' => 'display:none'),
                'label_attr' => array('style' => 'display:none')
            ))
            ->add('name')
            ->add('country', TextType::class)
            ->add('gender', ChoiceType::class, array(
                'choices' => array('0' => 'not known', '1' => 'Male', '2' => 'Female', '9' => 'not applicable'),
            ))
            ->add('birth', 'birthday', array(
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                )
            ))
            ->add('genre', ChoiceType::class, array(

                'choices' => array(
                    'Classical' => 'Classical',
                    'Experimental' => 'Experimental',
                    'Flamenco' => 'Flamenco',
                    'Fingerstyle' => 'Fingerstyle',
                    'Folk' => 'Folk',
                    'Jazz' => 'Jazz',
                    'Metal' => 'Metal',
                    'Rock' => 'Rock'
                ),
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('website', TextType::class)
            ->add('about', TextareaType::class)
            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);
        $this->isGranted('edit', $form);

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

        return $this->render('@User/Registration/update_profile.html.twig', array(
            'form' => $form->createView(),
        ));
//        return array('form' => $form->createView());
    }


    /**
     * @Route(" /{id}/edit", name="editProfile")
     * @Template()
     */
    public function editAction($id, Request $request)
    {

        $userManager = $this->get('fos_user.user_manager');

        //      getting the user by the GET
        $userByName = $userManager->findUserByUsername($id);
        if(!$userByName) {
            return $this->redirect($this->generateUrl('index'));
        }
//       if the user is valid, getting the id
        $userById = $userByName->getId();

//      current user logged in, getting the object for the form
        $dataByUser =  $this->get('recup_current_user')->getUserProfileDataByUser();
        $em = $this->getDoctrine()->getManager();
        $id = $em->getRepository('UserBundle:UserProfile')
            ->findOneBy(['id' => $dataByUser]);

//      by the object find the id
        $getUsername = $id->getUserName();
        $getTheId = $getUsername->getId();

//       match the two id-s if they are don't correct redirect to index page
        if($userById != $getTheId) {
            return $this->redirect($this->generateUrl('index'));
        }
//      creating the form by the UserProflie object setting by the $id
        $form = $this->createFormBuilder($id)
            ->add('file')
            ->add('username', EntityType::class, array(
                'class' => 'RecUp\UserBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.id LIKE :user')
                        ->setParameter('user', $this->get('security.token_storage')->getToken()->getUser()->getId());
                },
                'attr' => array('style' => 'display:none'),
                'label_attr' => array('style' => 'display:none')
            ))
            ->add('name')
            ->add('country', TextType::class)
            ->add('gender', ChoiceType::class, array(
                'choices' => array('0' => 'not known', '1' => 'Male', '2' => 'Female', '9' => 'not applicable'),
            ))
            ->add('birth', 'birthday', array(
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                )
            ))
            ->add('genre', ChoiceType::class, array(

                'choices' => array(
                    'Classical' => 'Classical',
                    'Experimental' => 'Experimental',
                    'Flamenco' => 'Flamenco',
                    'Fingerstyle' => 'Fingerstyle',
                    'Folk' => 'Folk',
                    'Jazz' => 'Jazz',
                    'Metal' => 'Metal',
                    'Rock' => 'Rock'
                ),
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('website', TextType::class)
            ->add('about', TextareaType::class)
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
//            $form->submit($request);
//
//            handleRequest vs submit
//https://symfony.com/doc/current/cookbook/form/direct_submit.html

//            $form->bind($request); // bind is deprecated

            if ($form->isValid()){
                $em->flush();
                return $this->redirect($this->generateUrl('index'));
            }
        }

//       getting the username for the path, if i send this the update works
        $currentUser = $userByName->getUsername();

        return $this->render('@User/User/user_edit.html.twig', array(
            'form' => $form->createView(),
            'id'   => $currentUser,
        ));
    }

    /**
     * @Route("/user/{userFind}", defaults={"userFind" =  null}, name="user")
     */
    public function userAction($userFind)
    {
//        $dataByUser =  $this->get('recup_current_user')->getUserProfileDataByUser();
        $userManager = $this->get('fos_user.user_manager');

        $userByName = $userManager->findUserByUsername($userFind);

        if(!$userByName) {
            throw $this->createNotFoundException('user not found');
        }

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('UserBundle:UserProfile')
            ->findOneBy(['username' => $userByName]);

//        dump($user);die;

        if(!$user) {
            // need to add first a flash message for the user that he is not set the profile
            // yet and then redirect or go to the edit_profile page and then
            // have the flash message
            return $this->redirectToRoute('profile');
//            throw $this->createNotFoundException('user not found');
        }

//        $markdownTransformer =  $this->get('app.markdown_transformer');

        $country = $user->getCountry();
        $username = $user->getUsername();
        $birth = $user->getBirth();
        $about = $user->getAbout();
        $genres = $user->getGenre();
        $gender = $user->getGender();
        $file = $user->getWebPath();
        $name = $user->getName();
        $website = $user->getWebsite();
//        dump($file);die;
//        dump($dataByUser);die;


        return $this->render('@User/User/user_profile.html.twig', array(
//        'name' => $dataByUser,
            'country' => $country,
            'username' => $username,
            'birth' => $birth,
            'about' => $about,
            'genres' => $genres,
            'gender' => $gender,
            'file' => $file,
            'name' => $name,
            'website' => $website,
        ));
    }


}