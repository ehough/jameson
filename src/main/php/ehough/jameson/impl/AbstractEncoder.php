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
 *
 */
/**
 * JSON encoder. This class is based heavily on Zend_Json's encoder.
 *
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc.
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class ehough_jameson_impl_AbstractEncoder
    extends ehough_jameson_impl_AbstractTuneable implements ehough_jameson_api_IEncoder
{
    /** Whether or not to check for possible object recursion when encoding. */
    const OPTION_CYCLE_CHECK_ENABLED = 'cycleCheckEnabled';

    /** Whether or not to silence recursion errors. */
    const OPTION_SILENCE_CYCLICAL_ERRORS = 'silenceCyclicalErrors';

    /**
     * Try to use PHP's native encoder when possible.
     */
    const OPTION_USE_NATIVE_ENCODER = 'useNativeEncoder';

    /**
     * Get the map of default options.
     *
     * @return mixed Map of default options.
     */
    protected final function _getDefaultOptionMap()
    {
        return array(

                self::OPTION_CYCLE_CHECK_ENABLED     => true,
                self::OPTION_SILENCE_CYCLICAL_ERRORS => false,
                self::OPTION_USE_NATIVE_ENCODER      => true,
               );
    }
}