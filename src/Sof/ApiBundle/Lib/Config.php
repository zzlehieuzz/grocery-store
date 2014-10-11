<?php

/**
 * Config
 * @author Khiemnd 2012/11/10
 */
namespace Sof\ApiBundle\Lib;

use Symfony\Component\Yaml\Yaml;

class Config
{
  const VALUE_LIST = 'ValueList';
  const MESSAGE = 'Message';
  const COMMON = 'Config';
  const NOT_FOUND = 'YAML not found!';

  public static function getMessage($key, $paramArray = array())
  {
    $message = self::getConfig(self::MESSAGE, $key);

    if ($message && is_string($message)) {
      foreach ($paramArray as $param => $value) {
        $message = str_replace(sprintf('{{ %s }}', $param), $value, $message);
      }
    }

    return $message;
  }

  public static function get($key)
  {
    return self::getConfig(self::COMMON, $key);
  }

  public static function getValueList($keys, $options = array())
  {
    $keys = explode('.', $keys);

    if (!is_array($keys) || count($keys) != 2) return NULL;

    $exclude = FALSE;

    if (isset($options['exclude'])) {
      $exclude = str_replace(' ', '', $options['exclude']);
      $exclude = explode(',', $exclude);
    }

    list($fileName, $param) = $keys;

    $valueList = self::loadValueList($fileName, $param);

    if ($valueList && is_array($valueList)) {
      $resultList = array();

      foreach ($valueList as $key => $value) {
        $value = explode('|', $value);

        if (!$exclude || !isset($value[1]) || !in_array($value[1], $exclude)) {
          $resultList[$key] = $value[0];
        }
      }

      return $resultList;
    }

    return NULL;
  }

  public static function getValue($keys, $getText = FALSE)
  {
    $keys = explode('.', $keys);

    if (!is_array($keys) || count($keys) != 3) return NULL;

    list($fileName, $key, $const) = $keys;
    $valueList = self::loadValueList($fileName, $key);

    if ($valueList && is_array($valueList)) {
      foreach ($valueList as $key => $value) {
        $value = explode('|', $value);
        if (isset($value[1]) && $value[1] == $const) {
          if ($getText) return $value[0];

          return $key;
        }
      }
    }

    return NULL;
  }

  /**
   * root path
   * @return string ~/IjNetBundle/
   */
  public static function rootPath()
  {
    return __DIR__.'/../';
  }

  /**
   * upload path
   * @return string ~/data/
   */
  public static function uploadPath()
  {
    return self::rootPath() . '../../../data/';
  }

  public static function loadValueList($fileName, $key)
  {
    global $cacheYaml;
    global $cacheValueList;

    if(!isset($cacheYaml)) $cacheYaml = array();
    if(!isset($cacheValueList)) $cacheValueList = array();

    $valueListKey = $fileName . '.' . $key;
    if (isset($cacheValueList[$valueListKey])) {
      // Retreiving from local static cache
      return $cacheValueList[$valueListKey];
    }

    if (isset($cacheYaml[$fileName])) {
      // Retreiving from local static cache
      $paramValue = $cacheYaml[$fileName];
    } else {
      $filePath = self::rootPath() . 'Resources/config/'.self::VALUE_LIST.'/'.$fileName.'.yml';
      $paramValue = Yaml::parse($filePath);
      $cacheYaml[$fileName] = $paramValue; // cache
    }

    $cacheValueList[$valueListKey] = $paramValue[$key]; // cache
    return $paramValue[$key];
  }

  private static function getConfig($folderName, $paramKey)
  {
    global $cacheConfig;
    global $cacheConfigFile;

    if(!isset($cacheConfig)) $cacheConfig = array();
    if(!isset($cacheConfigFile)) $cacheConfigFile = array();

    if (isset($cacheConfig[$paramKey])) {
      return $cacheConfig[$paramKey];
    }

    $folderPath = self::rootPath() . 'Resources/config/'.$folderName;
    $paramKeyArr = explode('.', $paramKey);

    foreach (glob($folderPath.'/*.yml') as $yamlSrc) {

      if (isset($cacheConfigFile[basename($yamlSrc)])) {
        $paramValue = $cacheConfigFile[basename($yamlSrc)];
      } else {
        $paramValue = Yaml::parse($yamlSrc);
        $cacheConfigFile[basename($yamlSrc)] = $paramValue;
      }

      $found = TRUE;

      foreach ($paramKeyArr as $key) {
        if (!isset($paramValue[$key])) {
          $found = FALSE;
          break;
        }

        $paramValue = $paramValue[$key];
      }

      if ($found) {
        $cacheConfig[$paramKey] = $paramValue;
        return $paramValue;
      }
    }

    return NULL;
  }

  public static function getCSVformat($fileName)
  {
    $fileName = self::rootPath() . 'Resources/config/CSV/' . $fileName . '.csv.yml';

    return Yaml::parse($fileName);
  }

  public static function getInterface($fileName)
  {
    $fileName = self::rootPath() . 'Resources/config/Interface/' . $fileName . '.yml';

    return Yaml::parse($fileName);
  }
}
