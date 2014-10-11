<?php

namespace Sof\ApiBundle\Lib;

class TryUtil {

  /**
   * Try to call $func from $obj, uncallable return NULL
   * @param Object $obj
   * @param string $func
   * @return NULL|mixed
   *
   * @author Khiemnd 2012/12/04
   */
  public static function callMethod($obj, $func, $unCallDefault = NULL)
  {
    $funcArr = explode(':', $func);

    foreach ($funcArr as $funcName) {
      if (!is_callable(array($obj, $funcName))) {
        return $unCallDefault;
      }

      $obj = call_user_func(array($obj, $funcName));
    }

    return $obj;
  }

  /**
   * Try to get a value of $array from $obj, uncallable return NULL
   * @param Array $array
   * @param String $keys
   * @return NULL|mixed
   *
   * @author Khiemnd 2012/12/04
   */
  public static function fetchArray($array, $keys, $unCallDefault = NULL)
  {
    $keyArr = explode(':', $keys);

    foreach ($keyArr as $keyVal) {
      if (!isset($array[$keyVal])) {
        return $unCallDefault;
      }

      $array = $array[$keyVal];
    }

    return $array;
  }
}
