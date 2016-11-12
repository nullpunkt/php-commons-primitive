<?php
namespace citrus\commons\test;

use citrus\commons\Arrays;

class ArraysTest extends \PHPUnit_Framework_TestCase
{

    public function testDeflat() {

        $input = [
            'hi.my.name.is' => 'Horst',
            'hi.my.name.was' => 'Biene',
        ];

        $result = Arrays::deflat($input);

        $this->assertEquals('Horst', Arrays::keyPathValue($result, ['hi', 'my', 'name', 'is']));
        $this->assertEquals('Biene', Arrays::keyPathValue($result, ['hi', 'my', 'name', 'was']));
    }

    public function testInsert() {
        $array = array();
        $path = ['bar', 'baz'];
        Arrays::insert($array, $path, 'foo');
        $this->assertEquals('foo', Arrays::keyPathValue($array, $path));
        Arrays::insert($array, $path, 'moo');
        $this->assertEquals('moo', Arrays::keyPathValue($array, $path));
    }

}
