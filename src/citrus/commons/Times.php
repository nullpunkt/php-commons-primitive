<?php
namespace citrus\commons;

/**
 * @author Tobias Seipke <tobias.seipke@gmail.com>
 */
class Times {
    
    static function toSmartGermanTime($time) {
        
        $text = date('d.m.y H:i', $time);
        $diff = time()-$time;
        $dayDiff = floor($diff / (60*60*24));
        
        switch($dayDiff) {
            case 0:
                $secondsToNow = time() - $time;
                switch($secondsToNow) {
                    case ($secondsToNow<=1):
                        $text = 'jetzt';
                        break;
                    case ($secondsToNow<=60):
                        $text = 'vor '.$secondsToNow.' Sekunden';
                        break;
                    case ($secondsToNow<=3600):
                        $minutes = floor($secondsToNow/60);
                        $text = 'vor '.$minutes.' Minuten';
                        break;
                    case ($secondsToNow<=86400):
                        $hours = floor($secondsToNow/60/60);
                        $text = 'vor '.$hours.' Stunden';
                        break;
                }
                break;
            case 1:
                $text = 'Gestern um '.date('H:i', $time).' Uhr';
                break;
            case 2:
                $text = 'Vorgestern um '.date('H:i', $time).' Uhr';
                break;
        }
        
        return $text;
    }
 
    
    static function toSmartGermanDay($time) {
        
        
        
        $diff = time()-$time;
        
        $dayDiff = floor($diff / (60*60*24));
        $text = 'vor '.$dayDiff.' Tagen';
        
        switch($dayDiff) {
            case 0:
                $secondsToNow = time() - $time;
                switch($secondsToNow) {
                    case ($secondsToNow<=1):
                        $text = 'jetzt';
                        break;
                    case ($secondsToNow<=60):
                        $text = 'vor '.$secondsToNow.' Sekunden';
                        break;
                    case ($secondsToNow<=3600):
                        $minutes = floor($secondsToNow/60);
                        $text = 'vor '.$minutes.' Minuten';
                        break;
                    case ($secondsToNow<=86400):
                        $hours = floor($secondsToNow/60/60);
                        $text = 'vor '.$hours.' Stunden';
                        break;
                }
                break;
            case 1:
                $text = 'Gestern um '.date('H:i', $time).' Uhr';
                break;
            case 2:
                $text = 'Vorgestern um '.date('H:i', $time).' Uhr';
                break;
            default:
                if($dayDiff<7) {
                    $text = 'die letzten 7 Tage';
                    break;
                }
                if($dayDiff<30) {
                    $text = 'die letzten 30 Tage';
                    break;
                }
            break;
        }
        
        return $text;
    }
}

?>
