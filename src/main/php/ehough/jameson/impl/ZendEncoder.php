<?php
/**
 * Copyright 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of jameson (https://github.com/ehough/jameson)
 *
 * jameson is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * jameson is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with jameson.  If not, see <http://www.gnu.org/licenses/>.
 *
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
 *
 */
/**
 * JSON encoder. This class is based heavily on Zend_Json's encoder.
 *
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc.
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
final class ehough_jameson_impl_ZendEncoder extends ehough_jameson_impl_AbstractEncoder implements ehough_jameson_api_IEncoder
{
    /**
     * Array of visited objects; used to prevent cycling.
     *
     * @var array
     */
    private $_visited = array();

    /**
     * Use the JSON encoding scheme for the value specified
     *
     * @param mixed $value The value to be encoded
     *
     * @return string The encoded value
     */
    public function encode($value)
    {
        $encoder = new self();

        $encoder->setOptions($this->getOptions());

        return $encoder->_encode($value);
    }

    /**
     * Recursive driver which determines the type of value to be encoded
     * and then dispatches to the appropriate method. $values are either
     *    - objects (returns from {@link _encodeObject()})
     *    - arrays (returns from {@link _encodeArray()})
     *    - basic datums (e.g. numbers or strings)
     *
     * @param $value mixed The value to be encoded
     *
     * @return string Encoded value
     */
    private function _encode($value)
    {
        if (is_object($value)) {

            return $this->_encodeObject($value);
        }

        if (is_array($value)) {

            return $this->_encodeArray($value);
        }

        return self::_encodeDatum($value);
    }

    /**
     * JSON encode an array value
     *
     * Recursively encodes each value of an array and returns a JSON encoded
     * array string.
     *
     * Arrays are defined as integer-indexed arrays starting at index 0, where
     * the last index is (count($array) -1); any deviation from that is
     * considered an associative array, and will be encoded as such.
     *
     * @param $array array
     * @return string
     */
    private function _encodeArray($array)
    {
        $tmpArray = array();

        // Check for associative array
        if (!empty($array) && (array_keys($array) !== range(0, count($array) - 1))) {

            // Associative array
            $result = '{';

            foreach ($array as $key => $value) {

                $key        = (string) $key;
                $tmpArray[] = self::_encodeString($key) . ':' . $this->_encode($value);
            }

            $result .= implode(',', $tmpArray);
            $result .= '}';

        } else {

            // Indexed array
            $result = '[';
            $length = count($array);

            for ($i = 0; $i < $length; $i++) {

                $tmpArray[] = $this->_encode($array[$i]);
            }

            $result .= implode(',', $tmpArray);
            $result .= ']';
        }

        return $result;
    }

    /**
     * JSON encode a basic data type (string, number, boolean, null)
     *
     * If value type is not a string, number, boolean, or null, the string
     * 'null' is returned.
     *
     * @param  mixed $value
     * @return string
     */
    private static function _encodeDatum($value)
    {
        $result = 'null';

        if (is_int($value) || is_float($value)) {

            $result = (string) $value;
            $result = str_replace(',', '.', $result);

        } elseif (is_string($value)) {

            $result = self::_encodeString($value);

        } elseif (is_bool($value)) {

            $result = $value ? 'true' : 'false';
        }

        return $result;
    }

    /**
     * Encode an object to JSON by encoding each of the public properties
     *
     * A special property is added to the JSON object called '__className'
     * that contains the name of the class of $value. This is used to decode
     * the object on the client into a specific class.
     *
     * @param $value object
     *
     * @return string
     *
     * @throws ehough_jameson_api_exception_RecursionException If recursive checks are enabled
     *                                                         and the object has been serialized previously
     */
    private function _encodeObject($value)
    {
        if ($this->getOption(ehough_jameson_api_IEncoder::OPTION_CYCLE_CHECK_ENABLED) === true) {

            if ($this->_wasVisited($value)) {

                if ($this->getOption(ehough_jameson_api_IEncoder::OPTION_SILENCE_CYCLICAL_ERRORS) === true) {

                    return '"* RECURSION (' . str_replace('\\', '\\\\', get_class($value)) . ') *"';

                } else {

                    throw new ehough_jameson_api_exception_RecursionException(
                        'Cycles not supported in JSON encoding, cycle introduced by '
                            . 'class "' . get_class($value) . '"'
                    );
                }
            }

            $this->_visited[] = $value;
        }

        $props = '';

        if (method_exists($value, 'toJson')) {

            /** @noinspection PhpUndefinedMethodInspection */
            $props =',' . preg_replace("/^\{(.*)\}$/","\\1",$value->toJson());

        } else {

            if ($value instanceof \Iterator) {

                $propCollection = $value;

            } else {

                $propCollection = get_object_vars($value);
            }

            foreach ($propCollection as $name => $propValue) {

                if (isset($propValue)) {

                    $props .= ','
                        . $this->_encode($name)
                        . ':'
                        . $this->_encode($propValue);
                }
            }
        }

        $className = get_class($value);

        return '{"__className":'
            . self::_encodeString($className)
            . $props . '}';
    }

    /**
     * JSON encode a string value by escaping characters as necessary
     *
     * @param string $string string string string string
     *
     * @return string
     */
    private static function _encodeString($string)
    {
        // Escape these characters with a backslash:
        // " \ / \n \r \t \b \f
        $search  = array('\\', "\n", "\t", "\r", "\b", "\f", '"', '/');
        $replace = array('\\\\', '\\n', '\\t', '\\r', '\\b', '\\f', '\"', '\\/');
        $string  = str_replace($search, $replace, $string);

        // Escape certain ASCII characters:
        // 0x08 => \b
        // 0x0c => \f
        $string = str_replace(array(chr(0x08), chr(0x0C)), array('\b', '\f'), $string);
        $string = self::_encodeUnicodeString($string);

        return '"' . $string . '"';
    }

    /**
     * Determine if an object has been serialized already
     *
     * @param mixed $value
     *
     * @return boolean
     */
    private function _wasVisited($value)
    {
        if (in_array($value, $this->_visited, true)) {

            return true;
        }

        return false;
    }

    /**
     * Encode Unicode Characters to \u0000 ASCII syntax.
     *
     * This algorithm was originally developed for the
     * Solar Framework by Paul M. Jones
     *
     * @link   http://solarphp.com/
     * @link   http://svn.solarphp.com/core/trunk/Solar/JSON.php
     * @param  string $value
     * @return string
     */
    private static function _encodeUnicodeString($value)
    {
        $strlen_var = strlen($value);
        $ascii = "";

        /**
         * Iterate over every character in the string,
         * escaping with a slash or encoding to UTF-8 where necessary
         */
        for($i = 0; $i < $strlen_var; $i++) {
            $ord_var_c = ord($value[$i]);

            switch (true) {
                case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
                    // characters U-00000000 - U-0000007F (same as ASCII)
                    $ascii .= $value[$i];
                    break;

                case (($ord_var_c & 0xE0) == 0xC0):
                    // characters U-00000080 - U-000007FF, mask 110XXXXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c, ord($value[$i + 1]));
                    $i += 1;
                    $utf16 = self::_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;

                case (($ord_var_c & 0xF0) == 0xE0):
                    // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                        ord($value[$i + 1]),
                        ord($value[$i + 2]));
                    $i += 2;
                    $utf16 = self::_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;

                case (($ord_var_c & 0xF8) == 0xF0):
                    // characters U-00010000 - U-001FFFFF, mask 11110XXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                        ord($value[$i + 1]),
                        ord($value[$i + 2]),
                        ord($value[$i + 3]));
                    $i += 3;
                    $utf16 = self::_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;

                case (($ord_var_c & 0xFC) == 0xF8):
                    // characters U-00200000 - U-03FFFFFF, mask 111110XX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                        ord($value[$i + 1]),
                        ord($value[$i + 2]),
                        ord($value[$i + 3]),
                        ord($value[$i + 4]));
                    $i += 4;
                    $utf16 = self::_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;

                case (($ord_var_c & 0xFE) == 0xFC):
                    // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                        ord($value[$i + 1]),
                        ord($value[$i + 2]),
                        ord($value[$i + 3]),
                        ord($value[$i + 4]),
                        ord($value[$i + 5]));
                    $i += 5;
                    $utf16 = self::_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;
            }
        }

        return $ascii;
    }

    /**
     * Convert a string from one UTF-8 char to one UTF-16 char.
     *
     * Normally should be handled by mb_convert_encoding, but
     * provides a slower PHP-only method for installations
     * that lack the multibye string extension.
     *
     * This method is from the Solar Framework by Paul M. Jones
     *
     * @link   http://solarphp.com
     * @param string $utf8 UTF-8 character
     * @return string UTF-16 character
     */
    private static function _utf82utf16($utf8)
    {
        // Check for mb extension otherwise do by hand.
        if( function_exists('mb_convert_encoding') ) {

            return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
        }

        switch (strlen($utf8)) {

            case 1:

                // this case should never be reached, because we are in ASCII range
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return $utf8;

            case 2:

                // return a UTF-16 character from a 2-byte UTF-8 char
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0x07 & (ord($utf8{0}) >> 2))
                    . chr((0xC0 & (ord($utf8{0}) << 6))
                        | (0x3F & ord($utf8{1})));

            case 3:

                // return a UTF-16 character from a 3-byte UTF-8 char
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr((0xF0 & (ord($utf8{0}) << 4))
                    | (0x0F & (ord($utf8{1}) >> 2)))
                    . chr((0xC0 & (ord($utf8{1}) << 6))
                        | (0x7F & ord($utf8{2})));
        }

        // ignoring UTF-32 for now, sorry
        return '';
    }
}