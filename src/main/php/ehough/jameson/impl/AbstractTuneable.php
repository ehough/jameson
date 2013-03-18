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
 * Base class for handling options.
 *
 */
abstract class ehough_jameson_impl_AbstractTuneable implements ehough_jameson_api_IAbstractTuneable
{
    /** Option map. */
    private $_options = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_options = $this->_getDefaultOptionMap();
    }

    /**
     * Set options for the encoder.
     *
     * @param array $options An associative array of option => value.
     *
     * @return void
     */
    public final function setOptions(array $options)
    {
        foreach ($options as $key => $value) {

            if (array_key_exists($key, $this->_options)) {

                $this->_options[$key] = $value;
            }
        }
    }

    /**
     * Get the value of a single option.
     *
     * @param string $name The name of the option to retrieve.
     *
     * @return mixed The value of the option, or null if no such option.
     */
    public final function getOption($name)
    {
        if (isset($this->_options[$name])) {

            return $this->_options[$name];
        }

        return null;
    }

    /**
     * Retrieve the full set of options.
     *
     * @return array The full set of options.
     */
    public final function getOptions()
    {
        return $this->_options;
    }

    /**
     * Get the map of default options.
     *
     * @return mixed Map of default options.
     */
    protected abstract function _getDefaultOptionMap();
}