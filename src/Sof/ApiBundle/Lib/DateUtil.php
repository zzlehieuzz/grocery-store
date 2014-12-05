<?php

/**
 * Config
 * @author HieuNld 2014/01/08
 */
namespace Sof\ApiBundle\Lib;

class DateUtil
{

    const FORMAT_DATE_HIS     = 'H:i:s';
    const FORMAT_DATE_N       = 'N';
    const FORMAT_DATE_YM_NOT  = 'Ym';
    const FORMAT_DATE_YMD_NOT = 'Ymd';
    const FORMAT_DATE_YMD     = 'Y-m-d';
    const FORMAT_DATE_TIME    = 'Y-m-d H:i:s';
    const FORMAT_MONTH        = 'm';
    const FORMAT_DAY          = 'd';
    const FORMAT_LAST_DAY     = 't';

    const DAYS    = 86400;
    const HOURS   = 3600;
    const MINUTES = 60;

    const RETURN_DAYS       = 1;
    const RETURN_HOURS      = 2;
    const RETURN_MINUTES    = 3;
    const RETURN_SECONDS    = 4;

    /**
     * @param $format
     * @return \Datetime object
     * @author Hieunld 2014/01/08
     */
    public static function getTimeNow($format = 'now')
    {
        return new SofDateTime($format);
    }

    /**
     * @param $format
     * @param $value
     * @return string
     * @author Hieunld 2014/01/15
     */
    public static function getDateFormat($value, $format)
    {
        if (!$format) {
            $format = self::FORMAT_DATE_TIME;
        }

        if ($value instanceof \DateTime) {
            return $value->format($format);
        }

        return date($format, $value);
    }

  /**
   * @param null $value
   * @param null $compareDate
   * @param int $time
   * @return \Datetime object
   * @author Hieunld 2014/01/08
   */
    public static function minusDate($value = null, $compareDate = null, $time = self::RETURN_SECONDS) {
        if (!$value) {
            $value = self::getTimeNow();
        }

        if (!$compareDate) {
            $compareDate = self::getTimeNow();
        }

        $diff = $value->getTimestamp() - $compareDate->getTimestamp();

        switch ($time) {
            case self::RETURN_DAYS;
                $result = intval($diff / self::DAYS);
                break;

            case self::RETURN_HOURS;
                $result = intval($diff / self::HOURS);
                break;

            case self::RETURN_MINUTES;
                $result = intval($diff / self::MINUTES);
                break;

            default:
                $result = intval($diff);
        }

        return $result;
    }

    public static function modify($date, $string)
    {
        if($date instanceof \DateTime) {
            $date = clone $date;
            return $date->modify($string);
        }
    }

    public static function convertDateTimeToInsert($date)
    {
        return new SofDateTime(date_format($date, self::FORMAT_DATE_TIME));
    }

    public static function validateDate($date, $format = self::FORMAT_DATE_TIME)
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function getCurrentDate($format = self::FORMAT_DATE_YMD)
    {
        return DateUtil::getDateFormat(self::getTimeNow(), $format);
    }
}

class SofDateTime extends \DateTime {
    public function __toString()
    {
        return $this->format('U');
    }
}
