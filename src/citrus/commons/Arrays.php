<?php
namespace citrus\commons;

/**
 * @author Tobias Seipke <tobias.seipke@gmail.com>
 */
class Arrays {

    /**
     * Searches for $value in the $array and returns the index if found, otherwise NULL is returned.
     *
     * @param array $array The array within the value should be searched.
     * @param mixed $value The value which should be searched for.
     * @return mixed|NULL
     */
    static function indexOf($array, $value) {
        foreach($array as $index => $v)
            if($v===$value)return $index;
        return NULL;
    }


    /**
     * Casts value to an array in case it isn't already one. Returns true whether the value was transformed to an array.
     * @param mixed $value
     * @return boolean
     */
    static function cast(&$value) {
        if(is_object($value)) {
            $object= $value;
            $reflectionClass = new \ReflectionClass(get_class($object));
            $array = array();
            foreach ($reflectionClass->getProperties() as $property) {
                $property->setAccessible(true);
                $array[$property->getName()] = $property->getValue($object);
                $property->setAccessible(false);
            }
            $value = $array;
        }
        $is_array = is_array($value);
        if(!$is_array) {
            $value = array($value);
        }
        return !$is_array;
    }

    /**
     * Creates an index at position $key in $array if the key doesn't exist. If an index is created the value is set to $value.
     * @param type $array
     * @param type $key
     * @param type $value
     * @return boolean Returns true if the key was created
     */
    static function createKeyIfNotExists(&$array, $key, $value) {
        if(is_null($array)) {
            vardump(array(
                "error createKeyIfNotExists",
                "array" => $array,
                "key" => $key,
                "value" => $value
            ));
        }
        if(!array_key_exists($key, $array)) {
            $array[$key] = $value;
            return true;
        }
        return false;
    }


    /**
     * Insert $value into array if the value doesn't exist. When $key is not NULL it's used as key. Returns true if the value was created.
     *
     * @param type $array
     * @param type $value
     * @param type $key
     * @return
     */
    static function createValueIfNotExists(&$array, $value, $key=NULL) {
        $exists = in_array($value, $array);
        if(!$exists) {
            if($key!==NULL)$array[$key] = $value;
            else $array[] = $value;
        }
        return !$exists;
    }

    /**
     * Searches for $key in $array and returns its value - otherwise $default is returned
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    static function getValueIfKeyExists($array, $key, $default) {
        return (array_key_exists($key, $array)) ? $array[$key] : $default;
    }


    /**
     * Splits an array into parts of $partSize and returns an array containing the parts.
     * @param type $array
     * @param int $partSize
     * @return array
     */
    static function split($array, $partSize) {
        $ret = array();
        $tmp = array();
        if(!is_numeric($partSize))
            throw new Exception("The partSize must be numeric.");
        if($partSize<1)$partSize = 1;
        foreach($array as $index => $value) {
            $tmp[$index] = $value;
            if(count($tmp)==$partSize) {
                $ret[] = $tmp;
                unset($tmp);
                $tmp = array();
            }
        }
        if(count($tmp)>0)
            $ret[] = $tmp;
        return $ret;
    }


    static function toString($array) {
        $ret = "";
        foreach($array as $index => $value)
            $ret .= ($ret=="") ? "".$index.": ".$value : ", ".$index.": ".$value;
        return $ret;
    }

    static function fromDoctrineCollection(Doctrine_Collection $doctrineCollection, $keyField, $valueField) {
        $ret = array();
        $keys = $doctrineCollection->getKeys();
        foreach($keys as $key) {
            $entity = $doctrineCollection->get($key);
            self::createKeyIfNotExists($ret, $entity->$keyField, $entity->$valueField);
        }
        return $ret;
    }


    static function firstValue($array) {
        if(!is_array($array))return $array;
        if(count($array)===0)return NULL;
        foreach($array as $g => $v)return $v;
    }

    static function nthValue($array, $n=1) {
        if(!is_array($array))throw Exception('Given value is not an array.');
        $cnt=0;
        foreach($array as $g => $v)if(++$cnt==$n)return $v;
    }

    static function oneIndex($array) {
        if(count($array)!=1)return NULL;
        foreach($array as $g => $v)return $g;
    }

    static function firstIndex($array) {
        if(!is_array($array))return $array;
        foreach($array as $g => $v)return $g;
    }

    /**
     * Merges arrays and maintains indexes.
     *
     * @param array $arr1
     * @param array $arr2
     * @param array $arr3
     * @return array
     */
    static function aarray_merge($arr1, $arr2, $arr3=NULL, $arr4=NULL) {
        $ret = array();
        foreach($arr1 as $i => $v)$ret[$i] = $v;
        foreach($arr2 as $i => $v)$ret[$i] = $v;
        if($arr3!==NULL)
            foreach($arr3 as $i => $v)$ret[$i] = $v;
        if($arr4!==NULL)
            foreach($arr4 as $i => $v)$ret[$i] = $v;
        return $ret;
    }


    /**
     * Returns true if all $keys exist in $array
     * @param array $keys
     * @param array $array
     * @return boolean
     */
    static function keysExist($keys, $array) {
        return (count(array_intersect($keys, array_keys($array)))==count($keys));
    }

    /**
     * Check if the path in the given array exists. Eg.: $array = $array["foo"]["bar"]; keyPathExists($array, array("foo", "bar") returns true;
     * @param type $keys
     * @param type $path
     */
    static function keyPathExists($array, $path=array()) {
        foreach($path as $key) {
            if(!array_key_exists($key, $array))
                return false;
            $array = $array[$key];
        }
        return true;
    }


    /**
     * Return the value in the given keypath. Returns null if not exist;
     * @param type $keys
     * @param type $path
     */
    static function keyPathValue($array, $path) {
        if(!is_array($array) || !is_array($path)) {
            return NULL;
        }
        foreach($path as $key) {
            if(!array_key_exists($key, $array))
                return null;
            $array = $array[$key];
        }
        return $array;
    }

    /**
     * Kopiert den index und setzt ihn jeweil als value
     * @param array $array
     * @return array
     */
    static function indexAsValue($array) {
        foreach($array as $idx => $val)
            $array[$idx] = $idx;
        return $array;
    }

    /**
     * Kopiert den value und setzt ihn jeweil als index
     * @param array $array
     * @return array
     */
    static function valueAsIndex($array) {
        $ret = array();
        foreach($array as $idx => $val)
            $ret[$val] = $val;
        return $ret;
    }

    /**
     * Removes all indexes with value $value
     * @param array $array
     * @param mixed $value
     */
    static function removeValue(&$array, $value) {
        $tmp = array();
        foreach($array as $v)
            if($v!=$value)
                $tmp[] = $v;
        $array = $tmp;
    }

    /**
     * Removes all entries in $array with value $cleanIt
     * @param array $array
     * @param string|array $cleanIt
     */
    static function cleanUp($array, $cleanIt="") {
        $ret = array();
        foreach($array as $val)
            if(
                (is_array($cleanIt)&&!in_array($val, $cleanIt)) ||
                (!is_array($cleanIt)&&$val!=$cleanIt)
            )
                $ret[] = $val;
        return $ret;
    }

    /**
     * Removes all entries in $array with key $cleanIt
     * @param array $array
     * @param string $cleanIt
     */
    static function cleanUpIndex($array, $cleanIt="") {
        $ret = array();
        foreach($array as $key => $val)
            if(
                (is_array($cleanIt)&&!in_array($key, $cleanIt)) ||
                (!is_array($cleanIt)&&$key!=$cleanIt)
            )
                $ret[$key] = $val;
        return $ret;
    }

    /**
     * Maps a new array by using $indexField for index and $valueField for the value
     * @param type $array
     * @param type $indexField
     * @param type $valueField
     * @return array
     */
    static function map($array, $indexField, $valueField) {
        $ret = array();
        foreach($array as $values) {
            $ret[$values[$indexField]] = $values[$valueField];
        }
        return $ret;
    }

    /**
     * Flattens a multidimensional $array into a 2 dimensional array using $seperator for seperating indexes
     * @param type $array
     * @param type $seperator
     * @return array
     */
    static function flat($array, $seperator='.') {
        $ret = array();
        self::_flat($ret, $array, $seperator);
        return $ret;
    }

    private static function _flat(&$ret, $array, $seperator, $prefix='') {
        foreach($array as $index=> $value) {
            if(is_object($value)) continue;
            elseif(is_array($value)) self::_flat($ret, $value, $seperator, $prefix.$index.$seperator);
            else $ret[$prefix.$index] = $value;
        }
    }

    static function sortObjectsByMethod(&$array, $method, $desc=false) {
        $methodName = uniqid('sort');
        eval('function '.$methodName.'($a, $b) {
            $a = $a->'.$method.'(); $b = $b->'.$method.'();
            if ($a == $b) return 0;
            return ($a '.($desc?'>':'<').' $b) ? -1 : 1;
        }');
        uasort($array, $methodName);
    }

    static function sortArraysByValue(&$array, $valueIndex, $desc=false) {
        $methodName = uniqid('sort');
        eval('function '.$methodName.'($a, $b) {
            $a = $a["'.$valueIndex.'"]; $b = $b["'.$valueIndex.'"];
            if ($a == $b) return 0;
            return ($a '.($desc?'>':'<').' $b) ? -1 : 1;
        }');
        uasort($array, $methodName);
    }

    static function copyValues($indexes, $source, &$sink, $default=array()) {
        foreach($indexes as $index)
            if(array_key_exists($index, $source))
                $sink[$index] = $source[$index];
            elseif(array_key_exists($index, $default))
                $sink[$index] = $default[$index];
    }

    /**
     * Assigns the value of the $variable to the value in $index of the $array. Assings $default if array key doesn't exist.
     *
     *
     * @param type $array
     * @param type $index
     * @param type $variable
     * @param type $default
     */
    static function assignValue($array, $index, &$variable, $default=NULL) {
        $variable = (array_key_exists($index, $array))
            ? $array[$index]
            : $default;
    }

    static function isEmpty($array) {
        if($array===NULL)return true;
        if(!is_array($array))return true;
        return count($array)==0;
    }

    /**
     * Returns true if all $keys exist in $array
     * @param array $keys
     * @param array $array
     * @return boolean
     */
    static function keys_exits($keys, $array) {
        return (count(array_intersect($keys, array_keys($array)))==count($keys));
    }

    static function replace(&$array, $find, $replace) {
        if(!is_array($array))return;
        foreach($array as &$value) {
            if(is_object($value))
                self::cast($value);
            if(is_array($value))
                self::replace($value, $find, $replace);
            if($value===$find)
                $value = $replace;
        }
    }

    /**
     * Does the php functin explode and removes all resulting elements which are empty
     * @param type $delimiter
     * @param type $string
     * @return type
     */
    static function explodeClean($delimiter, $string) {
        $ret = array();
        $tmp = explode($delimiter, $string);
        foreach($tmp as $val) {
            if(trim($val)!=='') {
                $ret[] = $val;
            }
        }
        return $ret;
    }

    /**
     * Loops over $array and puts $prefix in front of each value. A new array is returned.
     * @param array $array
     * @param type $prefix
     * @return array
     */
    static function appendPrefix($array, $prefix) {
        $ret = array();
        foreach($array as $idx => $val) {
            $ret[$idx] = $prefix.$val;
        }
        return $ret;
    }

    /**
     * Fills $source with $value till its count() == $size
     *
     * @param $source
     * @param $size
     * @param $value
     */
    static function fill(&$source, $size, $value) {
        if(!is_array($source) || !is_int($size)) {
            return;
        }
        while(count($source)<$size) {
            array_push($source, $value);
        }
    }
} 