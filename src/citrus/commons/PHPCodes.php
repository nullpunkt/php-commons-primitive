<?php
namespace citrus\commons;

/**
 * @author Tobias Seipke <tobias.seipke@gmail.com>
 */
class PHPCodes {
    
    public static function inspect($code) {
        
        return array(
            'namespace' => self::extractNamespace($code),
            'uses' => self::extractUse($code)
        );
        
    }
    
    public static function extractNamespace($code) {
        $start = strpos($code, 'namespace ') + strlen('namespace ');
        $ret = substr($code, $start, strpos($code, ';', $start) - $start);
        return $ret;
    }
    
    public static function extractUse($code) {
        $code = substr($code, 0, strpos($code, '{'));
        $uses = array();
        while(strpos($code, 'use ')!==FALSE) {
            $start = strpos($code, 'use ') + strlen('use ');
            $uses[] = substr($code, $start, strpos($code, ';', $start) - $start);
            $code = substr($code, $start);
        }
        return $uses;
    }
    
    public static function extractClassCode($file, $class=NULL) {
        if($class===NULL)
            $class = self::extractClassName($file);
        $string = file_get_contents($file);
        return self::extractClassCodeFromString($string, $class);
    }
    
    public static function extractClassCodeFromString($string, $class) {
        return self::extractCode($string, 'class '.$class);
    }
    
    public static function extractFunctionCode($file, $function) {
        $string = file_get_contents($file);
        return self::extractFunctionCodeFromString($string, $function);
    }
    
    public static function extractFunctionCodeFromString($string, $function) {
        return self::extractCode($string, 'function '.$function);
    }
    
    public static function extractMethodCalls($file) {
        $string = file_get_contents($file);
        return self::extractMethodCallsFromString($string);
    }
    
    // aktuell unterstützt: $this
    public static function extractMethodCallsFromString($string) {
        $offset = 0;
        $ret = array();
        while(strpos($string, '$this', $offset)!==FALSE) {
            $offset = strpos($string, '$this', $offset);
            $call = substr($string, $offset, strpos($string, '(', $offset)-$offset);
            $explode = explode('->', $call);
            array_shift($explode);
            if(count($explode)==1||count($explode)==2)
                $ret[] = $explode;
            $offset++;
        }
        return $ret;
    }
    
    private static function extractCode($string, $start) {
        
        if(strpos($string, $start)===FALSE)return "";
        $current = strpos($string, $start);
        
        $current = strpos($string, '{', $current)+1;
//        vardump($start, $current, substr($string, $current, 10));
        $bracketCount = 1;
        $len = strlen($string);
        $ret = "{";
        $ignore = FALSE;
        
        while($current < $len && $bracketCount>0) {
            $char = substr($string, $current, 1);
            $twoChar = "xx";
            $ignores = array(
                "//" => "\n",
                "/*" => "*/",
                '"' => '"',
                "'" => "'"
            );
            if($ignore!==FALSE) {
                $twoChar = substr($string, $current, 2);
                if(array_key_exists($char, $ignores))
                    $ignore = $ignores[$char];
                elseif(array_key_exists($twoChar, $ignores))
                    $ignore = $ignores[$twoChar];
            } elseif($char==$ignore||$twoChar==$ignore) {
                $ignore = FALSE;
            }
            
            if($char=='{'&&!$ignore) {
                $char = "{";
                $bracketCount++;
            }
            if($char=='}'&&!$ignore) {
                $bracketCount--;
                $char = "}";
            }
            
//            if($char=="\n")$char.= "|".($ignore?"IGNORE":"ignore")."|".($ignore)."|";
            
            $current++;
            $ret .= $char;
        }
        if($bracketCount!==0) {
            throw new CitrusException('Unexpected bracket count ('.$bracketCount.')');
        }
        return $ret;
    }
}

?>
