<?php
namespace citrus\commons;

/**
 * @author Tobias Seipke <tobias.seipke@gmail.com>
 */
class Classes {

    /**
     * Returns the inheritance of the given class
     *
     * @param $className
     * @param array $ret
     * @return array
     */
    public static function extractInheritance($className, &$ret=array()) {
        if(!in_array($className, $ret))
            $ret[] = $className;
        $reflectionClass = new \ReflectionClass($className);
        if($reflectionClass->getParentClass())
            self::extractInheritance($reflectionClass->getParentClass()->name, $ret);
        foreach($reflectionClass->getInterfaceNames() as $name) {
            self::extractInheritance($name, $ret);
        }
        return $ret;
    }

    /**
     * Returns the @var annotation of a given $property
     *
     * @param \ReflectionProperty $property
     * @return string
     */
    public static function getVarAnnotationClass(\ReflectionProperty $property) {
        $doc = $property->getDocComment();
        if(strpos($doc, '@var ')===FALSE)return NULL;
        $start = strpos($doc, '@var ')+5;
        $length = strpos($doc, "\n", $start)-$start;
        $class = substr($doc, $start, $length);
        while(ord($class[strlen($class)-1])==13)
            $class = substr($class, 0, strlen($class)-1);
        if(strpos($class, '\\')===0) 
            $class = substr($class, 1);
        return trim($class);
    }

    /**
     * Loads the given file by require_once and returns a list of newly declared classes
     * @param $file
     * @return array
     */
    public static function loadFromFile($file) {
        $before = get_declared_classes();
        require_once $file;
        $classNames = array_diff(get_declared_classes(), $before);
        return $classNames;
    }
}
