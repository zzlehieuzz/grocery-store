<?php

/**
 * Config
 * @author HieuNld 2014/01/11
 */
namespace Sof\ApiBundle\Lib;

use Sof\ApiBundle\Exception\SofApiException;

class SofUtil
{

    /**
     * Remove alias of key in Scalar Array
     * @param $arrayScalar
     * @throws \Exception
     * @return \Array
     * Examples
     * {arrScalar["alias_filedName"] => value } => {arrScalar["filedName"] => value }
     * @author Hamd 2014/01/13
     */
    public static function formatScalarArray($arrayScalar)
    {
        $arrValue = self::formatScalarArrayList($arrayScalar);
        return (count($arrValue)>1) ? $arrValue : end($arrValue) ;
    }

    public static function formatScalarArrayList($arrayScalar)
    {
        $arrValue = array();

        if (!$arrayScalar) {
            return $arrValue;
        }

        foreach($arrayScalar as $index => $arrayScalarValue) {
            foreach($arrayScalarValue as $key => $item) {
                $keyExplodeArr = explode("_",$key);

                if(isset($arrValue[$index][end($keyExplodeArr)])) {
                    if(isset($arrValue[$index][$key])) {

                        throw new \Exception("Alias error");
                    } else {
                        $arrValue[$index][$key] = $item;
                    }

                } else {
                    $arrValue[$index][end($keyExplodeArr)] = $item;
                }
            }
        }

        return $arrValue;
    }

    /**
     * calculate divisionRound
     * @param $numerator
     * @param $denominator
     * @param int $precision
     * @param string $default
     * @return int
     *
     * @author HieuNLD 2014/01/15
     */
    public static function divisionRound($numerator, $denominator, $precision = 0, $default = '')
    {
        if(is_numeric($numerator) && $numerator && is_numeric($denominator) && $denominator) {

            return round($numerator / $denominator, $precision);
        }

        return $default;
    }

    /**
     * calculate divisionFloor
     * @param $numerator
     * @param $denominator
     * @param string $default
     * @return int
     *
     * @author HieuNLD 2014/01/15
     */
    public static function divisionFloor($numerator, $denominator, $default = '')
    {
        if(is_numeric($numerator) && $numerator && is_numeric($denominator) && $denominator) {
            return floor($numerator / $denominator);
        }

        return $default;
    }

    public static function getArrValue($list, $field) {
        $output = array();
        if (!is_array($list)) {
            return $output;
        }

        foreach($list as $item) {
            $output[] = $item[$field];
        }

        return $output;
    }

    /**
     * @return string
     */
    public static function createFileName() {
        $time = microtime(true);

        return sprintf("%s%03d", date('YmdHis', $time), ($time - floor($time)) * 1000);
    }
}