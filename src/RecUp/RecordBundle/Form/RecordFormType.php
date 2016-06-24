<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 6/6/2016
 * Time: 10:02 AM
 */

namespace RecUp\RecordBundle\Form;



use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RecordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
         ->add('username',  EntityType::class, array(
             'class' => 'RecUp\UserBundle\Entity\UserProfile',
             'property' => 'username',
             'query_builder' => function(EntityRepository $er) use ($options) {
                 return $er->createQueryBuilder('u')
                   ->where('u.id = :username' )
                    ->setParameter('username', $options['username']);
             },
             'attr' => array('style' => 'display:none'),
             'label_attr' => array('style' => 'display:none')
         ))
        ->add('songName')
        ->add('artist')
        ->add('about')
//        ->add('genre')
        ->add('isDownloadable', CheckboxType::class, array(
            'label' => 'Free download'
        ))
        ->add('songFile', VichFileType::class, array(
            'required'      => false,
            'allow_delete'  => true, // not mandatory, default is true
            'download_link' => false, // not mandatory, default is true
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RecUp\RecordBundle\Entity\Record',
            'username' => null
        ]);
    }

}