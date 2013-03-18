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
 * Fast JSON decoder. This class is based heavily on Zend_Json's JSON class.
 *
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc.
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
final class ehough_jameson_impl_FastDecoder
    extends ehough_jameson_impl_AbstractDecoder implements ehough_jameson_api_IDecoder
{
    /**
     * Decode a JSON source string.
     *
     * Decodes a JSON encoded string. The value returned will be one of the
     * following:
     *        - integer
     *        - float
     *        - boolean
     *        - null
     *      - StdClass
     *      - array
     *         - array of one or more of the above types
     *
     * @param string $encodedValue String to be decoded.
     *
     * @return mixed The decoded JSON.
     *
     * @throws ehough_jameson_api_exception_RuntimeException If there was a decode error.
     */
    public function decode($encodedValue)
    {
        $encodedValue = (string) $encodedValue;

        if (function_exists('json_decode')
            && $this->getOption(ehough_jameson_impl_AbstractDecoder::OPTION_USE_NATIVE_DECODER) === true
        ) {

            $decodeToStdClss
                = $this->getOption(ehough_jameson_impl_AbstractDecoder::OPTION_DECODE_TO_STDCLASS_INSTEAD_OF_ARRAYS);

            $decode = json_decode($encodedValue, $decodeToStdClss !== true);

            switch (json_last_error()) {

                case JSON_ERROR_NONE:

                    break;

                case JSON_ERROR_DEPTH:

                    throw new ehough_jameson_api_exception_RuntimeException(
                        'Decoding failed: Maximum stack depth exceeded'
                    );

                case JSON_ERROR_CTRL_CHAR:

                    throw new ehough_jameson_api_exception_RuntimeException(
                        'Decoding failed: Unexpected control character found'
                    );

                case JSON_ERROR_SYNTAX:

                    throw new ehough_jameson_api_exception_RuntimeException('Decoding failed: Syntax error');

                default:

                    throw new ehough_jameson_api_exception_RuntimeException('Decoding failed');
            }

            return $decode;
        }

        $decoder = new ehough_jameson_impl_ZendDecoder();

        $decoder->setOptions($this->getOptions());

        return $decoder->decode($encodedValue);
    }
}