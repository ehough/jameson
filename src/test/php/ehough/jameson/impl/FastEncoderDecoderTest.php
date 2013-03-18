<?php
/**
 * Copyright 2006 - 2013 Eric D. Hough (http://ehough.com)
 *
 * This file is part of jameson (https://github.com/ehough/jameson)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Original author...
 *
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Json
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc.
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @category   Zend
 * @package    Zend_JSON
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_JSON
 */
final class ehough_jameson_impl_FastEncoderDecoderTest extends PHPUnit_Framework_TestCase
{
    private $_encoder;

    private $_decoder;

    public function setUp()
    {
        $this->_decoder = new ehough_jameson_impl_FastDecoder();
        $this->_encoder = new ehough_jameson_impl_FastEncoder();

        $this->_encoder->setOptions(array(ehough_jameson_impl_AbstractEncoder::OPTION_USE_NATIVE_ENCODER => false));
        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_USE_NATIVE_DECODER => false));
    }

    public function testJSONWithPhpJSONExtension()
    {
        if (! extension_loaded('json')) {

            $this->markTestSkipped('JSON extension is not loaded');
        }

        $this->_encoder->setOptions(array(ehough_jameson_impl_AbstractEncoder::OPTION_USE_NATIVE_ENCODER => true));
        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_USE_NATIVE_DECODER => true));

        $this->_testJSON(array('string', 327, true, null));
    }

    public function testJSONWithBuiltins()
    {
        $this->_encoder->setOptions(array(ehough_jameson_impl_AbstractEncoder::OPTION_USE_NATIVE_ENCODER => false));
        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_USE_NATIVE_DECODER => false));

        $this->_testJSON(array('string', 327, true, null));
    }

    /**
     * Test encoding and decoding in a single step
     * @param array $values   array of values to test against encode/decode
     */
    protected function _testJSON($values)
    {
        $this->_encoder->setOptions(array(ehough_jameson_impl_AbstractEncoder::OPTION_USE_NATIVE_ENCODER => false));
        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_USE_NATIVE_DECODER => false));

        $encoded = $this->_encoder->encode($values);

        $this->assertEquals($values, $this->_decoder->decode($encoded));

        $this->_encoder->setOptions(array(ehough_jameson_impl_AbstractEncoder::OPTION_USE_NATIVE_ENCODER => true));
        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_USE_NATIVE_DECODER => true));

        $encoded = $this->_encoder->encode($values);

        $this->assertEquals($values, $this->_decoder->decode($encoded));
    }

    /**
     * test null encoding/decoding
     */
    public function testNull()
    {
        $this->_testEncodeDecode(array(null));
    }


    /**
     * test boolean encoding/decoding
     */
    public function testBoolean()
    {
        $this->assertTrue($this->_decoder->decode($this->_encoder->encode(true)));
        $this->assertFalse($this->_decoder->decode($this->_encoder->encode(false)));
    }


    /**
     * test integer encoding/decoding
     */
    public function testInteger()
    {
        $this->_testEncodeDecode(array(-2));
        $this->_testEncodeDecode(array(-1));

        $zero = $this->_decoder->decode($this->_encoder->encode(0));
        $this->assertEquals(0, $zero, 'Failed 0 integer test. Encoded: ' . serialize($this->_encoder->encode(0)));
    }


    /**
     * test float encoding/decoding
     */
    public function testFloat()
    {
        $this->_testEncodeDecode(array(-2.1, 1.2));
    }

    /**
     * test string encoding/decoding
     */
    public function testString()
    {
        $this->_testEncodeDecode(array('string'));
        $this->assertEquals('', $this->_decoder->decode($this->_encoder->encode('')), 'Empty string encoded: ' . serialize($this->_encoder->encode('')));
    }

    /**
     * Test backslash escaping of string
     */
    public function testString2()
    {
        $string   = 'INFO: Path \\\\test\\123\\abc';
        $expected = '"INFO: Path \\\\\\\\test\\\\123\\\\abc"';
        $encoded = $this->_encoder->encode($string);
        $this->assertEquals($expected, $encoded, 'Backslash encoding incorrect: expected: ' . serialize($expected) . '; received: ' . serialize($encoded) . "\n");
        $this->assertEquals($string, $this->_decoder->decode($encoded));
    }

    /**
     * Test newline escaping of string
     */
    public function testString3()
    {
        $expected = '"INFO: Path\nSome more"';
        $string   = "INFO: Path\nSome more";
        $encoded  = $this->_encoder->encode($string);
        $this->assertEquals($expected, $encoded, 'Newline encoding incorrect: expected ' . serialize($expected) . '; received: ' . serialize($encoded) . "\n");
        $this->assertEquals($string, $this->_decoder->decode($encoded));
    }

    /**
     * Test tab/non-tab escaping of string
     */
    public function testString4()
    {
        $expected = '"INFO: Path\\t\\\\tSome more"';
        $string   = "INFO: Path\t\\tSome more";
        $encoded  = $this->_encoder->encode($string);
        $this->assertEquals($expected, $encoded, 'Tab encoding incorrect: expected ' . serialize($expected) . '; received: ' . serialize($encoded) . "\n");
        $this->assertEquals($string, $this->_decoder->decode($encoded));
    }

    /**
     * Test double-quote escaping of string
     */
    public function testString5()
    {
        $expected = '"INFO: Path \"Some more\""';
        $string   = 'INFO: Path "Some more"';
        $encoded  = $this->_encoder->encode($string);
        $this->assertEquals($expected, $encoded, 'Quote encoding incorrect: expected ' . serialize($expected) . '; received: ' . serialize($encoded) . "\n");
        $this->assertEquals($string, $this->_decoder->decode($encoded));
    }

    /**
     * test indexed array encoding/decoding
     */
    public function testArray()
    {
        $array = array(1, 'one', 2, 'two');
        $encoded = $this->_encoder->encode($array);
        $this->assertSame($array, $this->_decoder->decode($encoded), 'Decoded array does not match: ' . serialize($encoded));
    }

    /**
     * test associative array encoding/decoding
     */
    public function testAssocArray()
    {
        $this->_testEncodeDecode(array(array('one' => 1, 'two' => 2)));
    }

    /**
     * test associative array encoding/decoding, with mixed key types
     */
    public function testAssocArray2()
    {
        $this->_testEncodeDecode(array(array('one' => 1, 2 => 2)));
    }

    /**
     * test associative array encoding/decoding, with integer keys not starting at 0
     */
    public function testAssocArray3()
    {
        $this->_testEncodeDecode(array(array(1 => 'one', 2 => 'two')));
    }

    /**
     * test object encoding/decoding (decoding to array)
     */
    public function testObject()
    {
        $value = new \stdClass();
        $value->one = 1;
        $value->two = 2;

        $array = array('__className' => 'stdClass', 'one' => 1, 'two' => 2);

        $this->_encoder->setOptions(array(ehough_jameson_impl_AbstractEncoder::OPTION_USE_NATIVE_ENCODER => false));

        $encoded = $this->_encoder->encode($value);


        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_DECODE_TO_STDCLASS_INSTEAD_OF_ARRAYS => false));

        $this->assertSame($array, $this->_decoder->decode($encoded));
    }

    /**
     * test object encoding/decoding (decoding to stdClass)
     */
    public function testObjectAsObject()
    {
        $value = new \stdClass();
        $value->one = 1;
        $value->two = 2;

        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_DECODE_TO_STDCLASS_INSTEAD_OF_ARRAYS => true));

        $encoded = $this->_encoder->encode($value);
        $decoded = $this->_decoder->decode($encoded);
        $this->assertTrue(is_object($decoded), 'Not decoded as an object');
        $this->assertTrue($decoded instanceof \StdClass, 'Not a StdClass object');
        $this->assertTrue(isset($decoded->one), 'Expected property not set');
        $this->assertEquals($value->one, $decoded->one, 'Unexpected value');
    }

    /**
     * Test that arrays of objects decode properly; see issue #144
     */
    public function testDecodeArrayOfObjects()
    {
        $value = '[{"id":1},{"foo":2}]';
        $expect = array(array('id' => 1), array('foo' => 2));

        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_DECODE_TO_STDCLASS_INSTEAD_OF_ARRAYS => false));

        $this->assertEquals($expect, $this->_decoder->decode($value));
    }

    /**
     * Test that objects of arrays decode properly; see issue #107
     */
    public function testDecodeObjectOfArrays()
    {
        $value = '{"codeDbVar" : {"age" : ["int", 5], "prenom" : ["varchar", 50]}, "234" : [22, "jb"], "346" : [64, "francois"], "21" : [12, "paul"]}';
        $expect = array(
            'codeDbVar' => array(
                'age'   => array('int', 5),
                'prenom' => array('varchar', 50),
            ),
            234 => array(22, 'jb'),
            346 => array(64, 'francois'),
            21  => array(12, 'paul')
        );

        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_DECODE_TO_STDCLASS_INSTEAD_OF_ARRAYS => false));

        $this->assertEquals($expect, $this->_decoder->decode($value));
    }

    /**
     * Test encoding and decoding in a single step
     * @param array $values   array of values to test against encode/decode
     */
    protected function _testEncodeDecode($values)
    {
        $this->_encoder->setOptions(array(ehough_jameson_impl_AbstractEncoder::OPTION_USE_NATIVE_ENCODER => false));
        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_USE_NATIVE_DECODER => false));

        $this->_doTestEncodeDecode($values);

        $this->_encoder->setOptions(array(ehough_jameson_impl_AbstractEncoder::OPTION_USE_NATIVE_ENCODER => true));
        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_USE_NATIVE_DECODER => true));

        $this->_doTestEncodeDecode($values);
    }

    private function _doTestEncodeDecode($values)
    {
        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_DECODE_TO_STDCLASS_INSTEAD_OF_ARRAYS => false));

        foreach ($values as $value) {

            $encoded = $this->_encoder->encode($value);

            if (is_array($value) || is_object($value)) {

                $this->assertEquals($this->_toArray($value), $this->_decoder->decode($encoded));

            } else {

                $this->assertEquals($value, $this->_decoder->decode($encoded));
            }
        }
    }

    protected function _toArray($value)
    {
        if (!is_array($value) || !is_object($value)) {
            return $value;
        }

        $array = array();
        foreach ((array)$value as $k => $v) {
            $array[$k] = $this->_toArray($v);
        }
        return $array;
    }

    /**
     * Test that version numbers such as 4.10 are encoded and decoded properly;
     * See ZF-377
     */
    public function testEncodeReleaseNumber()
    {
        $value = '4.10';

        $this->_testEncodeDecode(array($value));
    }

    /**
     * Tests that spaces/linebreaks prior to a closing right bracket don't throw
     * exceptions. See ZF-283.
     */
    public function testEarlyLineBreak()
    {
        $expected = array('data' => array(1, 2, 3, 4));

        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_DECODE_TO_STDCLASS_INSTEAD_OF_ARRAYS => false));

        $json = '{"data":[1,2,3,4' . "\n]}";
        $this->assertEquals($expected, $this->_decoder->decode($json));

        $json = '{"data":[1,2,3,4 ]}';
        $this->assertEquals($expected, $this->_decoder->decode($json));
    }

    /**
     * @group ZF-504
     */
    public function testEncodeEmptyArrayAsStruct()
    {
        $this->assertSame('[]', $this->_encoder->encode(array()));
    }

    /**
     * @group ZF-504
     */
    public function testDecodeBorkedJsonShouldThrowException1()
    {
        $this->setExpectedException('ehough_jameson_api_exception_RuntimeException');
        $this->_decoder->decode('[a"],["a],[][]');
    }

    /**
     * @group ZF-504
     */
    public function testDecodeBorkedJsonShouldThrowException2()
    {
        $this->setExpectedException('ehough_jameson_api_exception_RuntimeException');
        $this->_decoder->decode('[a"],["a]');
    }

    /**
     * @group ZF-504
     */
    public function testOctalValuesAreNotSupportedInJsonNotation()
    {
        $this->setExpectedException('ehough_jameson_api_exception_RuntimeException');
        $this->_decoder->decode('010');
    }

    /**
     * Tests for ZF-461
     *
     * Check to see that cycling detection works properly
     */
    public function testZf461()
    {
        $item1 = new ZendTest_JSON_Item() ;
        $item2 = new ZendTest_JSON_Item() ;
        $everything = array() ;
        $everything['allItems'] = array($item1, $item2) ;
        $everything['currentItem'] = $item1 ;

        // should not fail
        $this->_encoder->setOptions(array(ehough_jameson_impl_AbstractEncoder::OPTION_CYCLE_CHECK_ENABLED => false));
        $encoded = $this->_encoder->encode($everything);

        // should fail
        $this->setExpectedException('ehough_jameson_api_exception_RecursionException');
        $this->_encoder->setOptions(array(ehough_jameson_impl_AbstractEncoder::OPTION_CYCLE_CHECK_ENABLED => true));
        $this->_encoder->encode($everything);
    }

    /**
     * Test for ZF-4053
     *
     * Check to see that cyclical exceptions are silenced when
     * $option['silenceCyclicalExceptions'] = true is used
     */
    public function testZf4053()
    {
        $item1 = new ZendTest_JSON_Item() ;
        $item2 = new ZendTest_JSON_Item() ;
        $everything = array() ;
        $everything['allItems'] = array($item1, $item2) ;
        $everything['currentItem'] = $item1 ;

        $this->_encoder->setOptions(array(ehough_jameson_impl_AbstractEncoder::OPTION_SILENCE_CYCLICAL_ERRORS => true));

        $encoded = $this->_encoder->encode($everything);
        $json = '{"allItems":[{"__className":"ZendTest_JSON_Item"},{"__className":"ZendTest_JSON_Item"}],"currentItem":"* RECURSION (ZendTest_JSON_Item) *"}';

        $this->assertEquals($json, $encoded);
    }

    public function testEncodeObject()
    {
        $actual  = new ZendTest_JSON_Object();
        $encoded = $this->_encoder->encode($actual);

        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_DECODE_TO_STDCLASS_INSTEAD_OF_ARRAYS => true));

        $decoded = $this->_decoder->decode($encoded);

        $this->assertTrue(isset($decoded->__className));
        $this->assertEquals('ZendTest_JSON_Object', $decoded->__className);
        $this->assertTrue(isset($decoded->foo));
        $this->assertEquals('bar', $decoded->foo);
        $this->assertTrue(isset($decoded->bar));
        $this->assertEquals('baz', $decoded->bar);
        $this->assertFalse(isset($decoded->_foo));
    }

    public function testToJSONSerialization()
    {
        $toJSONObject = new ZendTest_JSON_ToJSONClass($this->_encoder);

        $result = $this->_encoder->encode($toJSONObject);

        $this->assertEquals('{"firstName":"John","lastName":"Doe","email":"john@doe.com"}', $result);
    }

    /**
     * @group ZF-4054
     */
    public function testEncodeWithUtf8IsTransformedToPackedSyntax()
    {
        $data = array("Отмена");
        $result = $this->_encoder->encode($data);

        $this->assertEquals('["\u041e\u0442\u043c\u0435\u043d\u0430"]', $result);
    }

    /**
     * @group ZF-4054
     *
     * This test contains assertions from the Solar Framework by Paul M. Jones
     * @link http://solarphp.com
     */
    public function testEncodeWithUtf8IsTransformedSolarRegression()
    {
        $expect = '"h\u00c3\u00a9ll\u00c3\u00b6 w\u00c3\u00b8r\u00c5\u201ad"';
        $this->assertEquals($expect,           $this->_encoder->encode('hÃ©llÃ¶ wÃ¸rÅ‚d'));
        $this->assertEquals('hÃ©llÃ¶ wÃ¸rÅ‚d', $this->_decoder->decode($expect));

        $expect = '"\u0440\u0443\u0441\u0441\u0438\u0448"';
        $this->assertEquals($expect,  $this->_encoder->encode("руссиш"));
        $this->assertEquals("руссиш", $this->_decoder->decode($expect));
    }

    /**
     * @group ZF-4054
     */
    public function testEncodeUnicodeStringSolarRegression()
    {
        $value    = 'hÃ©llÃ¶ wÃ¸rÅ‚d';
        $expected = '"h\u00c3\u00a9ll\u00c3\u00b6 w\u00c3\u00b8r\u00c5\u201ad"';
        $this->assertEquals($expected, $this->_encoder->encode($value));

        $value    = "\xC3\xA4";
        $expected = '"\u00e4"';
        $this->assertEquals($expected, $this->_encoder->encode($value));

        $value    = "\xE1\x82\xA0\xE1\x82\xA8";
        $expected = '"\u10a0\u10a8"';
        $this->assertEquals($expected, $this->_encoder->encode($value));
    }

    /**
     * @group ZF-4054
     */
    public function testDecodeUnicodeStringSolarRegression()
    {
        $expected = 'hÃ©llÃ¶ wÃ¸rÅ‚d';
        $value    = '"h\u00c3\u00a9ll\u00c3\u00b6 w\u00c3\u00b8r\u00c5\u201ad"';
        $this->assertEquals($expected, $this->_decoder->decode($value));

        $expected = "\xC3\xA4";
        $value    = '"\u00e4"';
        $this->assertEquals($expected, $this->_decoder->decode($value));

        $value    = '\u10a0';
        $expected = "\xE1\x82\xA0";
        //todo: FIX ME!
        //$this->assertEquals($expected, $this->_decoder->decode($value));
    }

    /**
     * @group ZF-4054
     *
     * This test contains assertions from the Solar Framework by Paul M. Jones
     * @link http://solarphp.com
     */
    public function testEncodeWithUtf8IsTransformedSolarRegressionEqualsJSONExt()
    {
        if(function_exists('json_encode') == false) {
            $this->markTestSkipped('Test can only be run, when ext/json is installed.');
        }

        $this->assertEquals(
            json_encode('hÃ©llÃ¶ wÃ¸rÅ‚d'),
            $this->_encoder->encode('hÃ©llÃ¶ wÃ¸rÅ‚d')
        );

        $this->assertEquals(
            json_encode("руссиш"),
            $this->_encoder->encode("руссиш")
        );
    }

    /**
     * @group ZF-4437
     */
    public function testCommaDecimalIsConvertedToCorrectJSONWithDot()
    {
        setlocale(LC_ALL, 'Spanish_Spain', 'es_ES', 'es_ES.utf-8');
        if (strcmp('1,2', (string)floatval(1.20)) != 0) {
            $this->assertTrue(true);
            return;
        }

        $actual = $this->_encoder->encode(array(floatval(1.20), floatval(1.68)));
        $this->assertEquals('[1.2,1.68]', $actual);
    }

    /**
     * @group ZF-8663
     */
    public function testNativeJSONEncoderWillProperlyEncodeSolidusInStringValues()
    {
        $source = "</foo><foo>bar</foo>";
        $target = '"<\\/foo><foo>bar<\\/foo>"';

        // first test ext/json
        $this->_encoder->setOptions(array(ehough_jameson_impl_AbstractEncoder::OPTION_USE_NATIVE_ENCODER => true));
        $this->_decoder->setOptions(array(ehough_jameson_impl_AbstractDecoder::OPTION_USE_NATIVE_DECODER => true));
        $this->assertEquals($target, $this->_encoder->encode($source));
    }

    /**
     * @group ZF-8663
     */
    public function testBuiltinJSONEncoderWillProperlyEncodeSolidusInStringValues()
    {
        $source = "</foo><foo>bar</foo>";
        $target = '"<\\/foo><foo>bar<\\/foo>"';

        // first test ext/json
        $this->assertEquals($target, $this->_encoder->encode($source));
    }

    /**
     * @group ZF-8918
     */
    public function testDecodingInvalidJSONShouldRaiseAnException()
    {
        $this->setExpectedException('ehough_jameson_api_exception_RuntimeException');

        $this->_decoder->decode(' some string ');
    }

    /**
     * Encoding an iterator using the internal encoder should handle undefined keys
     *
     * @group ZF-9416
     */
    public function testIteratorWithoutDefinedKey()
    {
        $inputValue = new \ArrayIterator(array('foo'));
        $encoded = $this->_encoder->encode($inputValue);
        $expectedDecoding = '{"__className":"ArrayIterator",0:"foo"}';
        $this->assertEquals($expectedDecoding, $encoded);
    }

    /**
     * The default json decode type should be TYPE_OBJECT
     *
     * @group ZF-8618
     */
    public function testDefaultTypeObject()
    {
        $this->assertInstanceOf('stdClass', $this->_decoder->decode('{"var":"value"}'));
    }



    /**
     * @group ZF-11167
     */
    public function testEncodeWillUseToArrayMethodWhenAvailable()
    {
        $o = new ZF11167_ToArrayClass();

        $objJson = $this->_encoder->encode($o);
        $arrJson = $this->_encoder->encode($o->toArray());
        $this->assertSame($arrJson, $objJson);
    }

    /**
     * @group ZF-11167
     */
    public function testEncodeWillUseToJsonWhenBothToJsonAndToArrayMethodsAreAvailable()
    {
        $o = new ZF11167_ToArrayToJsonClass($this->_encoder);
        $objJson = $this->_encoder->encode($o);
        $this->assertEquals('"bogus"', $objJson);
        $arrJson = $this->_encoder->encode($o->toArray());
        $this->assertNotSame($objJson, $arrJson);
    }

    /**
     * @group ZF-9521
     */
    public function testWillEncodeArrayOfObjectsEachWithToJsonMethod()
    {
        $array = array('one'=>new ZendTest_JSON_ToJSONClass($this->_encoder));
        $expected = '{"one":{"__className":"ZendTest_JSON_ToJSONClass","firstName":"John","lastName":"Doe","email":"john@doe.com"}}';

        $json = $this->_encoder->encode($array);
        $this->assertEquals($expected, $json);
    }

    /**
     * @group ZF-7586
     */
    public function testWillDecodeStructureWithEmptyKeyToObjectProperly()
    {
        $json = '{"":"test"}';
        $object = $this->_decoder->decode($json);
        $this->assertTrue(isset($object->_empty_));
        $this->assertEquals('test', $object->_empty_);
    }

}

/**
 * Zend_JSONTest_Item: test item for use with testZf461()
 */
class ZendTest_JSON_Item
{
}

/**
 * Zend_JSONTest_Object: test class for encoding classes
 */
class ZendTest_JSON_Object
{
    const FOO = 'bar';

    public $foo = 'bar';
    public $bar = 'baz';

    protected $_foo = 'fooled you';

    public function foo($bar, $baz)
    {
    }

    public function bar($baz)
    {
    }

    protected function baz()
    {
    }
}

class ZendTest_JSON_ToJSONClass
{
    private $_firstName = 'John';

    private $_lastName = 'Doe';

    private $_email = 'john@doe.com';

    private $_encoder;

    public function __construct(ehough_jameson_api_IEncoder $encoder)
    {
        $this->_encoder = $encoder;
    }

    public function toJSON()
    {
        $data = array(
            'firstName' => $this->_firstName,
            'lastName'  => $this->_lastName,
            'email'     => $this->_email
        );

        return $this->_encoder->encode($data);
    }
}

/**
 * Serializable class exposing a toArray() method
 * @see ZF-11167
 */
class ZF11167_ToArrayClass
{
    private $_firstName = 'John';

    private $_lastName = 'Doe';

    private $_email = 'john@doe.com';

    public function toArray()
    {
        $data = array(
            'firstName' => $this->_firstName,
            'lastName'  => $this->_lastName,
            'email'     => $this->_email
        );
        return $data;
    }
}

/**
 * Serializable class exposing both toArray() and toJson() methods
 * @see ZF-11167
 */
class ZF11167_ToArrayToJsonClass extends ZF11167_ToArrayClass
{
    private $_encoder;

    public function __construct(ehough_jameson_api_IEncoder $encoder)
    {
        $this->_encoder = $encoder;
    }

    public function toJson()
    {
        return $this->_encoder->encode('bogus');
    }
}
