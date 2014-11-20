<?php
namespace citrus\commons;

/**
 * @author Tobias Seipke <tobias.seipke@gmail.com>
 */
class Dates {
    
    public static function germanDate($time) {
        $months = array(
            1 => "Januar",
            2 => "Februar",
            3 => "März",
            4 => "April",
            5 => "Mai",
            6 => "Juni",
            7 => "Juli",
            8 => "August",
            9 => "September",
            10 => "Oktober",
            11 => "November",
            12 => "Dezember"
        );
        return date("j", $time) . ". " . $months[date("n", $time)] . " " . date("Y", $time);
    }
    
    public static function isSqlDate($date) {
        return (
            strlen($date)===10
            && $date[4]==='-'
            && $date[7]==='-'
            && is_int(substr($date, 0, 4))
            && is_int(substr($date, 5, 2))
            && is_int(substr($date, 8, 2))
        );
    }
    
    public static function fromParsed($parsed) {
        $ret = new \DateTime;
        $ret->setDate($parsed['year'], $parsed['month'], $parsed['day']);
        $ret->setTime($parsed['hour'], $parsed['minute'], $parsed['second']);
        return $ret;
    }
    
    public static function parseDateByFormat($date, $format) {
        $ret = date_parse_from_format($format, $date);
        return self::fromParsed($ret)->getTimestamp();
    }
    
    public static function parse($date) {
        $ret = date_parse($date);
        $formats = array(
            'Y-m-d',
            'd-m-y',
            'd/m/Y'
        );
        if($ret['error_count']>0) {
            foreach($formats as $format) {
                if($ret['error_count']===0) {
                    continue;
                }
                $ret = date_parse_from_format($format, $date);
            }
        }
        if($ret['error_count']>0) {
            return NULL;
        }
        
        return self::fromParsed($ret)->getTimestamp();
    }
}
