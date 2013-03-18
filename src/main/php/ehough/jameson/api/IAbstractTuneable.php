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
 * An item that has basic options functionality.
 *
 * @author Eric Hough <eric@ehough.com>
 *
 */
interface ehough_jameson_api_IAbstractTuneable
{
    /**
     * Set options for the encoder.
     *
     * @param array $options An associative array of option => value.
     *
     * @return void
     */
    function setOptions(array $options);

    /**
     * Retrieve the full set of options.
     *
     * @return array The full set of options.
     */
    function getOptions();

    /**
     * Get the value of a single option.
     *
     * @param string $name The name of the option to retrieve.
     *
     * @return mixed The value of the option, or null if no such option.
     */
    function getOption($name);
}