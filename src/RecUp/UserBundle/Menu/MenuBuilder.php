<?php

namespace RecUp\UserBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class MenuBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');
        $menu->addChild("<span class='glyphicon glyphicon-cd'> Notification</span>",
            array(
                'route' => 'index',
                'extras' => array(
                    'safe_label' => true
                )
            ));
//        $menu->addChild('Home', array('route' => 'index'))
//            ->setAttribute('class', 'fa fa-home');
//        $menu->addChild('Notification', array('route' => 'record_songs'))
//            ->setAttribute('class', 'fa fa-group');
        return $menu;
    }
    public function userMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');
        /*
        You probably want to show user specific information such as the username here. That's possible! Use any of the below methods to do this.
        if($this->container->get('security.context')->isGranted(array('ROLE_ADMIN', 'ROLE_USER'))) {} // Check if the visitor has any authenticated roles
        $username = $this->container->get('security.context')->getToken()->getUser()->getUsername(); // Get username of the current logged in user
        */

        $menu->addChild('User', array('label' => 'Hi visitor'))
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', 'fa fa-user');
        $menu['User']->addChild('Edit profile', array('route' => 'fos_user_registration_register'))
            ->setAttribute('icon', 'fa fa-edit');
        return $menu;
    }
}