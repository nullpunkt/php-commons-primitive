<?php
namespace citrus\commons;

/**
 * @author Tobias Seipke <tobias.seipke@gmail.com>
 */
class Arrays {

    /**
     * Searches for $value in the $array and returns the index if found, otherwise NULL is returned.
     * @param array $array The array within the value should be searched.
     * @param mixed $value The value which should be searched for.
     * @return mixed|NULL
     */
    static function indexOf(array $array, $value) {
        foreach($array as $index => $v) {
            if($v===$value) {
                return $index;
            }
        }
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
     * @param array $array
     * @param mixed $key
     * @param mixed $value
     * @return boolean Returns true if the key was created
     */
    static function createKeyIfNotExists(array &$array, $key, $value) {
        if(!array_key_exists($key, $array)) {
            $array[$key] = $value;
            return true;
        }
        return false;
    }

    /**
     * Insert $value into array if the value doesn't exist. When $key is not NULL it's used as key. Returns true if the value was created.
     * @param array $array
     * @param mixed $value
     * @param null $key
     * @return bool
     */
    static function createValueIfNotExists(array &$array, $value, $key=NULL) {
        $exists = in_array($value, $array);
        if(!$exists) {
            if($key!==NULL)$array[$key] = $value;
            else $array[] = $value;
        }
        return !$exists;
    }

    /**
     * Searches for $key in $array and returns its value - otherwise $default is returned
     * @param array $array
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    static function getValueIfKeyExists(array $array, $key, $default) {
        return (array_key_exists($key, $array)) ? $array[$key] : $default;
    }

    /**
     * Splits an array into parts of $partSize and returns an array containing the parts.
     * @param $array
     * @param $partSize
     * @return array
     * @throws \InvalidArgumentException
     */
    static function split(array $array, $partSize) {
        $ret = array();
        $tmp = array();
        if(!is_numeric($partSize)) {
            throw new \InvalidArgumentException("The partSize must be numeric.");
        }
        if($partSize<1) {
            $partSize = 1;
        }
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

    /**
     * Generates a string from array
     * @param array $array
     * @return string
     */
    static function toString(array $array) {
        $ret = "";
        foreach($array as $index => $value) {
            $ret .= ($ret=="") ? "".$index.": ".$value : ", ".$index.": ".$value;
        }
        return $ret;
    }

    /**
     * Returns the first value of $array
     * @param array $array
     * @return mixed|NULL
     */
    static function firstValue(array $array) {
        foreach($array as $g => $v) {
            return $v;
        }
        return NULL;
    }

    /**
     * Returns the $n'th value of $array
     * @param array $array
     * @param int $n
     * @return mixed|NULL
     */
    static function nthValue(array $array, $n=1) {
        $cnt=0;
        foreach($array as $g => $v) {
            if(++$cnt==$n) {
                return $v;
            }
        }
        return NULL;
    }

    /**
     * If the given $array contains only one index, it will be returned. Otherwise null ist returned.
     * @param array $array
     * @return int|null|string
     */
    static function oneIndex(array $array) {
        if(count($array)!=1)return NULL;
        foreach($array as $g => $v) {
            return $g;
        }
        return NULL;
    }

    /**
     * Returns the first index of $array
     * @param $array
     * @return int|string|null
     */
    static function firstIndex(array $array) {
        foreach($array as $g => $v) {
            return $g;
        }
        return NULL;
    }

    /**
     * Merges arrays and maintains indexes.
     * @param array $arr1
     * @param array $arr2
     * @param array $arr3 (optional)
     * @param array $arr4 (optional)
     * @return array
     */
    static function aarray_merge(array $arr1, array $arr2, array $arr3=NULL, array $arr4=NULL) {
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
    static function keysExist(array $keys, array $array) {
        return (count(array_intersect($keys, array_keys($array)))==count($keys));
    }

    /**
     * Check if the path in the given array exists. Eg.: $array = $array["foo"]["bar"]; keyPathExists($array, array("foo", "bar") returns true;
     * @param array $array
     * @param array $path
     * @return bool
     */
    static function keyPathExists(array $array, array $path) {
        foreach($path as $key) {
            if(!array_key_exists($key, $array)) {
                return false;
            }
            $array = $array[$key];
        }
        return true;
    }

    /**
     * Return the value in the given keypath. Returns $default (default=null) if not exist
     * @param array $array
     * @param array $path
     * @param null|mixed $default (default=null)
     * @return null|mixed
     */
    static function keyPathValue(array $array, array $path, $default=NULL) {
        if(!is_array($path)) {
            return $default;
        }
        foreach($path as $key) {
            if(!is_array($array) || !array_key_exists($key, $array))
                return $default;
            $array = $array[$key];
        }
        return $array;
    }

    /**
     * Returns an array where index and value are the indexes of input array
     * @param array $array
     * @return array
     */
    static function indexAsValue(array $array) {
        foreach($array as $idx => $val) {
            $array[$idx] = $idx;
        }
        return $array;
    }

    /**
     * Returns an array where index and value are the values of input array
     * @param array $array
     * @return array
     */
    static function valueAsIndex(array $array) {
        $ret = array();
        foreach($array as $idx => $val) {
            $ret[$val] = $val;
        }
        return $ret;
    }

    /**
     * Removes all entries of $array where array value = $value, $value could be an array of values to remove
     * @param array $array
     * @param mixed|array $value
     * @return array
     */
    static function removeValue(array $array, $value) {
        $ret = array();
        foreach($array as $val) {
            if(
                (is_array($value)&&!in_array($val, $value)) ||
                (!is_array($value)&&$val!=$value)
            ) {
                $ret[] = $val;
            }
        }
        return $ret;
    }

    /**
     * Removes all entries of $array where key = $key, $key could be an array of keys to remove
     * @param array $array
     * @param string $key
     * @return array
     */
    static function removeKey(array $array, $key="") {
        $ret = array();
        foreach($array as $k => $val) {
            if(
                (is_array($key)&&!in_array($k, $key)) ||
                (!is_array($key)&&$k!=$key)
            ) {
                $ret[$k] = $val;
            }
        }
        return $ret;
    }

    /**
     * Maps a new array by using $indexField for index and $valueField for the value
     * @param array $array
     * @param int|string $indexField
     * @param int|string $valueField
     * @return array
     */
    static function map(array $array, $indexField, $valueField) {
        $ret = array();
        foreach($array as $values) {
            $ret[$values[$indexField]] = $values[$valueField];
        }
        return $ret;
    }

    /**
     * Flattens a multidimensional $array into a simple array using $seperator for seperating nesting levels
     * @param array $array
     * @param string $seperator
     * @return array
     */
    static function flat(array $array, $seperator='.') {
        $ret = array();
        self::_flat($ret, $array, $seperator);
        return $ret;
    }

    /**
     * Helper function for Arrays::flat()
     * @param $ret
     * @param $array
     * @param $seperator
     * @param string $prefix
     */
    private static function _flat(&$ret, $array, $seperator, $prefix='') {
        foreach($array as $index=> $value) {
            if(is_object($value)) continue;
            elseif(is_array($value)) self::_flat($ret, $value, $seperator, $prefix.$index.$seperator);
            else $ret[$prefix.$index] = $value;
        }
    }

    /**
     * Returns null if $array is null, no array or an empty array
     * @param array|null $array
     * @return bool
     */
    static function isEmpty($array) {
        return
            $array===NULL
            || !is_array($array)
            || count($array)===0
        ;
    }

    /**
     * Returns true if all $keys exist in $array
     * @param array $keys
     * @param array $array
     * @return boolean
     */
    static function keys_exits(array $array, array $keys) {
        return (count(array_intersect($keys, array_keys($array)))==count($keys));
    }

    /**
     * Does the php function explode and removes all resulting elements which are empty
     * @param string $delimiter
     * @param string $string
     * @return array
     */
    static function explode($delimiter, $string) {
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
     * Loops over $array and puts $prefix in front of each value and returns the result
     * @param array $array
     * @param string $prefix
     * @return array
     */
    static function appendPrefix(array $array, $prefix) {
        $ret = array();
        foreach($array as $idx => $val) {
            $ret[$idx] = $prefix.$val;
        }
        return $ret;
    }

    /**
     * Fills $source with $value till its count() == $size
     * @param array $source
     * @param int $size
     * @param mixed $value
     */
    static function fill(array &$source, $size, $value) {
        if(!is_array($source) || !is_int($size)) {
            return;
        }
        while(count($source)<$size) {
            array_push($source, $value);
        }
    }
} 