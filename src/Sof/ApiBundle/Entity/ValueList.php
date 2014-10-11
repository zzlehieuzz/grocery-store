<?php

namespace Sof\ApiBundle\Lib;

use Sof\ApiBundle\Lib\Config;

/**
 * Get value list config
 * @author Anhnt 2012/11/14 14:50
 */
class ValueList
{

  public static function get($keys, $options = array())
  {
    return Config::getValueList($keys, $options);
  }

  public static function valueToText($keys, $value, $default = NULL)
  {
    $valueList = self::get($keys);
    if (!isset($valueList[$value])) return $default;
    return $valueList[$value];
  }

  public static function constToValue($keys)
  {
    return Config::getValue($keys);
  }

  public static function constToText($keys)
  {
    return Config::getValue($keys, TRUE);
  }

  public static function textToValue($searchText, $keys)
  {
    $valueList = ValueList::get($keys);

    foreach ($valueList as $key => $text) {
      if ($searchText == $text) {
        return $key;
      }
    }

    return NULL;
  }

  public static function getStockType()
  {
    $stockType = self::get('common.stock_type');
    $out = self::constToValue('common.stock_type.STOCK_OUT');
    $outCancel = self::constToValue('common.stock_type.STOCK_OUT_CANCEL');

    return array($out => $stockType[$out], $outCancel => $stockType[$outCancel]);
  }

  public static function getStockTransfer()
  {
    $stockType = self::get('common.stock_type');
    $out = self::constToValue('common.stock_type.TRANSFER_OUT');
    $in = self::constToValue('common.stock_type.TRANSFER_IN');

    return array($out => $stockType[$out], $in => $stockType[$in]);
  }

  public static function getArrayValue($array, $key)
  {
    $results = array();
    foreach ($array as $data) {
      $results[] = $data[$key];
    }

    return $results;
  }
}
