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
     * @param array $params
     * @return string
     *
     * @author Hieunld 2014/01/11
     */
    public static function createSpotCode($params = array())
    {
        $spotIdCode = '';
        if (isset($params['worldId']) && isset($params['areaId'])) {
            $spotIdCode = ($params['worldId']*10000) + ($params['areaId']*100);
        }

        return $spotIdCode;
    }

    /**
     * @param $spotCode
     * @param $status
     * @return string
     *
     * @author Hieunld 2014/01/11
     */
    public static function recoveryAreaCode($spotCode, $status = 'world')
    {
        if ($status == 'area') {

            return substr_replace($spotCode, '00', -2);
        }

        return substr_replace($spotCode, '0000', -4);
    }

    /**
     * @param $spotCode
     * @throws \Sof\ApiBundle\Exception\SofApiException
     * @return mixed
     *
     * @author datdvq 2014/04/22
     */
    public static function getSpotParts($spotCode)
    {
        if (strlen($spotCode) < 5) {
            throw new SofApiException;
        }

        $result = array();
        $result['areaCode']    = (int)substr_replace($spotCode, '00',   -2);
        $result['worldCode']   = (int)substr_replace($spotCode, '0000', -4);
        $result['spotNum']     = (int)substr($spotCode, -2);
        $result['areaNum']     = (int)substr($spotCode, -4, 2);
        $result['worldNum']    = $spotCode % 10000;
        $result['nextAreaCode']      = (int)$result['areaCode'] + 100;
        $result['nextAreaworldCode'] = (int)$result['areaCode'] + 10000;

        return $result;
    }

    /**
     * @param $spotProgress
     * @return boolean
     *
     * @author datdvq 2014/04/22
     */
    public static function isClearSpot($spotProgress)
    {
        $spotProgressParts = explode('/', $spotProgress);

        if (count($spotProgressParts) == 2 && $spotProgressParts[0] == $spotProgressParts[1]) {
            return TRUE;
        }

        return FALSE;
    }

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
}