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
 * JSON decoder. This class is based heavily on Zend_Json's decoder.
 *
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc.
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class ehough_jameson_impl_AbstractDecoder
    extends ehough_jameson_impl_AbstractTuneable implements ehough_jameson_api_IDecoder
{
    /**
     * By default, decoded objects will be returned as associative arrays. Set this
     * option to true to return a stdClass object instead.
     */
    const OPTION_DECODE_TO_STDCLASS_INSTEAD_OF_ARRAYS = 'decodeToStdClassInsteadOfArrays';

    /**
     * Try to use PHP's native decoder when possible.
     */
    const OPTION_USE_NATIVE_DECODER = 'useNativeDecoder';

    /**
     * Get the map of default options.
     *
     * @return mixed Map of default options.
     */
    protected final function _getDefaultOptionMap()
    {
        return array(

                self::OPTION_USE_NATIVE_DECODER                   => true,
                self::OPTION_DECODE_TO_STDCLASS_INSTEAD_OF_ARRAYS => true,
               );
    }
}