<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 4/15/2016
 * Time: 1:18 PM
 */

namespace RecUp\RecordBundle\Service;


use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;

class MarkdownTransformer
{
    private $markdownParser;

    public function __construct(MarkdownParserInterface $markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    public function parse($str)
    {
        return $this->markdownParser
            ->transformMarkdown($str);
    }

}