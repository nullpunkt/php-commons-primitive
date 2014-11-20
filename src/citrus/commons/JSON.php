<?php
namespace citrus\commons;

/**
 * @author Tobias Seipke <tobias.seipke@gmail.com>
 */
class JSON {

    /**
     * Transforms $object to a json string and additionally adds all getter methods "suffix get"
     *
     * @param $object
     * @return string
     */
    public static function toJson($object) {
        return json_encode(self::_toJson($object)); 
    }
    
    private static function _toJson($var, &$ret=NULL) {
        
        // array
        if(is_array($var)) {
            $ret = array();
            foreach($var as $idx => $tmp)
                $ret[$idx] = self::_toJson($tmp);
            return $ret;
        }
        
        if(is_object($var)) {
            $ret = array();
            // object
            $class = new \ReflectionClass(get_class($var));
            $methods = $class->getMethods();
            foreach($methods as $method) {
                $methodName = $method->name;
                if(strpos($methodName, 'get')===0) {
                    $index = strtolower(substr($methodName, 3, 1)).substr($methodName, 4);
                    $value = $method->invoke($var);
                    if(is_object($value))
                        $value = self::_toJson($value);
                    $ret[$index] = $value;
                }
            }
            
            return $ret;
        }
        
        return $var;
        
    }
}

?>
