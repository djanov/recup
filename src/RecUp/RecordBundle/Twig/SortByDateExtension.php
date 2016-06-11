<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 6/11/2016
 * Time: 10:07 PM
 */

namespace RecUp\RecordBundle\Twig;


use Symfony\Component\Validator\Constraints\DateTime;

class SortByDateExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'app_sort_date';
    }

    public function getFunctions()
    {
        return array(
          new \Twig_SimpleFunction('sortDate', array($this, 'sortByDate'))
        );
    }

    public function sortByDate($data = array())
    {
       foreach ($data as $key => $row) {
           $sort[$key] = strtotime($row['data']);
       }
        return array_multisort($sort, SORT_DESC, $data);
    }



}