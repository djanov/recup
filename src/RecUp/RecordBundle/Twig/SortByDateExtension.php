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


    public function sortByDate($data)
    {
//        dump($data);die;
        
       foreach ($data as $key => $row) {
//           dump($sort[$key] = strtotime($row));die;
           $sort[$key] = strtotime($row);

//                      $sort[$key] = strtotime($row['date']);
//          dump($sort['date']);die;
       }
//         return array_sort($sort, 'date', SORT_DESC);
        return array($data, SORT_DESC);
//        return array_multisort($sort, SORT_DESC, $data);

    }



}