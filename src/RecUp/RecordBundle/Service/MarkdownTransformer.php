<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 4/15/2016
 * Time: 1:18 PM
 */

namespace RecUp\RecordBundle\Service;


class MarkdownTransformer
{
    private $markdownParser;

    public function __construct($markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    public function parse($str)
    {
        return $this->markdownParser
            ->transform($str);
    }

}