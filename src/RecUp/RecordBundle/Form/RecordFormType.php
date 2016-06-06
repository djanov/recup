<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 6/6/2016
 * Time: 10:02 AM
 */

namespace RecUp\RecordBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RecordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('songName')
        ->add('artist')
        ->add('about')
        ->add('genre')
        ->add('songFile', VichFileType::class, array(
            'required'      => false,
            'allow_delete'  => true, // not mandatory, default is true
            'download_link' => false, // not mandatory, default is true
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RecUp\RecordBundle\Entity\Record'
        ]);
    }

}