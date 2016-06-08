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

        $menu->addChild('Home', array('route' => 'index'))
            ->setAttribute('icon', 'fa fa-home');

        $menu->addChild('My Tracks', array('route' => 'record_songs'))
              ->setAttribute('icon', 'glyphicon glyphicon-cd');
//            ->setAttribute('class', 'badge')
//            ->setLabel('2');

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
        $menu->addChild('New Track', array('route' => 'record_new'))
            ->setAttribute('icon', 'fa fa-plus');

        $username = $this->container->get('security.token_storage')->getToken()->getUser()->getUsername();


        $usernameUpper = strtoupper($username); // need to make if user not logged in
        


        $menu->addChild('User', array('label' => $usernameUpper))
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', 'fa fa-user');
//              need to change 'uri' later for dynamic.
        $menu['User']->addChild('Profile', array('uri' => '/user/'.$username))
            ->setAttribute('icon', 'glyphicon glyphicon-user')
            ->setAttribute('divider_append', true);
        $menu['User']->addChild('Settings', array('uri' => '/../'.$username.'/edit'))
            ->setAttribute('icon', 'fa fa-edit');
        $menu['User']->addChild('Logout', array('uri' => '/logout'))
        ->setAttribute('icon', 'glyphicon glyphicon-log-out');
        return $menu;
    }
}