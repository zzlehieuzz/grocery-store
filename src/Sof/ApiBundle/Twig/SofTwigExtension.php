<?php
/**
 * Created by PhpStorm.
 * User: DAT
 * Date: 4/24/14
 * Time: 7:18 PM
 */

namespace Sof\ApiBundle\Twig;


class SofTwigExtension  extends \Twig_Extension {

    public function getName()
    {
        return 'sof_extension';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('json_print', array($this, 'jsonPrintFilter'), array('is_safe' => array('html'))),
        );
    }

    public function jsonPrintFilter($content)
    {
        return '<pre>'. preg_replace('/\n\s+{/','{',str_replace('    ', '  ', json_encode($content, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE))).'</pre>';
    }
} 