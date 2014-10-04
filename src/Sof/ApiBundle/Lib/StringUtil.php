<?php

namespace Sof\ApiBundle\Lib;


class StringUtil {

  public static function contains($haystack, $needle)
  {
    if (strpos($haystack, $needle) === false) { // not found
      return false;
    } else {
      return true;
    }
  }

  public static function startsWith($haystack, $needle)
  {
    return !strncmp($haystack, $needle, strlen($needle));
  }

  public static function endsWith($haystack, $needle)
  {
    $length = strlen($needle);
    if ($length == 0) {
      return true;
    }

    return (substr($haystack, -$length) === $needle);
  }

  public static function formatCode($num, $length)
  {
    return str_pad($num, $length, '0', STR_PAD_LEFT);
  }

  /**
   * Generate a random string
   * @author Anhnt 2013/05/18
   * Examples
   * str_rand() => m2dy5ofe
   * str_rand(15) => remdjynd47b66hq
   * str_rand(15, 'numeric') => 504359393089603
   * str_rand(15, '01') => 111001110111101
   */
  public static function strRand($length = null, $output = null) {
    // Possible seeds
    $outputs['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
    $outputs['numeric'] = '0123456789';
    $outputs['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
    $outputs['hexadec'] = '0123456789abcdef';

    // Choose seed
    if (isset($outputs[$output])) {
      $output = $outputs[$output];
    }

    // Seed generator
    list($usec, $sec) = explode(' ', microtime());
    $seed = (float) $sec + ((float) $usec * 100000);
    mt_srand($seed);

    // Generate string
    $str = '';
    $output_count = strlen($output);
    for ($i = 0; $length > $i; $i++) {
      $str .= $output{mt_rand(0, $output_count - 1)};
    }

    return $str;
  }

  public static function truncate($str, $length, $suffix = '')
  {
    if (mb_strlen($str, 'UTF-8') > $length) {
      $str = mb_substr($str, 0, $length, 'UTF-8') . $suffix;
    }

    return $str;
  }

  public static function cutString(&$str, $length)
  {
    $sub = mb_substr($str, 0, $length, 'UTF-8');
    $str = mb_substr($str, $length, mb_strlen($str, 'UTF-8') - $length, 'UTF-8');

    return $sub;
  }
}
