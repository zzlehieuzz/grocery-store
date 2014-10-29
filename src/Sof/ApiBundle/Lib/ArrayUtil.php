<?php

namespace Sof\ApiBundle\Lib;


class ArrayUtil {

    public static function objToArray($data)
    {
        $result = array();
        foreach ($data as $key => $value) {

            print_r($value);
        }
        print_r($data);
        die;
        return $result;
    }
}
